<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Order;
use App\Models\Payment;
use Artisan;
use App\Models\Plan;
use App\Models\UserCoupon;
use App\Models\Utility;
use CoinGate\CoinGate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Client;
use App\Models\Project;

class CoingatePaymentController extends Controller
{
    public $mode;
    public $coingate_auth_token;
    public $is_enabled;
    public $currancy;
    public $user;

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
        $this->coingate_auth_token = isset($payment_setting['coingate_auth_token']) ? $payment_setting['coingate_auth_token'] : '';
        $this->mode = isset($payment_setting['coingate_mode']) ? $payment_setting['coingate_mode'] : 'off';
        $this->is_enabled = isset($payment_setting['is_coingate_enabled']) ? $payment_setting['is_coingate_enabled'] : 'off';
    }

    public function invoicePayWithCoingate(Request $request, $slug, $invoice_id)
    {
        $user_auth = Auth::user();
        $client_keyword = isset($user_auth) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';
        $validatorArray = [
            'amount' => 'required',
        ];
        $validator = Validator::make(
            $request->all(),
            $validatorArray
        );

        if ($validator->fails()) {
            return redirect()->back()->with('error', __($validator->errors()->first()));
        }

        // $this->setPaymentDetail();
        $this->setPaymentDetail_client($invoice_id);

        $invoice = Invoice::find($invoice_id);
        if ($invoice->getDueAmount() < $request->amount) {
            return redirect()->route(
                $client_keyword . 'invoices.show',
                [
                    $slug,
                    $invoice_id,
                ]
            )->with('error', __('Invalid amount.'));
        }
        \App\Coingate\Coingate::config(
            array(
                'environment' => $this->mode,
                'auth_token' => $this->coingate_auth_token,
                'curlopt_ssl_verifypeer' => FALSE,
            )
        );
        $post_params = array(
            'order_id' => time(),
            'price_amount' => $request->amount,
            'price_currency' => $this->currancy,
            'receive_currency' => $this->currancy,
            'callback_url' => route(
                $client_keyword . 'invoice.coingate',
                [
                    $slug,
                    encrypt($invoice->id),
                ]
            ),
            'cancel_url' => route(
                $client_keyword . 'invoice.coingate',
                [
                    $slug,
                    encrypt($invoice->id),
                ]
            ),
            'success_url' => route(
                $client_keyword . 'invoice.coingate',
                [
                    $slug,
                    encrypt($invoice->id),
                    'success=true',
                ]
            ),
            'title' => 'Plan #' . time(),
        );
        $order = \App\Coingate\Coingate::coingatePayment($post_params, 'POST');
        if ($order['status_code'] === 200) {
            $request->session()->put('invoice_data', $post_params);
            $response = $order['response'];
            return redirect($response['payment_url']);
        } else {
            return redirect()->back()->with('error', __('Something went wrong.'));
        }
    }

    public function getInvoicePaymentStatus($slug, $invoice_id, Request $request)
    {
        // $this->setPaymentDetail();
        if (!empty($invoice_id)) {
            $invoice_id = decrypt($invoice_id);
            $this->setPaymentDetail_client($invoice_id);
            $user = Auth::user();
            // $currentWorkspace = Utility::getWorkspaceBySlug($slug);
            $currentWorkspace = $user ? Utility::getWorkspaceBySlug($slug) : Utility::getWorkspaceBySlug_copylink('invoice', $invoice_id);
            $invoice = Invoice::find($invoice_id);
            $invoice_data = $request->session()->get('invoice_data');

            if ($invoice && !empty($invoice_data)) {
                try {
                    if ($request->has('success') && $request->success == 'true') {
                        $order_id = strtoupper(str_replace('.', '', uniqid('', true)));

                        $invoice_payment = new InvoicePayment();
                        $invoice_payment->order_id = $order_id;
                        $invoice_payment->invoice_id = $invoice->id;
                        $invoice_payment->currency = $currentWorkspace->currency_code;
                        $invoice_payment->amount = isset($invoice_data['price_amount']) ? $invoice_data['price_amount'] : 0;
                        $invoice_payment->payment_type = 'Coingate';
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

                        $request->session()->forget('invoice_data');

                        //return redirect()->back()->with('success', __('Payment added Successfully'));

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
                        if (\Auth::check()) {
                            return redirect()->route(
                                'client.invoices.show',
                                [
                                    $slug,
                                    $invoice_id,
                                ]
                            )->with('error', __('Transaction fail'));
                        } else {
                            return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)])->with('error', __('Transaction fail'));
                        }

                    }
                } catch (\Exception $e) {
                    return redirect()->route('client.invoices.index', $slug)->with('error', __('Invoice not found!'));
                }
            } else {
                if (\Auth::check()) {
                    return redirect()->route(
                        'client.invoices.show',
                        [
                            $slug,
                            $invoice_id,
                        ]
                    )->with('error', __('Invoice not found.'));
                } else {
                    return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)])->with('success', __('Invoice not found'));
                }
            }
        } else {
            return redirect()->route('client.invoices.index', $slug)->with('error', __('Invoice not found.'));
        }
    }
}
