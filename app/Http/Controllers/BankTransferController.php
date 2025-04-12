<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Plan;
use App\Models\Coupon;
use App\Models\Utility;
use App\Models\UserCoupon;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\Client;
use Illuminate\Support\Facades\Storage;
use App\Models\Project;

class BankTransferController extends Controller
{
    public $email;
    public $is_enabled;
    public $currancy;
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
        $this->currancy = (isset($this->user->currentWorkspace->currency_code)) ? $this->user->currentWorkspace->currency_code : 'USD';
    }

    public function invoicePayWithBank($slug, $invoice_id, Request $request)
    {
        $validator = \Validator::make(
            $request->all(),
            [
                'payment_receipt' => 'required',
            ]
        );
        if ($validator->fails()) {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }

        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $invoiceID = \Illuminate\Support\Facades\Crypt::decrypt($invoice_id);
        $invoice = Invoice::find($invoiceID);
        // $user      = User::find($invoice->created_by);
        if (Auth::check()) {
            $user = \Auth::user();
        } else {
            $user = Client::where('id', $invoice->client_id)->first();
        }
        $this->setPaymentDetail_client($invoiceID);

        // $settings=Utility::settingsById($invoice->created_by);
        if ($invoice) {

            if ($request->payment_receipt) {
                $request->validate(
                    [
                        'payment_receipt' => 'required',
                    ]
                );
                $validation = [
                    // 'mimes:'.'png',
                    'max:' . '20480',
                ];
                $receipt = time() . '_' . 'receipt_image.png';
                $dir = 'uploads/invoice_receipt/';
                $path = Utility::upload_file($request, 'payment_receipt', $receipt, $dir, $validation);
                // if($path['flag'] == 1){
                //     $receipt = $path['url'];
                // }else{
                //     return redirect()->back()->with('error', __($path['msg']));
                // }
            }

            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));

            $invoice_payment = new InvoicePayment();
            $invoice_payment->order_id = $orderID;
            $invoice_payment->invoice_id = $invoice->id;
            $invoice_payment->currency = $currentWorkspace->currency_code;
            $invoice_payment->amount = $request->amount;
            $invoice_payment->payment_type = 'Bank Transfer';
            $invoice_payment->receipt = $receipt;
            $invoice_payment->client_id = $user->id;
            $invoice_payment->txn_id = '';
            $invoice_payment->payment_status = 'pending';
            $invoice_payment->save();

            return redirect()->back()->with('success', __('Invoice payment request send successfully.'));
        } else {
            return redirect()->back()->with('success', __('Invoice payment request send successfully.'));
        }
    }

    public function invoice_status_show($slug, $id)
    {
        $order = InvoicePayment::find($id);
        $currentWorkspace = Utility::getWorkspaceBySlug($slug);
        $admin_payment_setting = Utility::getPaymentSetting($currentWorkspace->id);

        return view('invoices.invoicestatus', compact('order', 'admin_payment_setting', 'currentWorkspace'));
    }

    public function invoicebankPaymentApproval(Request $request, $id)
    {
        $orders = InvoicePayment::find($id);
        $invoice = Invoice::find($orders->invoice_id);
        $user = User::find($orders->user_id);
        if ($request->payment_approval == '1') {
            $orders->update(
                [
                    'payment_status' => 'succeeded',
                ]
            );

            if (($invoice->getDueAmount()) == 0) {
                $invoice->status = 'paid';
                $invoice->save();
            } else {

                $invoice->status = 'partialy paid';
                $invoice->save();
            }

            return redirect()->back()->with('success', __('Payment Approved Successfully!'));
        } else {
            $orders->update([
                'payment_status' => 'Rejected',
            ]);
            return redirect()->back()->with('success', __('Payment Rejected'));
        }
    }
    public function invoice_payment_destroy($id)
    {
        $setting = Utility::getAdminPaymentSettings();
        $payments = InvoicePayment::find($id);
        if (Storage::disk($setting['storage_setting'])->exists('/uploads/invoice_receipt/' . $payments->receipt)) {
            Storage::disk($setting['storage_setting'])->delete('uploads/invoice_receipt/' . $payments->receipt);
        }
        $payments->delete();
        return redirect()->back()->with('success', __('payment Deleted Successfully!'));
    }
}
