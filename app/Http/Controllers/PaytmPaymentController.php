<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Order;
use App\Models\Plan;
use App\Models\UserCoupon;
use App\Models\Utility;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Anand\LaravelPaytmWallet\Facades\PaytmWallet;
use Session;
use App\Models\Client;
use App\Models\Project;



class PaytmPaymentController extends Controller
{
    public $currancy, $user;

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

        config(
            [
                'services.paytm-wallet.env' => isset($payment_setting['paytm_mode']) ? $payment_setting['paytm_mode'] : '',
                'services.paytm-wallet.merchant_id' => isset($payment_setting['paytm_merchant_id']) ? $payment_setting['paytm_merchant_id'] : '',
                'services.paytm-wallet.merchant_key' => isset($payment_setting['paytm_merchant_key']) ? $payment_setting['paytm_merchant_key'] : '',
                'services.paytm-wallet.merchant_website' => 'WEBSTAGING',
                'services.paytm-wallet.channel' => 'WEB',
                'services.paytm-wallet.industry_type' => isset($payment_setting['paytm_industry_type']) ? $payment_setting['paytm_industry_type'] : '',
            ]
        );

    }


    public function invoicePayWithPaytm(Request $request, $slug, $invoice_id)
    {
        // $this->setPaymentDetail();
        $this->setPaymentDetail_client($invoice_id);
        $user_auth = Auth::user();
        $client_keyword = isset($user_auth) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';
        $validatorArray = [
            'amount' => 'required',
            'mobile' => 'required',
        ];

        $validator = Validator::make(
            $request->all(),
            $validatorArray
        )->setAttributeNames(
                [
                    'mobile' => 'Mobile No.',
                ]
            );

        if ($validator->fails()) {
            return redirect()->back()->with('error', __($validator->errors()->first()));
        }

        // $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $invoice = Invoice::find($request->invoice_id);


        if ($invoice->getDueAmount() < $request->amount) {
            return redirect()->route(
                $client_keyword . 'invoices.show',
                [
                    $slug,
                    $invoice_id,
                ]
            )->with('error', __('Invalid amount.'));
        }


        $call_back = route(
            $client_keyword . 'invoice.paytm',
            [
                $slug,
                encrypt($invoice->id),

            ]
        );

        $payment = PaytmWallet::with('receive');

        $payment->prepare(
            [
                'order' => date('Y-m-d') . '-' . strtotime(date('Y-m-d H:i:s')),
                'user' => $this->user->id,
                'mobile_number' => $request->mobile,
                'email' => $this->user->email,
                'amount' => $request->amount,
                'invoice_id' => $invoice->id,
                'callback_url' => route($client_keyword . 'invoice.paytm', '_token=' . Session::token() . '&slug=' . $slug . '&invoice_id=' . encrypt($invoice->id)),
            ]
        );

        return $payment->receive();
    }

    public function getInvoicePaymentStatus(Request $request)
    {
        $invoice_id = $request->invoice_id;
        $invoice_id = decrypt($invoice_id);
        $this->setPaymentDetail_client($invoice_id);
        $slug = $request->slug;
        if (!empty($invoice_id)) {
            $user = Auth::user();
            $currentWorkspace = $user ? Utility::getWorkspaceBySlug($slug) : Utility::getWorkspaceBySlug_copylink('invoice', $invoice_id);


            $invoice = Invoice::find($invoice_id);
            if ($invoice) {
                // try
                // {
                $transaction = PaytmWallet::with('receive');
                // $response    = $transaction->response();
                // if($transaction->isSuccessful())
                // {
                $order_id = strtoupper(str_replace('.', '', uniqid('', true)));

                $invoice_payment = new InvoicePayment();
                $invoice_payment->order_id = $order_id;
                $invoice_payment->invoice_id = $invoice->id;
                $invoice_payment->currency = $currentWorkspace->currency_code;
                $invoice_payment->amount = isset($request->TXNAMOUNT) ? $request->TXNAMOUNT : 0;
                $invoice_payment->payment_type = 'Paytm';
                $invoice_payment->receipt = '';
                $invoice_payment->client_id = $invoice->client_id;
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
                // $webhook=  Utility::webhookSetting($module);
                $webhook = Utility::webhookSetting($module, $user1);

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
                    return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)])->with('success', __('Invoice paid Successfully'));
                }
                // }
                // else
                // {
                //     return redirect()->route(
                //         'client.invoices.show', [
                //                                   $slug,
                //                                   $invoice_id,
                //                               ]
                //     )->with('error', __('Transaction fail'));
                // }
                // }
                // catch(\Exception $e)
                // {

                //     return redirect()->route(
                //         'client.invoices.show', [
                //                                   $slug,
                //                                   $invoice_id,
                //                               ]
                //     )->with('error', __('Something went wrong.'));
                // }
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
                    return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice_id)])->with('success', __('Invoice not found'));
                }

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
                return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice_id)])->with('success', __('Invoice not found'));
            }

        }
    }
}
