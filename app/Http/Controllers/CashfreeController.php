<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\PlanOrder;
use App\Models\Plan;
use App\Models\Utility;
use App\Models\UserCoupon;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Client;
use App\Models\ProductVariantOption;
use App\Models\PurchasedProducts;
use App\Models\InvoicePayment;
use App\Models\Store;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use PhpParser\Node\Stmt\TryCatch;
use GuzzleHttp\Client as benefit_client;


class CashfreeController extends Controller
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
        config(
            [
                'services.cashfree.key' => isset($payment_setting['cashfree_api_key']) ? $payment_setting['cashfree_api_key'] : '',
                'services.cashfree.secret' => isset($payment_setting['cashfree_secret_key']) ? $payment_setting['cashfree_secret_key'] : '',
            ]
        );
    }

    public function invoicePayWithcashfree(Request $request, $slug, $invoice_id)
    {
        $invoice = Invoice::find($invoice_id);
        $this->setPaymentDetail_client($invoice_id);
        $currentWorkspace = Utility::getWorkspaceBySlug_copylink('invoice', $invoice_id);
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
                $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
                $url = config('services.cashfree.url');

                $headers = array(
                    "Content-Type: application/json",
                    "x-api-version: 2022-01-01",
                    "x-client-id: " . config('services.cashfree.key'),
                    "x-client-secret: " . config('services.cashfree.secret')
                );

                $data = json_encode([
                    'order_id' => $orderID,
                    'order_amount' => $get_amount,
                    "order_currency" => !empty($currentWorkspace->currency_code) ? $currentWorkspace->currency_code : 'USD',
                    "order_name" => Utility::invoiceNumberFormat($invoice->id),
                    "customer_details" => [
                        "customer_id" => 'user_' . $user->id,
                        "customer_name" => $user->name,
                        "customer_email" => $user->email,
                        "customer_phone" => '1234567890',
                    ],
                    "order_meta" => [
                        // "return_url" => route($client_keyword.'invoice.cashfree.success'). '?order_id={order_id}&order_token={order_token}' .''
                        "return_url" => route($client_keyword . 'invoice.cashfree.success', $slug) . '?order_id={order_id}&order_token={order_token}&invoice_id=' . $invoice->id . '&amount=' . $get_amount . ''
                    ]
                ]);

                try {
                    $curl = curl_init($url);
                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                    $resp = curl_exec($curl);
                    curl_close($curl);
                    return redirect()->to(json_decode($resp)->payment_link);
                } catch (\Throwable $th) {
                    return redirect()->back()->with('error', 'Currency Not Supported.Contact To Your Site Admin');
                }
            }
        }
    }

    public function getInvoicePaymentStatus(Request $request, $slug)
    {
        $invoice_id = $request->invoice_id;
        $currentWorkspace = Utility::getWorkspaceBySlug_copylink('invoice', $invoice_id);
        $user_auth = Auth::user();
        $client_keyword = isset($user_auth) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';
        $this->setPaymentDetail_client($invoice_id);
        $slug = $request->slug;

        if (!empty($invoice_id)) {
            $invoice = Invoice::find($invoice_id);
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            if (Auth::check()) {
                $user = Auth::user();
            } else {
                $user = User::where('id', $invoice->client_id)->first();
            }
            if ($invoice) {
                try {
                    $client = new benefit_client();
                    $response = $client->request('GET', config('services.cashfree.url') . '/' . $request->get('order_id') . '/settlements', [
                        'headers' => [
                            'accept' => 'application/json',
                            'x-api-version' => '2022-09-01',
                            "x-client-id" => config('services.cashfree.key'),
                            "x-client-secret" => config('services.cashfree.secret')
                        ],
                    ]);

                    $respons = json_decode($response->getBody());
                    if ($respons->order_id && $respons->cf_payment_id != NULL) {
                        $response = $client->request('GET', config('services.cashfree.url') . '/' . $respons->order_id . '/payments/' . $respons->cf_payment_id . '', [
                            'headers' => [
                                'accept' => 'application/json',
                                'x-api-version' => '2022-09-01',
                                'x-client-id' => config('services.cashfree.key'),
                                'x-client-secret' => config('services.cashfree.secret'),
                            ],
                        ]);
                        $info = json_decode($response->getBody());
                    }

                    if ($info->payment_status == "SUCCESS") {
                        $invoice_payment = new InvoicePayment();
                        $invoice_payment->order_id = $orderID;
                        $invoice_payment->invoice_id = $invoice->id;
                        $invoice_payment->currency = $currentWorkspace->currency_code;
                        $invoice_payment->amount = isset($request->amount) ? $request->amount : 0;
                        $invoice_payment->payment_type = 'Cashfree';
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
                        if (\Auth::check()) {
                            return redirect()->route(
                                'client.invoices.show',
                                [
                                    $slug,
                                    $invoice_id,
                                ]
                            )->with('error', __('Transaction fail!'));
                        } else {
                            return redirect()->route('pay.invoice', [$slug, Crypt::encrypt($invoice->id)])->with('success', __('Payment added Successfully'));

                        }
                    }
                } catch (\Exception $e) {
                    if (\Auth::check()) {
                        return redirect()->route(
                            'client.invoices.show',
                            [
                                $slug,
                                $invoice_id,
                            ]
                        )->with('error', __($e->getMessage()));
                    } else {
                        return redirect()->route('pay.invoice', [$slug, Crypt::encrypt($invoice->id)])->with('error', __($e->getMessage()));

                    }
                }

            } else {
                if (Auth::user()) {
                    return redirect()->route('invoices.show', $invoice_id)->with('error', __('Invoice not found'));
                } else {
                    $id = \Crypt::encrypt($invoice_id);
                    return redirect()->route('pay.invoice', $id)->with('error', __('Transaction fail!'));
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
