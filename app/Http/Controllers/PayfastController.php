<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Plan;
use App\Models\UserCoupon;
use App\Models\Utility;
use App\Models\Invoice;
use App\Models\Client;
use App\Models\InvoicePayment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Redirect;
use App\Models\Project;
use App\Models\User;



class PayfastController extends Controller
{
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
    }

    public function generateSignature($data, $passPhrase = null)
    {

        $pfOutput = '';
        foreach ($data as $key => $val) {
            if ($val !== '') {
                $pfOutput .= $key . '=' . urlencode(trim($val)) . '&';
            }
        }

        $getString = substr($pfOutput, 0, -1);
        if ($passPhrase !== null) {
            $getString .= '&passphrase=' . urlencode(trim($passPhrase));
        }
        return md5($getString);
    }

    public function invoicePayWithpayfast(Request $request, $slug, $invoice_id)
    {


        $invoice = Invoice::find($invoice_id);
        $user1 = Auth::user();
        $user_auth = Auth::user();
        $client_keyword = isset($user1) ? (($user_auth->getGuard() == 'client') ? 'client.' : '') : '';

        if (Auth::user() != null) {
            $this->user = Auth::user();
        } else {
            $this->user = Client::where('id', $invoice->client_id)->first();
        }

        $payment_setting = Utility::getPaymentSetting($this->user->currentWorkspace->id);
        $pfHost = $payment_setting['payfast_mode'] == 'sandbox' ? 'sandbox.payfast.co.za' : 'www.payfast.co.za';


        $order_id = strtoupper(str_replace('.', '', uniqid('', true)));

        $success = Crypt::encrypt([
            'invoice_id' => $invoice->id,
            'order_id' => $order_id,
            'plan_amount' => $request->amount,
            'slug' => $this->user->currentWorkspace->slug
        ]);

        $data = array(
            // Merchant details
            'merchant_id' => !empty($payment_setting['payfast_merchant_id']) ? $payment_setting['payfast_merchant_id'] : '',
            'merchant_key' => !empty($payment_setting['payfast_merchant_key']) ? $payment_setting['payfast_merchant_key'] : '',
            'return_url' => route($client_keyword . 'invoice.payfast', $success),
            'cancel_url' => route($client_keyword . 'invoices.show', [$this->user->currentWorkspace->slug, $invoice->id]),
            'notify_url' => route($client_keyword . 'invoices.show', [$this->user->currentWorkspace->slug, $invoice->id]),
            // Buyer details
            'name_first' => $this->user->name,
            'name_last' => '',
            'email_address' => $this->user->email,
            // Transaction details
            'm_payment_id' => $order_id, //Unique payment ID to pass through to notify_url
            'amount' => number_format(sprintf('%.2f', $request->amount), 2, '.', ''),
            'item_name' => $order_id,
        );

        $passphrase = !empty($payment_setting['payfast_signature']) ? $payment_setting['payfast_signature'] : '';
        $signature = $this->generateSignature($data, $passphrase);
        $data['signature'] = $signature;

        $url = "https://$pfHost/eng/process";
        $fields_string = http_build_query($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function getInvoicePaymentStatus($success)
    {
        $data = Crypt::decrypt($success);
        $invoice = Invoice::find($data['invoice_id']);
        // $currentWorkspace = Utility::getWorkspaceBySlug($data['slug']);
        $currentWorkspace = Utility::getWorkspaceBySlug_copylink('invoice', $data['invoice_id']);


        if (Auth::user() != null) {
            $this->user = Auth::user();
        } else {
            $this->user = Client::where('id', $invoice['client_id'])->first();
        }

        $invoice_payment = new InvoicePayment();
        $invoice_payment->order_id = $data['order_id'];
        $invoice_payment->invoice_id = $data['invoice_id'];
        $invoice_payment->currency = isset($currentWorkspace->currency_code) ? $currentWorkspace->currency_code : 'en';
        $invoice_payment->amount = $data['plan_amount'];
        $invoice_payment->payment_type = 'PayFast';
        $invoice_payment->receipt = '';
        $invoice_payment->client_id = $this->user->id;
        $invoice_payment->txn_id = $data['order_id'];
        $invoice_payment->payment_status = 'approved';
        $invoice_payment->save();

        if ($invoice) {
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
                'paid_amount' => $data['plan_amount'],
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

            //return redirect()->back()->with('success', __('Payment added Successfully'));

            if (Auth::check()) {
                return redirect()->route(
                    'client.invoices.show',
                    [
                        $data['slug'],
                        $invoice->id,
                    ]
                )->with('success', __('Payment added Successfully'));
            } else {
                return redirect()->route('pay.invoice', [$data['slug'], \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)])->with('success', __('Payment added Successfully'));
            }
        }
    }
}
