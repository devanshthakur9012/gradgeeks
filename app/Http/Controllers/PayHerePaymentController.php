<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;
use App\Models\Utility;
use App\Models\Coupon;
use App\Models\UserCoupon;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Lahirulhr\PayHere\PayHere;

class PayHerePaymentController extends Controller
{

    public function setPaymentDetail_client($invoice_id)
    {
        $invoice = Invoice::find($invoice_id);
        if (Auth::user() != null) {
            $this->user = Auth::user();
        } else {
            $this->user = Client::where('id', $invoice->client_id)->first();
        }

        $payment_setting = Utility::getPaymentSetting($this->user->currentWorkspace->id);

        $this->currancy = (isset($this->user->currentWorkspace->currency_code)) ? $this->user->currentWorkspace->currency_code : 'USD';
        $this->payhere_mode = isset($payment_setting['payhere_mode']) ? $payment_setting['payhere_mode'] : '';
        $this->payhere_merchant_id = isset($payment_setting['payhere_merchant_id']) ? $payment_setting['payhere_merchant_id'] : '';
        $this->payhere_merchant_secret = isset($payment_setting['payhere_merchant_secret']) ? $payment_setting['payhere_merchant_secret'] : '';
        $this->payhere_app_id = isset($payment_setting['payhere_app_id']) ? $payment_setting['payhere_app_id'] : '';
        $this->payhere_app_secret = isset($payment_setting['payhere_app_secret']) ? $payment_setting['payhere_app_secret'] : '';


        $config = [
            'payhere.api_endpoint' => isset($payment_setting['payhere_mode']) && $payment_setting['payhere_mode'] === 'sandbox'
                ? 'https://sandbox.payhere.lk/'
                : 'https://www.payhere.lk/',
        ];

        $config['payhere.merchant_id'] = $payment_setting['payhere_merchant_id'] ?? '';
        $config['payhere.merchant_secret'] = $payment_setting['payhere_merchant_secret'] ?? '';
        $config['payhere.app_secret'] = $payment_setting['payhere_app_secret'] ?? '';
        $config['payhere.app_id'] = $payment_setting['payhere_app_id'] ?? '';
        config($config);
    }

    public function invoicePayWithPayhere(Request $request, $slug, $invoice_id)
    {
        $invoice = Invoice::find($invoice_id);
        if ($invoice) {
            try {

                $request->validate(['amount' => 'required|numeric|min:0']);
                $this->setPaymentDetail_client($invoice_id);
                $user_auth = Auth::user();
                $get_amount = $request->amount;
                $orderId = strtoupper(str_replace('.', '', uniqid('', true)));
                $client_keyword = isset($user_auth) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';
                $order_id = strtoupper(str_replace('.', '', uniqid('', true)));
                if (
                    $this->currancy != 'LKR' &&
                    $this->currancy != 'USD' &&
                    $this->currancy != 'GBP' &&
                    $this->currancy != 'EUR' &&
                    $this->currancy != 'AUD'
                ) {
                    return redirect()->back()->with('error', __('Availabe currencies: LKR, USD, GBP, EUR, AUD'));
                }
                $merchant_id = $this->payhere_merchant_id;
                $hash = strtoupper(
                    md5(
                        $merchant_id .
                        $order_id .
                        number_format($get_amount, 2, '.', '') .
                        $this->currancy .
                        strtoupper(md5(config('payhere.merchant_secret')))
                    )
                );

                $data = [
                    'first_name' => $this->user->name,
                    'last_name' => $this->user->name,
                    'email' => $this->user->email,
                    'address' => '',
                    'city' => '',
                    'country' => '',
                    'order_id' => $order_id,
                    'items' => 'Invoice Payment',
                    'currency' => $this->currancy,
                    'amount' => $get_amount,
                    'hash' => $hash,
                ];

                return PayHere::checkOut()
                    ->data($data)
                    ->successUrl(route($client_keyword . 'invoice.payhere.status', ['success' => 1, 'id' => $invoice->id, 'amt' => $get_amount, 'slug' => $slug]))
                    ->failUrl(route($client_keyword . 'invoice.payhere.status', ['success' => 0, 'id' => $invoice->id, 'slug' => $slug]))
                    ->renderView();
            } catch (\Exception $e) {
                return redirect()->back()->with('error', __($e));
            }
        } else {
            return redirect()->back()->with('error', 'Invoice Not Found !!');
        }
    }

    public function invoiceGetPayHereStatus(Request $request, $slug)
    {
        $invoiceId = $request->id;
        $orderId = $request->order_id;
        $this->setPaymentDetail_client($invoiceId);
        $amount = $request->amount;
        $user_auth = Auth::user();
        $client_keyword = isset($user_auth) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';

        $invoice = Invoice::find($invoiceId);
        if (!empty($invoice)) {
            $currentWorkspace = Utility::getWorkspaceBySlug($slug);
            $invoice_payment = new InvoicePayment();
            $invoice_payment->order_id = $orderId;
            $invoice_payment->invoice_id = $invoiceId;
            $invoice_payment->currency = isset($currentWorkspace->currency_code) ? $currentWorkspace->currency_code : 'USD';
            $invoice_payment->amount = $amount;
            $invoice_payment->payment_type = 'Payhere';
            $invoice_payment->receipt = '';
            $invoice_payment->client_id = $this->user->id;
            $invoice_payment->txn_id = '';
            $invoice_payment->payment_status = 'approved';
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
                'project_name' => $project_name->name,
                'company_name' => User::find($project_name->created_by)->name,
                'invoice_id' => Utility::invoiceNumberFormat($invoice->id),
                'client_name' => $client->name,
                'total_amount' => $total_amount,
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
            // $webhook=  Utility::webhookSetting($module);
            if ($webhook) {
                $parameter = json_encode($invoice);
                // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
                $status = Utility::WebhookCall($webhook['url'], $parameter, $webhook['method']);
                // if($status == true)
                // {
                //     return redirect()->back()->with('success', __('Payment added Successfully!'));
                // }
                // else
                // {
                //     return redirect()->back()->with('error', __('Webhook call failed.'));
                // }
            }

            if (Auth::check()) {
                return redirect()->route(
                    $client_keyword . 'invoices.show',
                    [
                        $slug,
                        $invoice->id,
                    ]
                )->with('success', __('Payment added Successfully'));
            } else {
                return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)])->with('success', __('Payment added Successfully'));
            }
        } else {
            if (Auth::check()) {
                return redirect()->route(
                    $client_keyword . 'invoices.show',
                    [
                        $slug,
                        $invoiceId,
                    ]
                )->with('error', __('Invoice not found.'));
            } else {
                return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoiceId)])->with('error', __('Invoice not found!'));
            }
        }
    }
}
