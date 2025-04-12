<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utility;
use App\Models\Plan;
use App\Models\Coupon;
use App\Models\UserCoupon;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\InvoicePayment;
use App\Models\Project;
use App\Models\User;

class NepalsteController extends Controller
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
        $this->nepalste_mode = isset($payment_setting['nepalste_mode']) ? $payment_setting['nepalste_mode'] : '';
        $this->api_key = isset($payment_setting['nepalste_public_key']) ? $payment_setting['nepalste_public_key'] : '';
    }


    public function invoicePayWithNepalste(Request $request, $slug, $invoice_id)
    {
        $invoice = Invoice::find($invoice_id);
        try {
            if ($invoice) {
                $request->validate(['amount' => 'required|numeric|min:0']);
                $this->setPaymentDetail_client($invoice_id);
                $user_auth = Auth::user();
                $get_amount = $request->amount;

                $orderId = strtoupper(str_replace('.', '', uniqid('', true)));
                $client_keyword = isset($user_auth) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';
                $paiementproMerchantId = $this->api_key;

                $response = ['amount' => $get_amount, 'invoice_id' => $invoice->id, 'slug' => $slug, 'orderId' => $orderId];
                $parameters = [
                    'identifier' => 'DFU80XZIKS',
                    'currency' => $this->currancy ?? 'NPR',
                    'amount' => $get_amount,
                    'details' => $invoice->id,
                    'ipn_url' => route($client_keyword . 'invoice.nepalste.status', $response),
                    'cancel_url' => route($client_keyword . 'invoice.nepalste.cancel', $response),
                    'success_url' => route($client_keyword . 'invoice.nepalste.status', $response),
                    'public_key' => $this->api_key,
                    'site_logo' => 'https://nepalste.com.np/assets/images/logoIcon/logo.png',
                    'checkout_theme' => 'dark',
                    'customer_name' => $this->user->name,
                    'customer_email' => $this->user->email,
                ];


                //live end point
                $liveUrl = "https://nepalste.com.np/payment/initiate";
                //test end point
                $sandboxUrl = "https://nepalste.com.np/sandbox/payment/initiate";

                $url = $this->nepalste_mode == 'live' ? $liveUrl : $sandboxUrl;


                $ch = curl_init();
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $parameters);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                curl_close($ch);

                $result = json_decode($result, true);

                if (isset($result['success'])) {
                    return redirect($result['url']);
                } else {
                    return redirect()->back()->with('error', __($result['message']));
                }
            } else {
                return redirect()->back()->with('error', 'Invoice Not Found !!');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __($e->getMessage()));
        }
    }

    public function getInvoicePaymentStatus(Request $request, $slug)
    {
        $invoiceId = $request->invoice_id;
        $orderId = $request->orderId;
        $this->setPaymentDetail_client($invoiceId);
        $amount = $request->amount;
        $user_auth = Auth::user();
        $client_keyword = isset($user_auth) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';
        if (!empty($invoiceId)) {
            $invoice = Invoice::find($invoiceId);
            if (!empty($invoice)) {
                $currentWorkspace = Utility::getWorkspaceBySlug($slug);
                $invoice_payment = new InvoicePayment();
                $invoice_payment->order_id = $orderId;
                $invoice_payment->invoice_id = $invoiceId;
                $invoice_payment->currency = isset($currentWorkspace->currency_code) ? $currentWorkspace->currency_code : 'USD';
                $invoice_payment->amount = $amount;
                $invoice_payment->payment_type = 'Nepalste';
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
    public function invoiceGetNepalsteCancel(Request $request, $slug)
    {
        $user_auth = Auth::user();
        $client_keyword = isset($user_auth) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';
        if (Auth::check()) {
            return redirect()->route(
                $client_keyword . 'invoices.show',
                [
                    $slug,
                    $request->invoice_id,
                ]
            )->with('error', __('Transaction Has Been Failed.'));
        } else {
            return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($request->invoice_id)])->with('error', __('Transaction Has Been Failed.'));
        }
        return redirect()->back()->with('error', __('Transaction has failed'));
    }
}
