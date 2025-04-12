<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Coupon;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Order;
use App\Models\Project;
use App\Models\User;
use App\Models\UserCoupon;
use App\Models\Utility;
use App\Models\Workspace;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class PowertranzPaymentController extends Controller
{
    //
    private $currencyArray = [
        "USD" => "840",
        "EUR" => "978",
        "GBP" => "826",
        "CAD" => "124",
        "AUD" => "036",
        "JPY" => "392",
        "CHF" => "756",
        "SEK" => "752",
        "NOK" => "578",
        "DKK" => "208",
        "NZD" => "554",
        "SGD" => "702",
        "HKD" => "344",
        "ZAR" => "710",
        "MXN" => "484",
        "BRL" => "986",
        "INR" => "356",
        "CNY" => "156",
        "RUB" => "643",
    ];

    public function curl_response($data, $request, $arr = [])
    {
        if (Auth::user()) {
            $user = Auth::user();
        } else {
            $user = User::find($arr['data']->created_by ?? $arr['data']->client_id);
        }
        $expiryDate = explode('/', $request->expiryDate);
        $expiryDate = $expiryDate[1] . $expiryDate[0];

        if (array_key_exists($arr['admin_currency'], $this->currencyArray)) {
            $currency_code = $this->currencyArray[$arr['admin_currency']];
        } else {
            $json_response = [
                'status'  => false,
                'message' => __('Something went wrong. Please try again.')
            ];
            return $json_response;
        }

        $headers = [
            "Accept: application/json",
            "PowerTranz-PowerTranzId:" . $arr['powertranz_id'] . "",
            "PowerTranz-PowerTranzPassword:" . $arr['powertranz_password'] . "",
            "Content-Type: application/json; charset=utf-8",
            "Host: staging.ptranz.com",
            "Connection: Keep-Alive"
        ];

        $fields = [
            "TotalAmount"   => (int)$data['amount'],
            "CurrencyCode"  => $currency_code,
            "ThreeDSecure"  => true,
            "Source" => [
                "CardPan"        => $request->cardNumber,
                "CardCvv"        => $request->cvv,
                "CardExpiration" => $expiryDate,
                "CardholderName" => $request->card_name
            ],
            "OrderIdentifier"   => $data['order_id'],
            "BillingAddress"    => [
                "FirstName"     => $user->name,
                "EmailAddress"  => $user->email,
                "PhoneNumber"   => $user->mobile_no ?? 1234567890
            ],
            "AddressMatch" => false,
            "ExtendedData" => [
                "ThreeDSecure" => [
                    "ChallengeWindowSize" => 4,
                    "ChallengeIndicator"  => "01"
                ],
                "MerchantResponseUrl" => $arr['status_url']
            ]
        ];

        if ($arr['powertranz_mode'] == 'live') {
            $auth_url = $arr['production_url'] . "/api/spi/auth";
        } else {
            $auth_url = "https://staging.ptranz.com/api/spi/auth";
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $auth_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch)) {
            $json_response = [
                'status'  => false,
                'message' => __('Something went wrong. Please try again.')
            ];
            return $json_response;
        }
        curl_close($ch);

        $json_response = json_decode($response, true);
        if ($http_status !== 200) {
            $json_response = [
                'status'  => false,
                'message' => __('Something went wrong. Please try again.')
            ];
            return $json_response;
        }
        return $json_response;
    }

    // Invoice Payment
    public function invoicePayWithPowerTranz(Request $request, $slug, $invoice_id)
    {
        $invoice          = Invoice::find($invoice_id);
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $amount           = $request->amount;

        $payment_setting        = Utility::getPaymentSetting($currentWorkspace->id);
        $powertranz_id          = isset($payment_setting['powertranz_merchant_id']) ? $payment_setting['powertranz_merchant_id'] : '';
        $powertranz_password    = isset($payment_setting['powertranz_processing_password']) ? $payment_setting['powertranz_processing_password'] : '';
        $production_url         = isset($payment_setting['production_url']) ? $payment_setting['production_url'] : '';
        $powertranz_mode        = isset($payment_setting['powertranz_mode']) ? $payment_setting['powertranz_mode'] : 'sandbox';
        $currency               = $currentWorkspace->currency_code ? $currentWorkspace->currency_code : 'USD';
        $orderId                = strtoupper(str_replace('.', '', uniqid('', true)));

        $user1 = Auth::user();
        $client_keyword = isset($user1) ? (($user1->getGuard() == 'client') ? 'client.' : '') : '';

        if (array_key_exists($currency, $this->currencyArray)) {
            $currency_code = $this->currencyArray[$currency];
        } else {
            return redirect()->back()->with('error', __('Currency not supported'));
        }

        if ($invoice) {
            try {
                $session                        = $request->toArray();
                $session['order_id']            = $orderId;
                $session['invoice_id']          = $invoice_id;
                $session['amount']              = $amount;
                $session['currentWorkspace']    = $currentWorkspace;
                $request->session()->put($orderId, $session);

                if (!empty($powertranz_id)) {
                    $pay = [
                        'powertranz_merchant_id'           => $powertranz_id,
                        'powertranz_processing_password'   => $powertranz_password,
                        'order_id'                         => $orderId,
                        'powertranz_mode'                  => $powertranz_mode,
                        'amount'                           => $request->amount,
                        'action'                           => route($client_keyword . 'powertranz.invoice.response', [$slug, Crypt::encrypt($request->all())]),
                        'back_url'                         => route('invoices.index', ['slug' => $slug]),
                        'currency'                         => $currency,
                    ];
                    return view('invoices.powertranz-payment', compact('pay'));
                }
            } catch (\Exception $e) {
                return redirect()->back()->with('error', __($e->getMessage()));
            }
        } else {
            return redirect()->back()->with('error', __('Permission denied.'));
        }
    }

    public function InvoiceResponse(Request $request, $slug)
    {
        $data               = json_decode($request->data, true);
        $session            = session()->get($data['order_id']);
        $data               = array_merge($session);
        $invoice_id         = $session['invoice_id'];
        $invoice            = Invoice::find($invoice_id);
        $currentWorkspace   = $session['currentWorkspace'];
        $get_amount         = $session['amount'];
        $route              = 'pay.invoice';
        $user1              = Auth::user();
        $client_keyword     = isset($user1) ? (($user1->getGuard() == 'client') ? 'client.' : '') : '';

        try {
            $payment_setting               = Utility::getPaymentSetting($currentWorkspace->id);
            $arr['powertranz_id']          = isset($payment_setting['powertranz_merchant_id']) ? $payment_setting['powertranz_merchant_id']  : '';
            $arr['powertranz_password']    = isset($payment_setting['powertranz_processing_password']) ? $payment_setting['powertranz_processing_password'] : '';
            $arr['production_url']         = isset($payment_setting['production_url']) ? $payment_setting['production_url'] : '';
            $arr['powertranz_mode']        = isset($payment_setting['powertranz_mode']) ? $payment_setting['powertranz_mode'] : 'sandbox';
            $arr['admin_currency']         = isset($currentWorkspace->currency_code) ? $currentWorkspace->currency_code : 'USD';
            $arr['data']                   = $invoice;
            $arr['status_url']             = route($client_keyword . 'invoice.powertranz.status', [$invoice->id, $get_amount, $slug, 'success' => 1, 'data' => $data]);

            $json_response = $this->curl_response($data, $request, $arr);

            if (isset($json_response['status']) && $json_response['status'] == false) {
                return redirect()->route($route, [$slug, Crypt::encrypt($invoice_id)])->with('error', $json_response);
            }

            if (isset($json_response['Approved']) && $json_response['Approved'] === false) {
                if (isset($json_response['RedirectData'])) {
                    return response($json_response['RedirectData']);
                } else {
                    return redirect()->route($route, [$slug, Crypt::encrypt($invoice_id)])->with('error', __('Something went wrong. Please try again.'));
                }
            } else {
                return redirect()->route($route, [$slug, Crypt::encrypt($invoice_id)])->with('error', __('Something went wrong. Please try again.'));
            }
        } catch (\Exception $e) {
            return redirect()->route($route, [$slug, Crypt::encrypt($invoice_id)])->with('error', $e->getMessage() ?? __('Something went wrong. Please try again.'));
        }
    }

    public function invoiceGetPowertranzStatus(Request $request, $id, $amt = null, $slug)
    {
        // dd(Auth::check()); 
        $response           = json_decode($request->Response, true);
        $orderID            = $response['OrderIdentifier'];
        $session            = (object) $request->session()->get($orderID);
        $invoice_id         = $session->invoice_id;
        $amount             = $session->amount;
        $invoice            = Invoice::find($invoice_id);
        $currentWorkspace   = Utility::getWorkspaceBySlug_copylink('invoice', $invoice->id);
        $user               = User::find($invoice->client_id);
        $request->session()->forget($orderID);

        $payment_setting    = Utility::getPaymentSetting($currentWorkspace->id);
        $powertranz_mode    = isset($payment_setting['powertranz_mode']) ? $payment_setting['powertranz_mode'] : 'sandbox';
        $production_url     = isset($payment_setting['production_url']) ? $payment_setting['production_url'] : '';

        if ($powertranz_mode == 'live') {
            $pay_url = $production_url . "/api/spi/Payment";
        } else {
            $pay_url = "https://staging.ptranz.com/api/spi/Payment";
        }

        $response = Http::withHeaders([
            "Accept: text/plain",
            "Content-Type: application/json-patch+json",
            "Host: staging.ptranz.com",
            "Connection: Keep-Alive"
        ])->post($pay_url, $request->SpiToken);
        $json_response = json_decode($response, true);

        if ($response->successful() && $json_response['Approved'] == "true" && $json_response['IsoResponseCode'] == "00") {
            if (!empty($invoice)) {
                $invoice_payment                 = new InvoicePayment();
                $invoice_payment->order_id       = $orderID;
                $invoice_payment->invoice_id     = $invoice_id;
                $invoice_payment->currency       = $currentWorkspace->currency_code;
                $invoice_payment->amount         = $amount;
                $invoice_payment->payment_type   = __('PowerTranz');
                $invoice_payment->client_id      = $user->id;
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
        }else {
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
