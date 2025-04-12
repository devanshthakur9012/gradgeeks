<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use YooKassa\Client;
use App\Models\Client as AppClient;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\Utility;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\InvoicePayment;
use App\Models\UserCoupon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class YooKassaController extends Controller
{
    public function setPaymentDetail_client($invoice_id)
    {
        $invoice = Invoice::find($invoice_id);
        if (Auth::user() != null) {
            $this->user = Auth::user();
        } else {
            $this->user = AppClient::where('id', $invoice->client_id)->first();
        }

        $payment_setting = Utility::getPaymentSetting($this->user->currentWorkspace->id);
        $this->currancy = (isset($this->user->currentWorkspace->currency_code)) ? $this->user->currentWorkspace->currency_code : 'USD';
        $this->yookassa_shopid = isset($payment_setting['yookassa_shopid']) ? $payment_setting['yookassa_shopid'] : '';
        $this->yookassa_secret_key = isset($payment_setting['yookassa_secret_key']) ? $payment_setting['yookassa_secret_key'] : '';
    }
    public function invoicePayWithYookassa(Request $request, $slug, $invoice_id)
    {
        $this->setPaymentDetail_client($invoice_id);
        $user_auth = Auth::user();
        $get_amount = $request->amount;
        // $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
        $invoice = Invoice::find($invoice_id);
        $client_keyword = isset($user_auth) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';


        if ($this->currancy == 'RUB') {
            if ($invoice) {
                if (is_int((int) $this->yookassa_shopid)) {
                    $client = new Client();
                    $client->setAuth((int) $this->yookassa_shopid, $this->yookassa_secret_key);
                    $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                    Session::put('orderID', $orderID);
                    $payment = $client->createPayment(
                        array(
                            'amount' => array(
                                'value' => $get_amount,
                                'currency' => $this->currancy,
                            ),
                            'confirmation' => array(
                                'type' => 'redirect',
                                'return_url' => route($client_keyword . 'invoice.yookassa.status', [
                                    'slug' => $slug,
                                    'invoice_id' => $invoice->id,
                                    'amount' => $get_amount
                                ])
                            ),
                            'capture' => true,
                            'description' => 'Заказ №1',
                        ),
                        uniqid('', true)
                    );
                    Session::put('invoice_payment_id', $payment['id']);

                    if ($payment['confirmation']['confirmation_url'] != null) {
                        return redirect($payment['confirmation']['confirmation_url']);
                    } else {
                        return redirect()->route('plans.index')->with('error', 'Something went wrong, Please try again');
                    }
                } else {
                    return redirect()->back()->with('error', 'Please Enter  Valid Shop Id Key');
                }
            } else {
                return redirect()->back()->with('error', 'Invoice not found.');
            }
        } else {
            return redirect()->back()->with('error', 'Currency Is Not Supported...');
        }
    }

    public function getInvoicePaymentStatus(Request $request, $slug)
    {
        $invoiceId = $request->invoice_id;
        $this->setPaymentDetail_client($invoiceId);
        $user_auth = Auth::user();
        $invoice = Invoice::find($request->invoice_id);
        $amount = $request->amount;
        $client_keyword = isset($user_auth) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';

        if ($invoice) {
            if (is_int((int) $this->yookassa_shopid)) {
                $client = new Client();
                $client->setAuth((int) $this->yookassa_shopid, $this->yookassa_secret_key);
                $paymentId = Session::get('invoice_payment_id');


                if ($paymentId == null) {
                    return redirect()->back()->with('error', __('Transaction Unsuccesfull'));
                }
                $payment = $client->getPaymentInfo($paymentId);
                Session::forget('invoice_payment_id');

                if (isset($payment) && $payment->status == "succeeded") {
                    try {
                        $orderId = Session::get('orderID');
                        Session::forget('orderID');

                        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
                        $invoice_payment = new InvoicePayment();
                        $invoice_payment->order_id = $orderId;
                        $invoice_payment->invoice_id = $invoiceId;
                        $invoice_payment->currency = isset($currentWorkspace->currency_code) ? $currentWorkspace->currency_code : 'USD';
                        $invoice_payment->amount = $amount;
                        $invoice_payment->payment_type = 'Yookassa';
                        $invoice_payment->receipt = '';
                        $invoice_payment->client_id = $this->user->id;
                        $invoice_payment->txn_id = '';
                        $invoice_payment->payment_status = 'approved';
                        $invoice_payment->save();

                        if (($invoice->getDueAmount() - $invoice_payment->amount) == 0) {
                            $invoice->status = 2;
                            $invoice->save();
                        } else {
                            $invoice->status = 3;
                            $invoice->save();
                        }

                        $user1 = $currentWorkspace->id;
                        $settings = Utility::getPaymentSetting($user1);
                        $total_amount = $invoice->getDueAmounts($invoice->id);
                        $client = AppClient::find($invoice->client_id);
                        $project_name = Project::where('id', $invoice->project_id)->first();

                        $uArr = [
                            // 'user_name' => $user->name,
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
                            )->with('success', __('Payment added Successfully..'));
                        } else {
                            return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)])->with('success', __('Payment added Successfully...'));
                        }
                    } catch (\Exception $e) {
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
                    }
                } else {
                    return redirect()->back()->with('error', 'Please Enter  Valid Shop Id Key');
                }
            }
        } else {
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
        }
    }
}
