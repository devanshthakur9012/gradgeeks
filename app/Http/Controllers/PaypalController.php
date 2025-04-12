<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Order;
use App\Models\UserCoupon;
use App\Models\Utility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use App\Models\User;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use App\Models\Client;
use App\Models\Project;
use Srmklive\PayPal\Services\PayPal as PayPalClient;



class PaypalController extends Controller
{
    // private $_api_context;
    // private $user;

    // public function setPaymentDetail_client($invoice_id){

    //     $invoice = Invoice::find($invoice_id);
    //     $paypal_conf = config('paypal');

    //     if(Auth::user() != null){
    //         $this->user         = Auth::user();
    //     }else{
    //         $this->user         = Client::where('id',$invoice->client_id)->first();
    //     }

    //     $paypal_conf['settings']['mode'] = $this->user->currentWorkspace->paypal_mode;
    //         $paypal_conf['client_id']        = $this->user->currentWorkspace->paypal_client_id;
    //         $paypal_conf['secret_key']       = $this->user->currentWorkspace->paypal_secret_key;

    //     $this->_api_context = new ApiContext(
    //     new OAuthTokenCredential(
    //         $paypal_conf['client_id'], $paypal_conf['secret_key']
    //         )
    //     );
    //     $this->_api_context->setConfig($paypal_conf['settings']);
    // }
    // public function clientPayWithPaypal(Request $request, $slug, $invoice_id)
    // {

    //     $user1 = Auth::user();

    //     $client_keyword = isset($user1) ? (($user1->getGuard() == 'client') ? 'client.' : '') : '';

    //     $user = $this->user;

    //     $get_amount = $request->amount;

    //     $request->validate(['amount' => 'required|numeric|min:0']);

    //     $currentWorkspace = $user ? Utility::getWorkspaceBySlug($slug) : Utility::getWorkspaceBySlug_copylink( 'invoice' , $invoice_id);

    //     if($currentWorkspace)
    //     {
    //         $invoice = Invoice::find($invoice_id);

    //         if($invoice)
    //         {
    //             if($get_amount > $invoice->getDueAmount())
    //             {
    //                 return redirect()->back()->with('error', __('Invalid amount.'));
    //             }
    //             else
    //             {

    //                 $this->setPaymentDetail_client($invoice_id);


    //                 $name = $currentWorkspace->name . " - " . Utility::invoiceNumberFormat($invoice->invoice_id);

    //                 $payer = new Payer();
    //                 $payer->setPaymentMethod('paypal');

    //                 $item_1 = new Item();
    //                 $item_1->setName($name)->setCurrency($currentWorkspace->currency_code)->setQuantity(1)->setPrice($get_amount);

    //                 $item_list = new ItemList();
    //                 $item_list->setItems([$item_1]);

    //                 $amount = new Amount();
    //                 $amount->setCurrency($currentWorkspace->currency_code)->setTotal($get_amount);

    //                 $transaction = new Transaction();
    //                 $transaction->setAmount($amount)->setItemList($item_list)->setDescription($name);

    //                 $redirect_urls = new RedirectUrls();

    //                 $redirect_urls->setReturnUrl(
    //                     route(
    //                         $client_keyword.'get.payment.status', [
    //                                                         $currentWorkspace->slug,
    //                                                         $invoice->id,
    //                                                     ]
    //                     )
    //                 )->setCancelUrl(
    //                     route(
    //                         $client_keyword.'get.payment.status', [
    //                             $currentWorkspace->slug,
    //                             $invoice->id,
    //                             ]
    //                             )
    //                         );

    //                 $payment = new Payment();
    //                 $payment->setIntent('Sale')->setPayer($payer)->setRedirectUrls($redirect_urls)->setTransactions([$transaction]);

    //                 try
    //                 {

    //                     $payment->create($this->_api_context);
    //                 }
    //                 catch(\PayPal\Exception\PayPalConnectionException $ex) //PPConnectionException
    //                 {
    //                     if(\Config::get('app.debug'))
    //                     {
    //                         if(\Auth::check())
    //                         {
    //                             return redirect()->route(
    //                                 $client_keyword.'invoices.show', [
    //                                                             $currentWorkspace->slug,
    //                                                             $invoice_id,
    //                                                         ]
    //                             )->with('error', __('Connection timeout'));
    //                         }
    //                         else
    //                         {
    //                             return redirect()->route('pay.invoice',[$slug,\Illuminate\Support\Facades\Crypt::encrypt($invoice->id)])->with('error', __('Connection timeout!'));
    //                         }
    //                     }
    //                     else
    //                     {
    //                         if(\Auth::check())
    //                         {
    //                             return redirect()->route(
    //                                 $client_keyword.'invoices.show', [
    //                                                             $currentWorkspace->slug,
    //                                                             $invoice_id,
    //                                                         ]
    //                             )->with('error', __('Some error occur, sorry for inconvenient'));
    //                         }
    //                         else
    //                         {
    //                             return redirect()->route('pay.invoice',[$slug,\Illuminate\Support\Facades\Crypt::encrypt($invoice->id)])->with('error', __('Some error occur, sorry for inconvenient'));
    //                         }

    //                     }
    //                 }
    //                 foreach($payment->getLinks() as $link)
    //                 {
    //                     if($link->getRel() == 'approval_url')
    //                     {
    //                         $redirect_url = $link->getHref();
    //                         break;
    //                     }
    //                 }
    //                 Session::put('paypal_payment_id', $payment->getId());
    //                 if(isset($redirect_url))
    //                 {
    //                     return Redirect::away($redirect_url);
    //                 }

    //                 return redirect()->route(
    //                     'client.invoices.show', [
    //                                                 $currentWorkspace->slug,
    //                                                 $invoice_id,
    //                                             ]
    //                 )->with('error', __('Unknown error occurred'));
    //             }
    //         }
    //         else
    //         {
    //             return redirect()->back()->with('error', __('Permission denied.'));
    //         }
    //     }
    //     else
    //     {
    //         return redirect()->back()->with('error', __('Something is wrong'));
    //     }
    // }

    // public function clientGetPaymentStatus(Request $request, $slug, $invoice_id)
    // {
    //     $user = $this->user;

    //     $invoice = Invoice::find($invoice_id);

    //     $currentWorkspace = $user ? Utility::getWorkspaceBySlug($slug) : Utility::getWorkspaceBySlug_copylink( 'invoice' , $invoice_id);

    //     if($currentWorkspace && $invoice)
    //     {
    //         $this->setPaymentDetail_client($invoice_id);


    //         $payment_id = Session::get('paypal_payment_id');

    //         Session::forget('paypal_payment_id');

    //         if(empty($request->PayerID || empty($request->token)))
    //         {
    //             return redirect()->route(
    //                 'client.invoices.show', [
    //                                           $currentWorkspace->slug,
    //                                           $invoice_id,
    //                                       ]
    //             )->with('error', __('Payment failed'));
    //         }

    //         $payment = Payment::get($payment_id, $this->_api_context);

    //         $execution = new PaymentExecution();
    //         $execution->setPayerId($request->PayerID);

    //         try
    //         {
    //             $result = $payment->execute($execution, $this->_api_context)->toArray();

    //             $order_id = strtoupper(str_replace('.', '', uniqid('', true)));

    //             $status = ucwords(str_replace('_', ' ', $result['state']));

    //             if($result['state'] == 'approved')
    //             {
    //                 $amount = $result['transactions'][0]['amount']['total'];
    //             }
    //             else
    //             {
    //                 $amount = isset($result['transactions'][0]['amount']['total']) ? $result['transactions'][0]['amount']['total'] : '0.00';
    //             }

    //             $invoice_payment                 = new InvoicePayment();
    //             $invoice_payment->order_id       = $order_id;
    //             $invoice_payment->invoice_id     = $invoice->id;
    //             $invoice_payment->currency       = $currentWorkspace->currency_code;
    //             $invoice_payment->amount         = $amount;
    //             $invoice_payment->payment_type   = 'PAYPAL';
    //             $invoice_payment->receipt        = '';
    //             $invoice_payment->client_id      = $this->user->id;
    //             $invoice_payment->txn_id         = $payment_id;
    //             $invoice_payment->payment_status = $result['state'];
    //             $invoice_payment->save();

    //             if($result['state'] == 'approved')
    //             {
    //                 if(($invoice->getDueAmount() - $invoice_payment->amount) == 0)
    //                     {
    //                         $invoice->status = 'paid';
    //                         $invoice->save();
    //                     }else{
    //                         $invoice->status = 'partialy paid';
    //                         $invoice->save();
    //                     }

    //                 $user1 = $currentWorkspace->id;
    //                     $settings  = Utility::getPaymentSetting($user1);
    //                     $total_amount =  $invoice->getDueAmounts($invoice->id);
    //                     $client           = Client::find($invoice->client_id);
    //                     $project_name = Project::where('id', $invoice->project_id)->first();


    //                     $uArr = [
    //                         // 'user_name' => $user->name,
    //                         'project_name' => $project_name->name,
    //                         'company_name' => User::find($project_name->created_by)->name,
    //                         'invoice_id' => Utility::invoiceNumberFormat($invoice->id),
    //                         'client_name' => $client->name,
    //                         'total_amount' => "$total_amount",
    //                         'paid_amount' => $request->amount,
    //                     ];

    //                     if (isset($settings['invoicest_notificaation']) && $settings['invoicest_notificaation'] == 1) {

    //                         Utility::send_slack_msg('Invoice Status Updated', $user1 , $uArr);
    //                     }

    //                     if (isset($settings['telegram_invoicest_notificaation']) && $settings['telegram_invoicest_notificaation'] == 1) {
    //                         Utility::send_telegram_msg('Invoice Status Updated' ,$uArr, $user1);
    //                     }

    //                     //webhook
    //                     $module ='Invoice Status Updated';
    //                     // $webhook=  Utility::webhookSetting($module);
    //                     $webhook=  Utility::webhookSetting($module , $user1);

    //                     if($webhook)
    //                     {
    //                         $parameter = json_encode($invoice);
    //                         // 1 parameter is  URL , 2 parameter is data , 3 parameter is method
    //                         $status = Utility::WebhookCall($webhook['url'],$parameter,$webhook['method']);
    //                         // if($status == true)
    //                         // {
    //                         //     return redirect()->back()->with('success', __('Payment added Successfully!'));
    //                         // }
    //                         // else
    //                         // {
    //                         //     return redirect()->back()->with('error', __('Webhook call failed.'));
    //                         // }
    //                     }
    //                 return redirect()->back()->with('success', __('Payment added Successfully'));

    //                 if(\Auth::check())
    //                 {
    //                     return redirect()->route(
    //                         'client.invoices.show', [
    //                                                   $currentWorkspace->slug,
    //                                                   $invoice_id,
    //                                               ]
    //                     )->with('success', __('Payment added Successfully'));
    //                 }
    //                 else
    //                 {
    //                     return redirect()->route('pay.invoice',[$slug,\Illuminate\Support\Facades\Crypt::encrypt($invoice->id)])->with('success', __('Payment added Successfully'));
    //                 }

    //             }
    //         }
    //         catch(\Exception $e)
    //         {
    //             if(\Auth::check())
    //             {
    //                 return redirect()->route(
    //                     'client.invoices.show', [
    //                                               $currentWorkspace->slug,
    //                                               $invoice_id,
    //                                           ]
    //                 )->with('error', __('Transaction has been failed!'));
    //             }
    //             else
    //             {
    //                 return redirect()->route('pay.invoice',[$slug,\Illuminate\Support\Facades\Crypt::encrypt($invoice->id)])->with('error', __('Transaction has been failed!'));
    //             }

    //         }
    //     }
    //     else
    //     {
    //         return redirect()->back()->with('error', __('Permission denied.'));
    //     }
    // }
    public function setApiContext()
    {
        $payment_setting = Utility::getAdminPaymentSettings();

        if ($payment_setting['paypal_mode'] == 'live') {
            config([
                'paypal.live.client_id' => isset($payment_setting['paypal_client_id']) ? $payment_setting['paypal_client_id'] : '',
                'paypal.live.client_secret' => isset($payment_setting['paypal_secret_key']) ? $payment_setting['paypal_secret_key'] : '',
                'paypal.mode' => isset($payment_setting['paypal_mode']) ? $payment_setting['paypal_mode'] : '',
            ]);
        } else {
            config([
                'paypal.sandbox.client_id' => isset($payment_setting['paypal_client_id']) ? $payment_setting['paypal_client_id'] : '',
                'paypal.sandbox.client_secret' => isset($payment_setting['paypal_secret_key']) ? $payment_setting['paypal_secret_key'] : '',
                'paypal.mode' => isset($payment_setting['paypal_mode']) ? $payment_setting['paypal_mode'] : '',
            ]);
        }
        return $payment_setting;
    }


    public function setPaymentDetail_client($invoice_id)
    {
        $invoice = Invoice::find($invoice_id);
        if (Auth::user() != null) {
            $this->user = Auth::user();
        } else {
            $this->user = Client::where('id', $invoice->client_id)->first();
        }
        $payment_setting = Utility::getPaymentSetting($this->user->currentWorkspace->id);
        $payment_setting['settings']['mode'] = $this->user->currentWorkspace->paypal_mode;
        $payment_setting['client_id'] = $this->user->currentWorkspace->paypal_client_id;
        $payment_setting['secret_key'] = $this->user->currentWorkspace->paypal_secret_key;

        if ($payment_setting['settings']['mode'] == 'live') {
            config([
                'paypal.live.client_id' => isset($payment_setting['client_id']) ? $payment_setting['client_id'] : '',
                'paypal.live.client_secret' => isset($payment_setting['secret_key']) ? $payment_setting['secret_key'] : '',
                'paypal.mode' => isset($payment_setting['settings']['mode']) ? $payment_setting['settings']['mode'] : '',
            ]);
        } else {
            config([
                'paypal.sandbox.client_id' => isset($payment_setting['client_id']) ? $payment_setting['client_id'] : '',
                'paypal.sandbox.client_secret' => isset($payment_setting['secret_key']) ? $payment_setting['secret_key'] : '',
                'paypal.mode' => isset($payment_setting['settings']['mode']) ? $payment_setting['settings']['mode'] : '',
            ]);
        }
        return $payment_setting;
    }

    public function clientPayWithPaypal(Request $request, $slug, $invoice_id)
    {
        //$user = $this->user;
        $totalAmount = $request->amount;
        $user_auth = Auth::user();
        $client_keyword = isset($user_auth) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';
        $request->validate(['amount' => 'required|numeric|min:0']);

        $currentWorkspace = Utility::getWorkspaceBySlug_copylink('invoice', $invoice_id);
        if ($currentWorkspace) {
            $invoice = Invoice::find($invoice_id);

            if ($invoice) {
                if ($totalAmount > $invoice->getDueAmount()) {
                    return redirect()->back()->with('error', __('Invalid amount.'));
                } else {
                    $this->setPaymentDetail_client($invoice_id);
                    $provider = new PayPalClient;
                    $provider->setApiCredentials(config('paypal'));
                    $paypalToken = $provider->getAccessToken();
                    $response = $provider->createOrder([
                        "intent" => "CAPTURE",
                        "application_context" => [
                            "return_url" => route($client_keyword . 'get.payment.status', [$currentWorkspace->slug, $invoice->id]),
                            "cancel_url" => route($client_keyword . 'get.payment.status', [$currentWorkspace->slug, $invoice->id]),
                        ],
                        "purchase_units" => [
                            0 => [
                                "amount" => [
                                    "currency_code" => $currentWorkspace['currency_code'],
                                    "value" => $totalAmount
                                ]
                            ]
                        ]
                    ]);

                    if (isset($response['id']) && $response['id'] != null) {
                        // redirect to approve href
                        foreach ($response['links'] as $links) {
                            if ($links['rel'] == 'approve') {
                                return redirect()->away($links['href']);
                            }
                        }
                        return redirect()
                            ->route('invoice.show', \Crypt::encrypt($invoice->id))
                            ->with('error', 'Something went wrong.');
                    } else {
                        return redirect()
                            ->route('invoice.show', \Crypt::encrypt($invoice->id))
                            ->with('error', $response['message'] ?? 'Something went wrong.');
                    }
                    //return redirect()->route('invoice.show', \Crypt::encrypt($invoice_id))->back()->with('error', __('Unknown error occurred'));
                }
            } else {
                return redirect()->back()->with('error', __('Invoice Not Found.'));
            }
        } else {
            return redirect()->back()->with('error', __('Something is wrong'));
        }
    }

    public function clientGetPaymentStatus(Request $request, $slug, $invoice_id)
    {
        $user = Auth::user();
        $invoice = Invoice::find($invoice_id);
        $currentWorkspace = Utility::getWorkspaceBySlug_copylink('invoice', $invoice->id);
        if ($currentWorkspace && $invoice) {
            $this->setPaymentDetail_client($invoice_id);
            $user = $this->user;
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();
            $response = $provider->capturePaymentOrder($request['token']);
            $totalAmount = $response['purchase_units'][0]['payments']['captures'][0]['amount']['value'];
            $txn_id = $response['purchase_units'][0]['payments']['captures'][0]['id'];
            if (isset($response['status']) && $response['status'] == 'COMPLETED') {
                if ($response['status'] == 'COMPLETED') {
                    $status = 'succeeded';
                }
                try {
                    $order_id = strtoupper(str_replace('.', '', uniqid('', true)));

                    $invoice_payment = new InvoicePayment();
                    $invoice_payment->order_id = $order_id;
                    $invoice_payment->invoice_id = $invoice->id;
                    $invoice_payment->currency = $currentWorkspace->currency_code;
                    $invoice_payment->amount = $totalAmount;
                    $invoice_payment->payment_type = 'PAYPAL';
                    $invoice_payment->receipt = '';
                    $invoice_payment->client_id = $this->user->id;
                    $invoice_payment->txn_id = $txn_id;
                    $invoice_payment->payment_status = $status;
                    $invoice_payment->save();


                    if (($invoice->getDueAmount() - $invoice_payment->amount) == 0) {
                        $invoice->status = 'paid';
                        $invoice->save();
                    } else {
                        $invoice->status = 'partialy paid';
                        $invoice->save();
                    }
                    $total_amount = $invoice->getDueAmount();

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
                    }
                    if (\Auth::check()) {
                        return redirect()->route(
                            'client.invoices.show',
                            [
                                $currentWorkspace->slug,
                                $invoice_id,
                            ]
                        )->with('success', __('Payment Added Successfully.'));
                    } else {
                        return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)])->with('success', __('Payment added Successfully..'));
                    }

                } catch (\Exception $e) {
                    if (\Auth::check()) {
                        return redirect()->route(
                            'client.invoices.show',
                            [
                                $currentWorkspace->slug,
                                $invoice_id,
                            ]
                        )->with('error', __('Transaction has been failed!'));
                    } else {
                        return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)])->with('error', __('Transaction has been failed!'));
                    }

                }
            } else {
                if (\Auth::check()) {
                    return redirect()->route('client.invoices.show', [$currentWorkspace->slug, $invoice_id])->with('error', __('Payment failed'));
                } else {
                    return redirect()->route('pay.invoice', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)])->with('error', __('Transaction has been Faild.'));
                }
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

}
