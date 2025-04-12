<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Project;
use App\Models\User;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Session;

class PayUController extends Controller
{
    //GenerateHash Function
    function generateHash($key, $txnid, $amount, $productinfo, $firstname, $email, $salt, $udf1 = '', $udf2 = '', $udf3 = '', $udf4 = '', $udf5 = '')
    {
        $data = $key . '|' . $txnid . '|' . $amount . '|' . $productinfo . '|' . $firstname . '|' . $email . '|' . $udf1 . '|' . $udf2 . '|' . $udf3 . '|' . $udf4 . '|' . $udf5 . '||||||' . $salt;
        return hash('sha512', $data);
    }

    // Pay Function
    public function pay($pay)
    {
        try {
            $pay         = Crypt::decrypt($pay);
            $pay         = (object) Session::get($pay);
            $pay->action =  ($pay->mode != 'sandbox') ? "https://secure.payu.in/_payment" : "https://test.payu.in/_payment";
            $pay->hash   = $this->generateHash($pay->key, $pay->txnid, $pay->amount, $pay->productinfo, $pay->firstname, $pay->email, $pay->salt);

            return view('invoices.payu_request', compact('pay'));
        } catch (\Exception $e) {
        }
    }

    // Invoice Payment
    public function invoicePayWithPayu(Request $request, $slug, $invoice_id)
    {
        $invoice              = Invoice::find($invoice_id);
        $currentWorkspace     = Utility::getWorkspaceBySlug($slug);
        $payment_setting      = Utility::getPaymentSetting($currentWorkspace->id);
        $payu_merchant_key    = isset($payment_setting['payu_merchant_key']) ? $payment_setting['payu_merchant_key'] : '';
        $payu_salt_key        = isset($payment_setting['payu_salt_key']) ? $payment_setting['payu_salt_key'] : '';
        $payu_mode            = isset($payment_setting['payu_mode']) ? $payment_setting['payu_mode'] : 'sandbox';
        $orderId              = strtoupper(str_replace('.', '', uniqid('', true)));

        if (Auth::user() != null) {
            $this->user = Auth::user();
        } else {
            $this->user = Client::where('id', $invoice->client_id)->first();
        }

        $user_auth = Auth::user();
        $client_keyword = isset($user_auth) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';

        if ($invoice) {
            try {
                if (!empty($payu_merchant_key)) {
                    $pay = [
                        'key'               => $payu_merchant_key,
                        'mode'              => $payu_mode,
                        'salt'              => $payu_salt_key,
                        'txnid'             => $orderId,
                        'amount'            => $request->amount,
                        'firstname'         => $this->user->name,
                        'email'             => $this->user->email,
                        'surl'              => route($client_keyword . 'invoice.get.payu.status', [
                            'id'    => $invoice->id,
                            'amt'   => $request->amount,
                            'slug'  => $currentWorkspace->slug,
                            'data'   => Crypt::encrypt([
                                'status'    => 'true',
                                'order_id'  => $orderId,
                                'amount'    => $request->amount
                            ])
                        ]),
                        'furl' => route($client_keyword . 'invoice.get.payu.status', [
                            'id'    => $invoice->id,
                            'amt'   => $request->amount,
                            'slug'  => $currentWorkspace->slug,
                            'data'  => Crypt::encrypt([
                                'status'    => 'false',
                                'order_id'  => $orderId,
                                'amount'    => $request->amount
                            ])
                        ]),

                        'productinfo'       => 'WorkDo',
                        'service_provider'  => 'payu_paisa',
                    ];
                    $pay_id = $orderId . '777';
                    $session = $request->toArray();
                    $request->session()->put('order_id', $session);
                    Session::flash($pay_id, $pay);
                    return redirect()->to(route('payu.pay', ['pay' => Crypt::encrypt($pay_id)]));
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', __($e->getMessage()));
            }
        } else {
            return redirect()->back()->with('error', __('Invoice Not Found'));
        }
    }

    // Invoice status
    public function getPayuStatus(Request $request, $invoice_id)
    {
        $data               = Crypt::decrypt($request->data);
        $orderID            = $data['order_id'];
        $amount             = $data['amount'];
        $invoice            = Invoice::find($invoice_id);
        $currentWorkspace   = Utility::getWorkspaceBySlug_copylink('invoice', $invoice->id);
        $slug               = $request->slug;
        
        if (Auth::user() != null) {
            $this->user = Auth::user();
        } else {
            $this->user = Client::where('id', $invoice->client_id)->first();
        }

        if (isset($data['status']) && $data['status'] == 'true') {
            if ($invoice) {
                $invoice_payment                 = new InvoicePayment();
                $invoice_payment->order_id       = $orderID;
                $invoice_payment->invoice_id     = $invoice->id;
                $invoice_payment->currency       = $currentWorkspace->currency_code;
                $invoice_payment->amount         = $amount;
                $invoice_payment->payment_type   = __('PayU');
                $invoice_payment->client_id      = $this->user->id;
                $invoice_payment->receipt        = '';
                $invoice_payment->txn_id         = '';
                $invoice_payment->payment_status = 'succeeded';
                $invoice_payment->save();

                if (($invoice->getDueAmount() - $invoice_payment->amount) == 0) {
                    $invoice->status = 'paid';
                    $invoice->save();
                } else {
                    $invoice->status = 'partialy paid';
                    $invoice->save();
                }

                $user1          = $currentWorkspace->id;
                $settings       = Utility::getPaymentSetting($user1);
                $total_amount   = $invoice->getDueAmounts($invoice->id);
                $client         = Client::find($invoice->client_id);
                $project_name   = Project::where('id', $invoice->project_id)->first();

                $uArr = [
                    'project_name' => $project_name->name,
                    'company_name' => User::find($project_name->created_by)->name,
                    'invoice_id'   => Utility::invoiceNumberFormat($invoice->id),
                    'client_name'  => $client->name,
                    'total_amount' => $total_amount,
                    'paid_amount'  => $request->amount,
                ];

                if (isset($settings['invoicest_notificaation']) && $settings['invoicest_notificaation'] == 1) {
                    Utility::send_slack_msg('Invoice Status Updated', $user1, $uArr);
                }

                if (isset($settings['telegram_invoicest_notificaation']) && $settings['telegram_invoicest_notificaation'] == 1) {
                    Utility::send_telegram_msg('Invoice Status Updated', $uArr, $user1);
                }

                //webhook
                $module  = 'Invoice Status Updated';
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
                        'client.invoices.show',
                        [
                            $slug,
                            $invoice->id,
                        ]
                    )->with('success', __('Invoice paid Successfully'));
                } else {
                    return redirect()->route('pay.invoice', ['slug' => $slug, 'id' => Crypt::encrypt($invoice->id)])->with('success', __('Payment added Successfully'));
                }
            } else {
                if (Auth::check()) {
                    return redirect()->route(
                        'client.invoices.show',
                        [
                            $slug,
                            $invoice_id,
                        ]
                    )->with('error', __('Invoice not found.'));
                } else {
                    return redirect()->route('pay.invoice', [$slug, Crypt::encrypt($invoice_id)])->with('error', __('Invoice not found!'));
                }
            }
        } else {
            if (Auth::check()) {
                return redirect()->route(
                    'client.invoices.show',
                    [
                        $slug,
                        $invoice_id,
                    ]
                )->with('error', __('Your Payment has failed!'));
            } else {
                return redirect()->route('pay.invoice', [$slug, Crypt::encrypt($invoice_id)])->with('error', __('Your Payment has failed!'));
            }
        }
    }
}
