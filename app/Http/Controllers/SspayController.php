<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Plan;
use App\Models\UserCoupon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\Models\Utility;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use App\Models\InvoicePayment;


class SspayController extends Controller
{
    public $secretKey, $callBackUrl, $returnUrl, $categoryCode, $is_enabled, $invoiceData, $user;

    public function setPaymentDetail_client($invoice_id)
    {

        $invoice = Invoice::find($invoice_id);

        if (Auth::user() != null) {
            $this->user = Auth::user();
        } else {
            $this->user = Client::where('id', $invoice->client_id)->first();
        }

        $payment_setting = Utility::getPaymentSetting($this->user->currentWorkspace->id);

        $this->secretKey = isset($payment_setting['sspay_secret_key']) ? $payment_setting['sspay_secret_key'] : '';
        $this->categoryCode = isset($payment_setting['sspay_category_code']) ? $payment_setting['sspay_category_code'] : '';
        $this->is_enabled = isset($payment_setting['is_sspay_enabled']) ? $payment_setting['is_sspay_enabled'] : 'off';

    }

    public function invoicepaywithsspay(Request $request, $slug, $invoice_id)
    {

        $this->setPaymentDetail_client($invoice_id);

        $invoice = Invoice::find($invoice_id);
        $get_amount = $request->amount;
        $user1 = Auth::user();
        $client_keyword = isset($user1) ? (($user1->getGuard() == 'client') ? 'client.' : '') : '';

        if ($invoice) {
            if ($get_amount > $invoice->getDueAmount()) {
                return redirect()->back()->with('error', __('Invalid amount.'));
            } else {

                try {

                    $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                    $this->callBackUrl = route($client_keyword . 'invoice.sspay', [$slug, $invoice->id, $get_amount]);

                    $this->returnUrl = route($client_keyword . 'invoice.sspay', [$slug, $invoice->id, $get_amount]);

                    $Date = date('d-m-Y');
                    $ammount = $get_amount;
                    $billExpiryDays = 3;
                    $billExpiryDate = date('d-m-Y', strtotime($Date . ' + 3 days'));
                    $billContentEmail = "Invoice is successfully paid!";

                    $some_data = array(
                        'userSecretKey' => $this->secretKey,
                        'categoryCode' => $this->categoryCode,
                        'billName' => 'Invoice',
                        'billDescription' => 'Invoice Payment',
                        'billPriceSetting' => 1,
                        'billPayorInfo' => 1,
                        'billAmount' => 100 * $ammount,
                        'billReturnUrl' => $this->returnUrl,
                        'billCallbackUrl' => $this->callBackUrl,
                        'billExternalReferenceNo' => 'AFR341DFI',
                        'billTo' => $this->user->name,
                        'billEmail' => $this->user->email,
                        'billPhone' => '000000000',
                        'billSplitPayment' => 0,
                        'billSplitPaymentArgs' => '',
                        'billPaymentChannel' => '0',
                        'billContentEmail' => $billContentEmail,
                        'billChargeToCustomer' => 1,
                        'billExpiryDate' => $billExpiryDate,
                        'billExpiryDays' => $billExpiryDays
                    );
                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_POST, 1);
                    curl_setopt($curl, CURLOPT_URL, 'https://sspay.my/index.php/api/createBill');
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $some_data);
                    $result = curl_exec($curl);
                    $info = curl_getinfo($curl);
                    curl_close($curl);
                    $obj = json_decode($result);
                    return redirect('https://sspay.my/' . $obj[0]->BillCode);

                } catch (Exception $e) {
                    return redirect()->back()->with('error', __($e->getMessage()));
                }
            }
            return redirect()->back()->with('error', __('Unknown error occurred.'));
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

    }

    public function getInvoicePaymentStatus(Request $request, $slug, $invoice_id, $amount)
    {
        $this->setPaymentDetail_client($invoice_id);

        $user = Auth::user();
        // $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $currentWorkspace = Utility::getWorkspaceBySlug_copylink('invoice', $invoice_id);


        if ($request->status_id == 3) {
            if (\Auth::check()) {
                return redirect()->route(
                    'client.invoices.show',
                    [
                        $slug,
                        $invoice_id,
                    ]
                )->with('error', __('Your Transaction is failed, please try again!'));
            } else {
                return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice_id)])->with('error', __('Your Transaction is failed, please try again'));
            }
        } else if ($request->status_id == 2) {

            if (\Auth::check()) {
                return redirect()->route(
                    'client.invoices.show',
                    [
                        $slug,
                        $invoice_id,
                    ]
                )->with('error', __('Your transaction is pending!'));
            } else {
                return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice_id)])->with('error', __('Your transaction is pending'));
            }

        } else if ($request->status_id == 1) {

            $invoice = Invoice::find($invoice_id);
            if ($invoice) {
                $order_id = strtoupper(str_replace('.', '', uniqid('', true)));

                $invoice_payment = new InvoicePayment();
                $invoice_payment->order_id = $request->transaction_id;
                $invoice_payment->invoice_id = $invoice->id;
                $invoice_payment->currency = $currentWorkspace->currency_code;
                $invoice_payment->amount = isset($amount) ? $amount : 0;
                $invoice_payment->payment_type = 'sspay';
                $invoice_payment->receipt = '';
                $invoice_payment->client_id = $this->user->id;
                $invoice_payment->txn_id = '';
                $invoice_payment->payment_status = 'succeeded';
                $invoice_payment->save();


                if (($invoice->getDueAmount() - $invoice_payment->amount) == 0) {
                    $invoice->status = 'paid';
                    $invoice->save();
                } else {

                    $invoice->status = 'partialy paid';
                    $invoice->save();
                }

                $user1 = $currentWorkspace->id;
                $settings = Utility::getPaymentSetting($user1);
                $total_amount = $invoice->getDueAmounts($invoice->id);
                $client = Client::find($invoice->client_id);
                $project_name = Project::where('id', $invoice->project_id)->first();


                $uArr = [
                    // 'user_name' => $user->name,
                    'project_name' => $project_name->name,
                    'company_name' => User::find($project_name->created_by)->name,
                    'invoice_id' => Utility::invoiceNumberFormat($invoice->id),
                    'client_name' => $client->name,
                    'total_amount' => "$total_amount",
                    'paid_amount' => $request->amount,
                ];

                if (isset($settings['invoicest_notificaation']) && $settings['invoicest_notificaation'] == 1) {

                    Utility::send_slack_msg('Invoice Status Updated', $user1, $uArr);
                }

                if (isset($settings['telegram_invoicest_notificaation']) && $settings['telegram_invoicest_notificaation'] == 1) {
                    Utility::send_telegram_msg('Invoice Status Updated', $uArr, $user1);
                }

                //webhook
                $module = 'Invoice Status Updated';
                $webhook = Utility::webhookSetting($module, $user1);
                if ($webhook) {
                    $parameter = json_encode($invoice);
                    // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
                    $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
                }

                return redirect()->back()->with('success', __('Payment added Successfully'));

                if (\Auth::check()) {
                    return redirect()->route(
                        'client.invoices.show',
                        [
                            $slug,
                            $invoice_id,
                        ]
                    )->with('success', __('Invoice paid Successfully!'));
                } else {
                    return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)])->with('success', __('Payment added Successfully'));
                }
            } else {
                return redirect()->route(
                    'client.invoices.show',
                    [
                        $slug,
                        $invoice_id,
                    ]
                )->with('error', __('Invoice not found.'));
            }
        }

    }
}
