<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Plan;
use App\Models\PlanOrder;
use App\Models\UserCoupon;
use App\Models\InvoicePayment;
use App\Models\ProductVariantOption;
use App\Models\PurchasedProducts;
use App\Models\ProductCoupon;
use App\Models\Invoice;
use GuzzleHttp\Client as benefit_client;
use Exception;
use App\Models\User;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Http\Request;
use App\Models\Utility;
use App\Models\Shipping;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use PhpParser\Node\Stmt\TryCatch;

class BenefitPaymentController extends Controller
{
    public function planPayWithbenefit(Request $request)
    {
        $admin_payment_setting = Utility::getAdminPaymentSettings();
        $secret_key = $admin_payment_setting['benefit_secret_key'];
        $objUser = \Auth::user();
        $planID = Crypt::decrypt($request->plan_id);
        $plan = Plan::find($planID);
        if ($plan) {

            if ($request->benefit_payment_frequency == 'annual') {
                $get_amount = $plan->annual_price;
            } else {
                $get_amount = $plan->monthly_price;
            }
            // $get_amount = $plan->price;
            try {
                if (!empty($request->coupon)) {
                    $coupons = Coupon::where('code', strtoupper($request->coupon))->where('is_active', '1')->first();
                    if (!empty($coupons)) {
                        $usedCoupun = $coupons->used_coupon();
                        $discount_value = ($get_amount / 100) * $coupons->discount;
                        $get_amount = $get_amount - $discount_value;

                        if ($coupons->limit == $usedCoupun) {
                            return redirect()->back()->with('error', __('This coupon code has expired.'));
                        }
                        if ($get_amount <= 0) {
                            $authuser = \Auth::user();
                            $authuser->plan = $plan->id;
                            $authuser->save();
                            $assignPlan = $authuser->assignPlan($plan->id);
                            if ($assignPlan['is_success'] == true && !empty($plan)) {
                                if (!empty($authuser->payment_subscription_id) && $authuser->payment_subscription_id != '') {
                                    try {
                                        $authuser->cancel_subscription($authuser->id);
                                    } catch (Exception $exception) {
                                        \Log::debug($exception->getMessage());
                                    }
                                }
                                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                                $userCoupon = new UserCoupon();
                                $userCoupon->user = $authuser->id;
                                $userCoupon->coupon = $coupons->id;
                                $userCoupon->order = $orderID;
                                $userCoupon->save();
                                Order::create(
                                    [
                                        'order_id' => $orderID,
                                        'name' => null,
                                        'email' => null,
                                        'card_number' => null,
                                        'card_exp_month' => null,
                                        'card_exp_year' => null,
                                        'plan_name' => $plan->name,
                                        'plan_id' => $plan->id,
                                        'price' => $get_amount == null ? 0 : $get_amount,
                                        'price_currency' => !empty(env('CURRENCY')) ? env('CURRENCY') : 'USD',
                                        'txn_id' => '',
                                        'payment_frequency' => $request->paytab_payment_frequency,
                                        'payment_type' => 'Benefit',
                                        'payment_status' => 'success',
                                        'receipt' => null,
                                        'user_id' => $authuser->id,
                                    ]
                                );
                                $assignPlan = $authuser->assignPlan($plan->id, $request->paytab_payment_frequency);
                                return redirect()->route('plans.index')->with('success', __('Plan Successfully Activated'));
                            }
                        }
                    } else {
                        return redirect()->back()->with('error', __('This coupon code is invalid or has expired.'));
                    }
                }

                $coupon = (empty($request->coupon)) ? "0" : $request->coupon;
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                $frequency = $request->benefit_payment_frequency;

                $userData =
                    [
                        "amount" => $get_amount,
                        "currency" => !empty(env('CURRENCY')) ? env('CURRENCY') : 'USD',
                        "customer_initiated" => true,
                        "threeDSecure" => true,
                        "save_card" => false,
                        "description" => " Plan - " . $plan->name,
                        "metadata" => ["udf1" => "Metadata 1"],
                        "reference" => ["transaction" => "txn_01", "order" => "ord_01"],
                        "receipt" => ["email" => true, "sms" => true],
                        "customer" => ["first_name" => $objUser->name, "middle_name" => "", "last_name" => "", "email" => $objUser->email, "phone" => ["country_code" => 965, "number" => 51234567]],
                        "source" => ["id" => "src_bh.benefit"],
                        "post" => ["url" => "https://webhook.site/fd8b0712-d70a-4280-8d6f-9f14407b3bbd"],
                        "redirect" => ["url" => route('benefit.call_back', ['plan_id' => $plan->id, 'amount' => $get_amount, 'coupon' => $coupon, 'frequency' => $frequency])],


                    ];

                $responseData = json_encode($userData);
                $client = new benefit_client();
                try {
                    $response = $client->request('POST', 'https://api.tap.company/v2/charges', [
                        'body' => $responseData,
                        'headers' => [
                            'Authorization' => 'Bearer ' . $secret_key,
                            'accept' => 'application/json',
                            'content-type' => 'application/json',
                        ],
                    ]);
                } catch (\Throwable $th) {
                    return redirect()->back()->with('error', 'Currency Not Supported.Contact To Your Site Admin');
                }

                $data = $response->getBody();
                $res = json_decode($data);
                return redirect($res->transaction->url);
            } catch (Exception $e) {

                return redirect()->back()->with('error', $e);
            }
        } else {
            return redirect()->route('plans.index')->with('error', __('Plan is deleted.'));
        }
    }

    public function getPaymentStatus(Request $request)
    {
        $admin_payment_setting = Utility::getAdminPaymentSettings();
        $secret_key = $admin_payment_setting['benefit_secret_key'];
        $user = \Auth::user();
        $plan = Plan::find($request->plan_id);
        $couponCode = $request->coupon;
        $getAmount = $request->amount;
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

        if ($couponCode != 0) {
            $coupons = Coupon::where('code', strtoupper($couponCode))->where('is_active', '1')->first();
            $request['coupon_id'] = $coupons->id;
        } else {
            $coupons = null;
        }
        try {
            $post = $request->all();
            $client = new benefit_client();
            $response = $client->request('GET', 'https://api.tap.company/v2/charges/' . $post['tap_id'], [
                'headers' => [
                    'Authorization' => 'Bearer ' . $secret_key,
                    'accept' => 'application/json',
                ],
            ]);

            $json = $response->getBody();
            $data = json_decode($json);
            $status_code = $data->gateway->response->code;

            if ($status_code == '00') {
                $order = new Order();
                $order->order_id = $orderID;
                $order->name = $user->name;
                $order->card_number = '';
                $order->card_exp_month = '';
                $order->card_exp_year = '';
                $order->plan_name = $plan->name;
                $order->plan_id = $plan->id;
                $order->price = $getAmount;
                $order->price_currency = !empty(env('CURRENCY')) ? env('CURRENCY') : 'USD';
                $order->payment_type = __('Benefit');
                $order->payment_status = 'success';
                $order->payment_frequency = $frequency;
                $order->txn_id = '';
                $order->receipt = '';
                $order->user_id = $user->id;
                $order->save();
                $assignPlan = $user->assignPlan($plan->id, $frequency);
                $coupons = Coupon::find($request->coupon_id);
                if (!empty($request->coupon_id)) {
                    if (!empty($coupons)) {
                        $userCoupon = new UserCoupon();
                        $userCoupon->user = $user->id;
                        $userCoupon->coupon = $coupons->id;
                        $userCoupon->order = $orderID;
                        $userCoupon->save();
                        $usedCoupun = $coupons->used_coupon();
                        if ($coupons->limit <= $usedCoupun) {
                            $coupons->is_active = 0;
                            $coupons->save();
                        }
                    }
                }

                if ($assignPlan['is_success']) {
                    return redirect()->route('plans.index')->with('success', __('Plan activated Successfully.'));
                } else {
                    return redirect()->route('plans.index')->with('error', __($assignPlan['error']));
                }

            } else {
                return redirect()->route('plans.index')->with('error', __('Your Transaction is fail please try again'));
            }
        } catch (Exception $e) {
            return redirect()->route('plans.index')->with('error', __($e->getMessage()));
        }
    }

    public function invoicePayWithbenefit(Request $request, $slug, $invoice_id)
    {

        $invoice = Invoice::find($invoice_id);
        $currentWorkspace = Utility::getWorkspaceBySlug_copylink('invoice', $invoice_id);
        $company_payment_setting = Utility::getPaymentSetting($currentWorkspace->id);
        $secret_key = $company_payment_setting['benefit_secret_key'];
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

                $userData =
                    [
                        "amount" => $get_amount,
                        "currency" => !empty($currentWorkspace->currency_code) ? $currentWorkspace->currency_code : 'USD',
                        "customer_initiated" => true,
                        "threeDSecure" => true,
                        "save_card" => false,
                        "description" => " Invoice - " . Utility::invoiceNumberFormat($invoice->id),
                        "metadata" => ["udf1" => "Metadata 1"],
                        "reference" => ["transaction" => "txn_01", "order" => "ord_01"],
                        "receipt" => ["email" => true, "sms" => true],
                        "customer" => ["first_name" => $user->name, "middle_name" => "", "last_name" => "", "email" => $user->email, "phone" => ["country_code" => 965, "number" => 51234567]],
                        "source" => ["id" => "src_bh.benefit"],
                        "post" => ["url" => "https://webhook.site/fd8b0712-d70a-4280-8d6f-9f14407b3bbd"],
                        "redirect" => ["url" => route($client_keyword . 'invoice.benefit.success', ['invoice_id' => $invoice->id, 'slug' => $slug, 'amount' => $get_amount])],


                    ];

                $responseData = json_encode($userData);
                $client = new benefit_client();
                try {
                    $response = $client->request('POST', 'https://api.tap.company/v2/charges', [
                        'body' => $responseData,
                        'headers' => [
                            'Authorization' => 'Bearer ' . $secret_key,
                            'accept' => 'application/json',
                            'content-type' => 'application/json',
                        ],
                    ]);
                    $data = $response->getBody();
                    $res = json_decode($data);
                    return redirect($res->transaction->url);
                } catch (\Throwable $th) {
                    return redirect()->back()->with('error', 'Currency Not Supported.Contact To Your Site Admin');
                }
            }
        }
    }

    public function getInvoicePaymentStatus(Request $request)
    {
        $invoice_id = $request->invoice_id;
        $currentWorkspace = Utility::getWorkspaceBySlug_copylink('invoice', $invoice_id);
        $user_auth = Auth::user();
        $client_keyword = isset($user_auth) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';
        $company_payment_setting = Utility::getPaymentSetting($currentWorkspace->id);
        $secret_key = $company_payment_setting['benefit_secret_key'];
        $slug = $request->slug;

        $invoice = Invoice::find($invoice_id);
        $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
        if (Auth::check()) {
            $user = Auth::user();
        } else {
            $user = User::where('id', $invoice->client_id)->first();
        }
        if ($invoice) {
            try {
                $post = $request->all();
                $client = new benefit_client();
                $response = $client->request('GET', 'https://api.tap.company/v2/charges/' . $post['tap_id'], [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $secret_key,
                        'accept' => 'application/json',
                    ],
                ]);

                $json = $response->getBody();
                $data = json_decode($json);
                $status_code = $data->gateway->response->code;

                if ($status_code == '00') {

                    $invoice_payment = new InvoicePayment();
                    $invoice_payment->order_id = $orderID;
                    $invoice_payment->invoice_id = $invoice->id;
                    $invoice_payment->currency = $currentWorkspace->currency_code;
                    $invoice_payment->amount = isset($request->amount) ? $request->amount : 0;
                    $invoice_payment->payment_type = 'Benefit';
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
                    if (Auth::user()) {
                        return redirect()->route('invoices.show', $invoice_id)->with('error', __('Transaction fail'));
                    } else {
                        $id = \Crypt::encrypt($invoice_id);

                        return redirect()->route('pay.invoice', $id)->with('error', __('Transaction fail!'));
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
                return redirect()->route('client.invoices.show', $invoice_id)->with('error', __('Invoice not found'));
            } else {
                $id = \Crypt::encrypt($invoice_id);

                return redirect()->route('pay.invoice', $id)->with('error', __('Invoice not found!'));
            }
        }
    }
}
