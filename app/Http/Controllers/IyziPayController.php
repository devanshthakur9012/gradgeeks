<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Utility;
use App\Models\Plan;
use App\Models\UserCoupon;
use App\Models\User;
use App\Models\Store;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Crypt;
use Exception;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\Project;
use App\Models\InvoicePayment;


class IyziPayController extends Controller
{
    public $iyzipay_api_key, $iyzipay_secret_key, $iyzipay_mode, $is_iyzipay_enabled, $invoiceData, $user;

    public function setPaymentDetail_client($invoice_id)
    {

        $invoice = Invoice::find($invoice_id);

        if (Auth::user() != null) {
            $this->user = Auth::user();
        } else {
            $this->user = Client::where('id', $invoice->client_id)->first();
        }

        $payment_setting = Utility::getPaymentSetting($this->user->currentWorkspace->id);

        $this->iyzipay_api_key = isset($payment_setting['iyzipay_api_key']) ? $payment_setting['iyzipay_api_key'] : '';
        $this->iyzipay_secret_key = isset($payment_setting['iyzipay_secret_key']) ? $payment_setting['iyzipay_secret_key'] : '';
        $this->iyzipay_mode = isset($payment_setting['iyzipay_mode']) ? $payment_setting['iyzipay_mode'] : '';
        $this->is_iyzipay_enabled = isset($payment_setting['is_iyzipay_enabled']) ? $payment_setting['is_iyzipay_enabled'] : 'off';

    }

    // public function initiatePayment(Request $request)
    // {
    //     $planID    = \Illuminate\Support\Facades\Crypt::decrypt($request->plan_id);
    //     $authuser  = \Auth::user();
    //     $adminPaymentSettings = Utility::getAdminPaymentSettings();

    //     $iyzipay_api_key = $adminPaymentSettings['iyzipay_api_key'];
    //     $iyzipay_secret_key = $adminPaymentSettings['iyzipay_secret_key'];
    //     $iyzipay_mode = $adminPaymentSettings['iyzipay_mode'];

    //     $currency = env('CURRENCY') ? env('CURRENCY') : '$';
    //     $plan = Plan::find($planID);
    //     $coupon_id = '0';
    //     if($request->iyzipay_payment_frequency == 'annual'){
    //         $price = $plan->annual_price;
    //     }else{
    //         $price = $plan->monthly_price;
    //     }

    //     $frequency = $request->iyzipay_payment_frequency;
    //     $coupon_code = null;
    //     $discount_value = null;
    //     $coupons = Coupon::where('code', $request->coupon)->where('is_active', '1')->first();
    //     if ($coupons) {
    //         $coupon_code = $coupons->code;
    //         $usedCoupun     = $coupons->used_coupon();
    //         if ($coupons->limit == $usedCoupun) {
    //             $res_data['error'] = __('This coupon code has expired.');
    //         } else {
    //             $discount_value = ($price / 100) * $coupons->discount;
    //             $price  = $price - $discount_value;
    //             if ($price < 0) {
    //                 $price = $price;
    //             }
    //             $coupon_id = $coupons->id;
    //         }
    //     }

    //     $res_data['total_price'] = $price;
    //     $res_data['coupon']      = $coupon_id;
    //     // set your Iyzico API credentials
    //     try {

    //         $setBaseUrl = ($iyzipay_mode == 'sandbox') ? 'https://sandbox-api.iyzipay.com' : 'https://api.iyzipay.com';
    //         $options = new \Iyzipay\Options();
    //         $options->setApiKey($iyzipay_api_key);
    //         $options->setSecretKey($iyzipay_secret_key);
    //         $options->setBaseUrl($setBaseUrl); // or "https://api.iyzipay.com" for production
    //         $ipAddress = Http::get('https://ipinfo.io/?callback=')->json();
    //         $address = ($authuser->address) ? $authuser->address : 'Nidakule Göztepe, Merdivenköy Mah. Bora Sok. No:1';
    //         // create a new payment request
    //         $request = new \Iyzipay\Request\CreateCheckoutFormInitializeRequest();
    //         $request->setLocale('en');
    //         $request->setPrice($res_data['total_price']);
    //         $request->setPaidPrice($res_data['total_price']);
    //         $request->setCurrency($currency);
    //         $request->setCallbackUrl(route('plan.iyzipay',[$plan->id, $price ,$frequency,$coupon_id]));
    //         $request->setEnabledInstallments(array(1));
    //         $request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);

    //         $buyer = new \Iyzipay\Model\Buyer();
    //         $buyer->setId($authuser->id);
    //         $buyer->setName(explode(' ', $authuser->name)[0]);
    //         $buyer->setSurname(explode(' ', $authuser->name)[0]);
    //         $buyer->setGsmNumber("+" . $authuser->dial_code . $authuser->phone);
    //         $buyer->setEmail($authuser->email);
    //         $buyer->setIdentityNumber(rand(0, 999999));
    //         $buyer->setLastLoginDate("2023-03-05 12:43:35");
    //         $buyer->setRegistrationDate("2023-04-21 15:12:09");
    //         $buyer->setRegistrationAddress($address);
    //         $buyer->setIp($ipAddress['ip']);
    //         $buyer->setCity($ipAddress['city']);
    //         $buyer->setCountry($ipAddress['country']);
    //         $buyer->setZipCode($ipAddress['postal']);
    //         $request->setBuyer($buyer);

    //         $shippingAddress = new \Iyzipay\Model\Address();
    //         $shippingAddress->setContactName($authuser->name);
    //         $shippingAddress->setCity($ipAddress['city']);
    //         $shippingAddress->setCountry($ipAddress['country']);
    //         $shippingAddress->setAddress($address);
    //         $shippingAddress->setZipCode($ipAddress['postal']);
    //         $request->setShippingAddress($shippingAddress);

    //         $billingAddress = new \Iyzipay\Model\Address();
    //         $billingAddress->setContactName($authuser->name);
    //         $billingAddress->setCity($ipAddress['city']);
    //         $billingAddress->setCountry($ipAddress['country']);
    //         $billingAddress->setAddress($address);
    //         $billingAddress->setZipCode($ipAddress['postal']);
    //         $request->setBillingAddress($billingAddress);

    //         $basketItems = array();
    //         $firstBasketItem = new \Iyzipay\Model\BasketItem();
    //         $firstBasketItem->setId("BI101");
    //         $firstBasketItem->setName("Binocular");
    //         $firstBasketItem->setCategory1("Collectibles");
    //         $firstBasketItem->setCategory2("Accessories");
    //         $firstBasketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
    //         $firstBasketItem->setPrice($res_data['total_price']);
    //         $basketItems[0] = $firstBasketItem;
    //         $request->setBasketItems($basketItems);

    //         $checkoutFormInitialize = \Iyzipay\Model\CheckoutFormInitialize::create($request, $options);
    //         return redirect()->to($checkoutFormInitialize->getpaymentPageUrl());
    //     } catch (\Exception $e) {
    //         return redirect()->route('plans.index')->with('errors', $e->getMessage());
    //     }
    // }

    // public function getPaymentStatus(Request $request, $planID, $price, $frequency ,$coupanCode = null)
    // {
    //     // $plan_id    = \Illuminate\Support\Facades\Crypt::decrypt($planID);
    //     $plan = Plan::find($planID);
    //     $user = \Auth::user();

    //     $order = new Order();
    //     $order->order_id = time();
    //     $order->name = $user->name;
    //     $order->card_number = '';
    //     $order->card_exp_month = '';
    //     $order->card_exp_year = '';
    //     $order->plan_name = $plan->name;
    //     $order->plan_id = $plan->id;
    //     $order->price = $price;
    //     $order->price_currency = env('CURRENCY') ? env('CURRENCY') : '$';
    //     $order->txn_id = time();
    //     $order->payment_type = __('Iyzipay');
    //     $order->payment_status = 'succeeded';
    //     $order->txn_id = '';
    //     $order->receipt = '';
    //     $order->user_id = $user->id;
    //     $order->save();
    //     $user = User::find($user->id);
    //     $coupons = Coupon::where('id', $coupanCode)->where('is_active', '1')->first();
    //     if (!empty($coupons)) {
    //         $userCoupon         = new UserCoupon();
    //         $userCoupon->user   = $user->id;
    //         $userCoupon->coupon = $coupons->id;
    //         $userCoupon->order  = $order->order_id;
    //         $userCoupon->save();
    //         $usedCoupun = $coupons->used_coupon();
    //         if ($coupons->limit <= $usedCoupun) {
    //             $coupons->is_active = 0;
    //             $coupons->save();
    //         }
    //     }
    //     $assignPlan = $user->assignPlan($plan->id , $frequency);


    //     if ($assignPlan['is_success']) {

    //         return redirect()->route('plans.index')->with('success', __('Plan activated Successfully.'));

    //     } else {

    //         return redirect()->route('plans.index')->with('error', __($assignPlan['error']));

    //     }
    // }

    public function invoicepaywithiyzipay(Request $request, $slug, $invoice_id)
    {

        $this->setPaymentDetail_client($invoice_id);

        $invoice = Invoice::find($invoice_id);
        $get_amount = $request->amount;
        $user1 = Auth::user();
        $client_keyword = isset($user1) ? (($user1->getGuard() == 'client') ? 'client.' : '') : '';


        if ($invoice) {
            if ($get_amount > $invoice->getDueAmount()) {
                return redirect()->back()->with('error', __('Invalid amount.'));
            } else {
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                try {

                    $setBaseUrl = ($this->iyzipay_mode == 'sandbox') ? 'https://sandbox-api.iyzipay.com' : 'https://api.iyzipay.com';
                    $options = new \Iyzipay\Options();
                    $options->setApiKey($this->iyzipay_api_key);
                    $options->setSecretKey($this->iyzipay_secret_key);
                    $options->setBaseUrl($setBaseUrl); // or "https://api.iyzipay.com" for production
                    $ipAddress = Http::get('https://ipinfo.io/?callback=')->json();
                    $address = ($this->user->address) ? $this->user->address : 'Nidakule Göztepe, Merdivenköy Mah. Bora Sok. No:1';
                    // create a new payment request
                    $request = new \Iyzipay\Request\CreateCheckoutFormInitializeRequest();
                    $request->setLocale('en');
                    $request->setPrice($get_amount);
                    $request->setPaidPrice($get_amount);
                    $request->setCurrency($this->user->currentWorkspace->currency_code ? $this->user->currentWorkspace->currency_code : 'USD');
                    $request->setCallbackUrl(route($client_keyword . 'invoice.iyzipay', [$slug, $invoice_id, $get_amount]));
                    $request->setEnabledInstallments(array(1));
                    $request->setPaymentGroup(\Iyzipay\Model\PaymentGroup::PRODUCT);

                    $buyer = new \Iyzipay\Model\Buyer();
                    $buyer->setId($this->user->id);
                    $buyer->setName(explode(' ', $this->user->name)[0]);
                    $buyer->setSurname(explode(' ', $this->user->name)[0]);
                    $buyer->setGsmNumber("+" . $this->user->dial_code . $this->user->phone);
                    $buyer->setEmail($this->user->email);
                    $buyer->setIdentityNumber(rand(0, 999999));
                    $buyer->setLastLoginDate("2023-03-05 12:43:35");
                    $buyer->setRegistrationDate("2023-04-21 15:12:09");
                    $buyer->setRegistrationAddress($address);
                    $buyer->setIp($ipAddress['ip']);
                    $buyer->setCity($ipAddress['city']);
                    $buyer->setCountry($ipAddress['country']);
                    $buyer->setZipCode($ipAddress['postal']);
                    $request->setBuyer($buyer);

                    $shippingAddress = new \Iyzipay\Model\Address();
                    $shippingAddress->setContactName($this->user->name);
                    $shippingAddress->setCity($ipAddress['city']);
                    $shippingAddress->setCountry($ipAddress['country']);
                    $shippingAddress->setAddress($address);
                    $shippingAddress->setZipCode($ipAddress['postal']);
                    $request->setShippingAddress($shippingAddress);

                    $billingAddress = new \Iyzipay\Model\Address();
                    $billingAddress->setContactName($this->user->name);
                    $billingAddress->setCity($ipAddress['city']);
                    $billingAddress->setCountry($ipAddress['country']);
                    $billingAddress->setAddress($address);
                    $billingAddress->setZipCode($ipAddress['postal']);
                    $request->setBillingAddress($billingAddress);

                    $basketItems = array();
                    $firstBasketItem = new \Iyzipay\Model\BasketItem();
                    $firstBasketItem->setId("BI101");
                    $firstBasketItem->setName("Binocular");
                    $firstBasketItem->setCategory1("Collectibles");
                    $firstBasketItem->setCategory2("Accessories");
                    $firstBasketItem->setItemType(\Iyzipay\Model\BasketItemType::PHYSICAL);
                    $firstBasketItem->setPrice($get_amount);
                    $basketItems[0] = $firstBasketItem;
                    $request->setBasketItems($basketItems);

                    $checkoutFormInitialize = \Iyzipay\Model\CheckoutFormInitialize::create($request, $options);
                    return redirect()->to($checkoutFormInitialize->getpaymentPageUrl());
                } catch (Exception $e) {
                    return redirect()->back()->with('errors', $e->getMessage());
                }
            }
        } else {

            return redirect()->back()->with('error', __('Invoice Not Found!.'));
        }

    }

    public function getInvoicePaymentStatus(Request $request, $slug, $invoice_id, $amount)
    {
        $this->setPaymentDetail_client($invoice_id);

        // $invoice_id = decrypt($invoice_id);
        $invoice = Invoice::find($invoice_id);
        if ($invoice) {
            // $user             = Auth::user();
            $currentWorkspace = Utility::getWorkspaceBySlug_copylink('invoice', $invoice_id);

            try {
                $order_id = strtoupper(str_replace('.', '', uniqid('', true)));

                $invoice_payment = new InvoicePayment();
                $invoice_payment->order_id = $order_id;
                $invoice_payment->invoice_id = $invoice->id;
                $invoice_payment->currency = $currentWorkspace->currency_code;
                $invoice_payment->amount = isset($amount) ? $amount : 0;
                $invoice_payment->payment_type = 'Iyzipay';
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

                // return redirect()->back()->with('success', __('Invoice paid Successfully'));

                if (\Auth::check()) {
                    return redirect()->route(
                        'client.invoices.show',
                        [
                            $slug,
                            $invoice_id,
                        ]
                    )->with('success', __('Invoice paid Successfully!'));
                } else {
                    return redirect()->route('pay.invoice', [$slug, Crypt::encrypt($invoice->id)])->with('success', __('Invoice paid Successfully'));
                }
            } catch (Exception $e) {
                return redirect()->back()->with('error', __('Something went wrong.'));

                return redirect()->route(
                    'client.invoices.show',
                    [
                        $slug,
                        $invoice_id,
                    ]
                )->with('error', __('Something went wrong.'));
            }
        } else {
            return redirect()->back()->with('error', __('Invoice not found.'));

            return redirect()->route(
                'client.invoices.show',
                [
                    $slug,
                    $invoice_id,
                ]
            )->with('error', __('Invoice not found.'));
        }
    }
}
