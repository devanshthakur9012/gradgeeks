<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Plan;
use App\Models\Order;
use App\Models\UserCoupon;
use App\Models\Utility;
use Illuminate\Http\Request;
use Twilio\TwiML\Voice\Stop;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use App\Models\InvoicePayment;
use Illuminate\Support\Facades\Auth;



class PaytrController extends Controller
{


    public function invoicePayWithPaytr(Request $request, $slug, $invoice_id)
    {
        $invoice = Invoice::find($invoice_id);
        $currentWorkspace = Utility::getWorkspaceBySlug_copylink('invoice', $invoice_id);

        $payment_setting = Utility::getPaymentSetting($currentWorkspace->id);
        $paytr_merchant_id = $payment_setting['paytr_merchant_id'];
        $paytr_merchant_key = $payment_setting['paytr_merchant_key'];
        $paytr_merchant_salt = $payment_setting['paytr_merchant_salt'];

        $user_auth = \Auth::user();
        $client_keyword = isset($user_auth) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';

        if (\Auth::check()) {
            $authuser = $user = \Auth::user();
        } else {
            $authuser = $user = Client::where('id', $invoice->client_id)->first();
        }

        $get_amount = $request->amount;
        if ($invoice && $get_amount != 0) {
            if ($get_amount > $invoice->getDueAmount()) {
                return redirect()->back()->with('error', __('Invalid amount.'));
            } else {

                try {

                    $merchant_id = $paytr_merchant_id;
                    $merchant_key = $paytr_merchant_key;
                    $merchant_salt = $paytr_merchant_salt;

                    $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

                    $email = $authuser->email;
                    $payment_amount = $get_amount;
                    $merchant_oid = $orderID;
                    $user_name = $authuser->name;
                    $user_address = isset($authuser->address) ? $authuser->address : 'No Address';
                    $user_phone = isset($authuser->telephone) ? $authuser->telephone : '0000000000';


                    $user_basket = base64_encode(json_encode(array(
                        array("Invoice", $payment_amount, 1),
                    )));

                    if (isset($_SERVER["HTTP_CLIENT_IP"])) {
                        $ip = $_SERVER["HTTP_CLIENT_IP"];
                    } elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"])) {
                        $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
                    } else {
                        $ip = $_SERVER["REMOTE_ADDR"];
                    }

                    $user_ip = $ip;
                    $timeout_limit = "30";
                    $debug_on = 1;
                    $test_mode = 0;
                    $no_installment = 0;
                    $max_installment = 0;
                    $currency = "TL";
                    $hash_str = $merchant_id . $user_ip . $merchant_oid . $email . $payment_amount . $user_basket . $no_installment . $max_installment . $currency . $test_mode;
                    $paytr_token = base64_encode(hash_hmac('sha256', $hash_str . $merchant_salt, $merchant_key, true));

                    $request['orderID'] = $orderID;
                    $request['invoice_id'] = $invoice_id;
                    $request['price'] = $get_amount;
                    $request['slug'] = $slug;
                    $request['payment_status'] = 'failed';
                    $payment_failed = $request->all();
                    $request['payment_status'] = 'success';
                    $payment_success = $request->all();

                    $post_vals = array(
                        'merchant_id' => $merchant_id,
                        'user_ip' => $user_ip,
                        'merchant_oid' => $merchant_oid,
                        'email' => $email,
                        'payment_amount' => $payment_amount,
                        'paytr_token' => $paytr_token,
                        'user_basket' => $user_basket,
                        'debug_on' => $debug_on,
                        'no_installment' => $no_installment,
                        'max_installment' => $max_installment,
                        'user_name' => $user_name,
                        'user_address' => $user_address,
                        'user_phone' => $user_phone,
                        'merchant_ok_url' => route($client_keyword . 'invoice.paytr.success', $payment_success, $slug),
                        'merchant_fail_url' => route($client_keyword . 'invoice.paytr.success', $payment_failed, $slug),
                        'timeout_limit' => $timeout_limit,
                        'currency' => $currency,
                        'test_mode' => $test_mode
                    );

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "https://www.paytr.com/odeme/api/get-token");
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vals);
                    curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 20);


                    $result = @curl_exec($ch);

                    if (curl_errno($ch)) {
                        die("PAYTR IFRAME connection error. err:" . curl_error($ch));
                    }

                    curl_close($ch);

                    $result = json_decode($result, 1);

                    if ($result['status'] == 'success') {
                        $token = $result['token'];
                    } else {

                        if (Auth::user()) {
                            return redirect()->route('client.invoices.show', [$slug, $invoice_id])->with('error', $result['reason']);
                        } else {
                            return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice_id)])->with('error', $result['reason']);
                        }

                    }

                    return view('invoices.paytr_payment', compact('token'));

                } catch (\Throwable $th) {
                    if (Auth::user()) {
                        return redirect()->route('client.invoices.show', [$slug, $invoice_id])->with('error', $th->getMessage());
                    } else {
                        return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice_id)])->with('error', $th->getMessage());
                    }
                }
            }
        }
    }

    public function getInvoicePaymentStatus(Request $request)
    {
        $invoice_id = $request->invoice_id;
        $slug = $request->slug;
        $currentWorkspace = Utility::getWorkspaceBySlug_copylink('invoice', $invoice_id);
        $user_auth = \Auth::user();
        $client_keyword = isset($user_auth) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';


        if (!empty($invoice_id)) {
            $invoice = Invoice::find($invoice_id);
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

            if (\Auth::check()) {
                $user = \Auth::user();
            } else {
                $user = Client::where('id', $invoice->client_id)->first();
            }
            if ($invoice) {
                try {
                    if ($request->payment_status == "success") {

                        $invoice_payment = new InvoicePayment();
                        $invoice_payment->order_id = $orderID;
                        $invoice_payment->invoice_id = $invoice->id;
                        $invoice_payment->currency = $currentWorkspace->currency_code;
                        $invoice_payment->amount = isset($request->amount) ? $request->amount : 0;
                        $invoice_payment->payment_type = 'PayTr';
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
                            )->with('error', __('Transaction fail!'));
                        } else {
                            return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)])->with('success', __('Payment added Successfully'));

                        }
                    }
                } catch (\Exception $e) {

                    if (\Auth::user()) {
                        return redirect()->route('client.invoices.show', [$slug, $invoice_id])->with('error', $th->getMessage());
                    } else {
                        return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice_id)])->with('error', $th->getMessage());
                    }
                }

            } else {
                if (\Auth::user()) {
                    return redirect()->route('client.invoices.show', [$slug, $invoice_id])->with('error', __('Invoice not found'));
                } else {
                    return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice_id)])->with('error', __('Transaction fail!'));
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

