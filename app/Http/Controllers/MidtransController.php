<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utility;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use App\Models\Plan;
use App\Models\Coupon;
use App\Models\UserCoupon;
use App\Models\Order;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\InvoicePayment;
use App\Models\Project;
use App\Models\User;
use Exception;

class MidtransController extends Controller
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
        // $this->midtras_server_key = isset($payment_setting['midtrans_server_key']) ? $payment_setting['midtrans_server_key'] : '';
        if ($payment_setting['midtrans_mode'] == 'live') {
            config([
                'midtrans_server_key' => isset($payment_setting['midtrans_server_key']) ? $payment_setting['midtrans_server_key'] : '',
            ]);
        } else {
            config([
                'midtrans_server_key' => isset($payment_setting['midtrans_server_key']) ? $payment_setting['midtrans_server_key'] : '',
            ]);
        }
    }

    public function invoicePayWithMidtrans(Request $request, $slug, $invoice_id)
    {
        $this->setPaymentDetail_client($invoice_id);
        $user_auth = Auth::user();
        $get_amount = $request->amount;
        $client_keyword = isset($user_auth) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
        $invoice = Invoice::find($invoice_id);
        $validatorArray = [
            'amount' => 'required',
        ];
        $validator = Validator::make(
            $request->all(),
            $validatorArray
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors()->first(),
                ],
                401
            );
        }

        try {
            if ($invoice) {
                try {
                    $payment_setting = Utility::getPaymentSetting($this->user->currentWorkspace->id);
                    $production = isset($payment_setting['midtrans_mode']) && $payment_setting['midtrans_mode'] == 'live' ? true : false;
                    // Set your Merchant Server Key
                    \Midtrans\Config::$serverKey = config('midtrans_server_key');
                    // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
                    \Midtrans\Config::$isProduction = $production;
                    // Set sanitization on (default)
                    \Midtrans\Config::$isSanitized = true;
                    // Set 3DS transaction for credit card to true
                    \Midtrans\Config::$is3ds = true;

                    $params = array(
                        'transaction_details' => array(
                            'order_id' => $orderID,
                            'gross_amount' => $get_amount,
                        ),
                        'customer_details' => array(
                            'first_name' => $this->user->name,
                            'last_name' => '',
                            'email' => $this->user->email,
                            'phone' => '8787878787',
                        ),
                    );
                    $snapToken = \Midtrans\Snap::getSnapToken($params);
                } catch (Exception $e) {
                    return redirect()->back()->with('error', $e->getMessage());
                }



                $data = [
                    'snap_token' => $snapToken,
                    'midtrans_secret' => config('midtrans_server_key'),
                    'invoice_id' => $invoice_id,
                    'amount' => $get_amount,
                    'fallback_url' => $client_keyword . 'invoice.midtrans.status',
                    $slug
                ];

                return view('midtrans.payment', compact('data'));
            } else {
                return redirect()->back()->with('error', 'Invoice not found.');
            }
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', __($e));
        }
    }


    public function getInvoicePaymentStatus(Request $request, $slug)
    {
        $response = json_decode($request->json, true);
        $responseArray = [];
        foreach ($response as $key => $value) {
            $responseArray[$key] = $value;
        }

        $invoice_id = $request->invoice_id;
        $amount = $request->amount;
        $this->setPaymentDetail_client($invoice_id);

        $user_auth = Auth::user();
        $client_keyword = isset($user_auth) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';
        if (!empty($invoice_id)) {
            $invoice = Invoice::find($invoice_id);

            if (!empty($invoice)) {
                $currentWorkspace = Utility::getWorkspaceBySlug($slug);
                $invoice_payment = new InvoicePayment();
                $invoice_payment->order_id = $responseArray['order_id'];
                $invoice_payment->invoice_id = $invoice_id;
                $invoice_payment->currency = isset($currentWorkspace->currency_code) ? $currentWorkspace->currency_code : 'USD';
                $invoice_payment->amount = $amount;
                $invoice_payment->payment_type = 'Midtrans';
                $invoice_payment->receipt = '';
                $invoice_payment->client_id = $this->user->id;
                $invoice_payment->txn_id = $responseArray['transaction_id'];
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
                        'client.invoices.show',
                        [
                            $slug,
                            $invoice->id,
                        ]
                    )->with('success', __('Payment added Successfully'));
                } else {
                    return redirect()->route('pay.invoice', [$slug, Crypt::encrypt($invoice->id)])->with('success', __('Payment added Successfully'));
                }
            } else {
                if (\Auth::check()) {
                    return redirect()->route(
                        $client_keyword . 'invoices.show',
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
            if (\Auth::check()) {
                return redirect()->route(
                    $client_keyword . 'invoices.show',
                    [
                        $slug,
                        $invoice_id,
                    ]
                )->with('error', __('Invoice not found.'));
            } else {
                return redirect()->route('pay.invoice', [$slug, Crypt::encrypt($invoice_id)])->with('error', __('Invoice not found!'));
            }
        }
    }
}
