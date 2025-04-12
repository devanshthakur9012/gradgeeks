<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utility;
use Paytabscom\Laravel_paytabs\Facades\paypage;
use App\Models\Plan;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\PlanOrder;
use App\Models\UserCoupon;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariantOption;
use App\Models\PurchasedProducts;
use App\Models\ProductCoupon;
use App\Models\Client;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Exception;


class PaytabController extends Controller
{
    public $paytab_profile_id, $paytab_server_key, $paytab_region, $is_enabled;

    public function setPaymentDetail_client($invoice_id)
    {

        $invoice = Invoice::find($invoice_id);

        if (Auth::user() != null) {
            $this->user = Auth::user();
        } else {
            $this->user = Client::where('id', $invoice->client_id)->first();
        }

        $payment_setting = Utility::getPaymentSetting($this->user->currentWorkspace->id);

        config([
            'paytabs.profile_id' => isset($payment_setting['paytabs_profile_id']) ? $payment_setting['paytabs_profile_id'] : '',
            'paytabs.server_key' => isset($payment_setting['paytab_server_key']) ? $payment_setting['paytab_server_key'] : '',
            'paytabs.region' => isset($payment_setting['paytabs_region']) ? $payment_setting['paytabs_region'] : '',
            'paytabs.currency' => !empty($this->user->currentWorkspace->currency_code) ? $this->user->currentWorkspace->currency_code : 'USD',
        ]);

    }

    public function invoicePayWithpaytab(Request $request, $slug, $invoice_id)
    {
        $invoice = Invoice::find($invoice_id);
        $this->setPaymentDetail_client($invoice_id);
        $currentWorkspace = Utility::getWorkspaceBySlug_copylink('invoice', $invoice_id);
        $user_auth = Auth::user();
        $client_keyword = isset($user_auth) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';

        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $user = Client::where('id', $invoice->client_id)->first();
        }

        $get_amount = $request->amount;
        if ($invoice && $get_amount != 0) {
            if ($get_amount > $invoice->getDueAmount()) {
                return redirect()->back()->with('error', __('Invalid amount.'));
            } else {
                if ($currentWorkspace->currency_code == 'INR') {
                    // $pay = paypage::sendPaymentCode('all')
                    $paypage = new \App\PayTab\paypage();
                    $pay = $paypage->sendPaymentCode('all')
                        ->sendTransaction('sale')
                        ->sendCart(1, $get_amount, 'invoice payment')
                        ->sendCustomerDetails(isset($user->name) ? $user->name : "", isset($user->email) ? $user->email : '', '', '', '', '', '', '', '')
                        ->sendURLs(
                            route($client_keyword . 'invoice.paytab.success', ['success' => 1, 'data' => $request->all(), $slug, 'amount' => $get_amount, 'slug' => $currentWorkspace->slug, 'invoice_id' => $invoice_id]),
                            route($client_keyword . 'invoice.paytab.success', ['success' => 0, 'data' => $request->all(), $slug, 'amount' => $get_amount, 'slug' => $currentWorkspace->slug, 'invoice_id' => $invoice_id])
                        )
                        ->sendLanguage('en')
                        ->sendFramed($on = false)
                        ->create_pay_page();
                    return $pay;
                } else {
                    return redirect()->back()->with('error', __('Currency Not Supported. Contact To Your Site Admin'));
                }
            }
        }
    }

    public function getInvoicePaymentStatus(Request $request, $slug)
    {
        $invoice_id = $request->invoice_id;
        $currentWorkspace = Utility::getWorkspaceBySlug_copylink('invoice', $invoice_id);
        $user_auth = Auth::user();
        $client_keyword = isset($user_auth) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';


        if (!empty($invoice_id)) {
            $invoice = Invoice::find($invoice_id);
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

            if (Auth::check()) {
                $user = Auth::user();
            } else {
                $user = Client::where('id', $invoice->client_id)->first();
            }
            if ($invoice) {
                try {
                    if ($request->success == "1") {

                        $invoice_payment = new InvoicePayment();
                        $invoice_payment->order_id = $orderID;
                        $invoice_payment->invoice_id = $invoice->id;
                        $invoice_payment->currency = $currentWorkspace->currency_code;
                        $invoice_payment->amount = isset($request->amount) ? $request->amount : 0;
                        $invoice_payment->payment_type = 'paytab';
                        $invoice_payment->receipt = '';
                        $invoice_payment->client_id = $user->id;
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
                            if ($status != true) {
                                $msg = "Webhook call failed.";
                            }
                        }
                        if (\Auth::check()) {
                            return redirect()->route(
                                'client.invoices.show',
                                [
                                    $slug,
                                    $invoice_id,
                                ]
                            )->with('success', __('Invoice paid Successfully!'));
                        } else {
                            return redirect()->route('pay.invoice', [$slug, Crypt::encrypt($invoice->id)])->with('success', __('Payment added Successfully'));
                        }

                    } else {
                        if (\Auth::check()) {
                            return redirect()->route(
                                'client.invoices.show',
                                [
                                    $slug,
                                    $invoice_id,
                                ]
                            )->with('error', __('Transaction fail!'));
                        } else {
                            return redirect()->route('pay.invoice', [$slug, Crypt::encrypt($invoice->id)])->with('error', __('Transaction fail!'));

                        }
                    }
                } catch (Exception $e) {
                    return redirect()->route(
                        'client.invoices.show',
                        [
                            $slug,
                            $invoice_id,
                        ]
                    )->with('error', __($e->getMessage()));
                }

            } else {
                if (Auth::user()) {
                    return redirect()->route('invoices.show', $invoice_id)->with('error', __('Invoice not found'));
                } else {
                    $id = \Crypt::encrypt($invoice_id);

                    return redirect()->route('pay.invoice', $id)->with('error', __('Transaction fail!'));
                }
            }
        } else {
            return redirect()->route(
                'client.invoices.show',
                [
                    $slug,
                    $invoice_id,
                ]
            )->with('error', __('Invoice not found!'));
        }
    }

}
