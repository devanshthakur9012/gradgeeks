@extends('layouts.invoicepayheader')

@section('page-title')
    {{ __('Invoices') }}
@endsection

@section('action-button')
    @if ($invoice->getDueAmounts($invoice->id) > 0)
        @if (
            $currentWorkspace->is_stripe_enabled == 1 ||
                $currentWorkspace->is_paypal_enabled == 1 ||
                (isset($paymentSetting['is_bank_enabled']) && $paymentSetting['is_bank_enabled'] == 'on') ||
                (isset($paymentSetting['is_paystack_enabled']) && $paymentSetting['is_paystack_enabled'] == 'on') ||
                (isset($paymentSetting['is_flutterwave_enabled']) && $paymentSetting['is_flutterwave_enabled'] == 'on') ||
                (isset($paymentSetting['is_razorpay_enabled']) && $paymentSetting['is_razorpay_enabled'] == 'on') ||
                (isset($paymentSetting['is_mercado_enabled']) && $paymentSetting['is_mercado_enabled'] == 'on') ||
                (isset($paymentSetting['is_paytm_enabled']) && $paymentSetting['is_paytm_enabled'] == 'on') ||
                (isset($paymentSetting['is_mollie_enabled']) && $paymentSetting['is_mollie_enabled'] == 'on') ||
                (isset($paymentSetting['is_skrill_enabled']) && $paymentSetting['is_skrill_enabled'] == 'on') ||
                (isset($paymentSetting['is_coingate_enabled']) && $paymentSetting['is_coingate_enabled'] == 'on') ||
                (isset($paymentSetting['is_paymentwall_enabled']) && $paymentSetting['is_paymentwall_enabled'] == 'on') ||
                (isset($paymentSetting['is_toyyibpay_enabled']) && $paymentSetting['is_toyyibpay_enabled'] == 'on') ||
                (isset($paymentSetting['is_payfast_enabled']) && $paymentSetting['is_payfast_enabled'] == 'on') ||
                (isset($paymentSetting['is_iyzipay_enabled']) && $paymentSetting['is_iyzipay_enabled'] == 'on') ||
                (isset($paymentSetting['is_sspay_enabled']) && $paymentSetting['is_sspay_enabled'] == 'on') ||
                (isset($paymentSetting['is_paytab_enabled']) && $paymentSetting['is_paytab_enabled'] == 'on') ||
                (isset($paymentSetting['is_benefit_enabled']) && $paymentSetting['is_benefit_enabled'] == 'on') ||
                (isset($paymentSetting['is_cashfree_enabled']) && $paymentSetting['is_cashfree_enabled'] == 'on') ||
                (isset($paymentSetting['is_aamarpay_enabled']) && $paymentSetting['is_aamarpay_enabled'] == 'on') ||
                (isset($paymentSetting['is_paytr_enabled']) && $paymentSetting['is_paytr_enabled'] == 'on') ||
                (isset($paymentSetting['is_midtrans_enabled']) && $paymentSetting['is_midtrans_enabled'] == 'on') ||
                (isset($paymentSetting['is_xendit_enabled']) && $paymentSetting['is_xendit_enabled'] == 'on') ||
                (isset($paymentSetting['is_yookassa_enabled']) && $paymentSetting['is_yookassa_enabled'] == 'on') ||
                (isset($paymentSetting['is_paiementpro_enabled']) && $paymentSetting['is_paiementpro_enabled'] == 'on') ||
                (isset($paymentSetting['is_nepalste_enabled']) && $paymentSetting['is_nepalste_enabled'] == 'on') ||
                (isset($paymentSetting['is_cinetpay_enabled']) && $paymentSetting['is_cinetpay_enabled'] == 'on') ||
                (isset($paymentSetting['is_fedapay_enabled']) && $paymentSetting['is_fedapay_enabled'] == 'on') ||
                (isset($paymentSetting['is_payhere_enabled']) && $paymentSetting['is_payhere_enabled'] == 'on') ||
                (isset($paymentSetting['is_powertranz_enabled']) && $paymentSetting['is_powertranz_enabled'] == 'on') ||
                (isset($paymentSetting['is_payu_enabled']) && $paymentSetting['is_payu_enabled'] == 'on'))
            <a href="#" data-toggle="modal" data-bs-toggle="tooltip" data-bs-original-title="{{ __('Pay Now') }}"
                data-size="lg" data-target="#paymentModal" class="btn btn-sm mx-1   btn-primary">
                <i class="ti ti-doller px-1"> $ </i>
            </a>
        @endif
    @endif

    <button type="button" class="btn btn-sm btn-primary">
        <a href="{{ route('client.invoice.print', [$currentWorkspace->slug, \Illuminate\Support\Facades\Crypt::encryptString($invoice->id)]) }}"
            class="text_white">
            <i class="ti ti-printer text-white"></i>
        </a>
    </button>
@endsection
@section('content')
    <div class="row">
        <div class="card" id="printTable">
            <div class="card-header">
                <h5 class="" style=" left: -12px !important;">
                    {{ App\Models\Utility::invoiceNumberFormat($invoice->invoice_id) }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row ">
                    <div class="col-md-3 ">
                        <div class="invoice-contact">
                            <div class="invoice-box row">
                                <div class="col-sm-12">
                                    <h6>{{ __('From') }}:</h6>
                                    @if ($currentWorkspace->company)
                                        <h6 class="m-0">{{ $currentWorkspace->company }}</h6>
                                    @endif
                                    @if ($currentWorkspace->address)
                                        {{ $currentWorkspace->address }},<br>
                                    @endif
                                    @if ($currentWorkspace->city)
                                        {{ $currentWorkspace->city }},
                                    @endif
                                    @if ($currentWorkspace->state)
                                        {{ $currentWorkspace->state }},
                                    @endif
                                    @if ($currentWorkspace->zipcode)
                                        -{{ $currentWorkspace->zipcode }},<br>
                                    @endif
                                    @if ($currentWorkspace->country)
                                        {{ $currentWorkspace->country }},<br>
                                    @endif
                                    @if ($currentWorkspace->telephone)
                                        {{ $currentWorkspace->telephone }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-xl-3  invoice-client-info">
                        <div class="invoice-contact">
                            <div class="invoice-box row">
                                <div class="col-sm-12">
                                    <h6>{{ __('To') }}:</h6>
                                    @if ($invoice->client)
                                        <h6 class="m-0">{{ $invoice->client->name }}</h6>
                                        {{ $invoice->client->email }}<br>
                                        @if ($invoice->client)
                                            @if ($invoice->client->address)
                                                {{ $invoice->client->address }},
                                                <br>
                                            @endif
                                            @if ($invoice->client->city)
                                                {{ $invoice->client->city }},
                                            @endif
                                            @if ($invoice->client->state)
                                                {{ $invoice->client->state }},
                                            @endif
                                            @if ($invoice->client->zipcode)
                                                -{{ $invoice->client->zipcode }},<br>
                                            @endif
                                            @if ($invoice->client->country)
                                                {{ $invoice->client->country }},<br>
                                            @endif
                                            @if ($invoice->client->telephone)
                                                {{ $invoice->client->telephone }}
                                            @endif
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-xl-3  invoice-client-info">
                        <div class="invoice-contact">
                            <div class="col-sm-12">
                                <h6 class="pb-4">Description :</h6>
                                <table class="table table-responsive invoice-table invoice-order table-borderless">
                                    <tbody style="padding-bottom: 0px  !important; font-size: 15px !important;">
                                        <tr>
                                            <td style="padding-bottom: 0px  !important; font-size: 15px !important;">
                                                <b>{{ __('Project') }} :</b>
                                                {{ $invoice->project->name }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding-bottom: 0px  !important; font-size: 15px !important;">
                                                <b>{{ __('Issue Date') }} :</b>
                                                {{ App\Models\Utility::dateFormat($invoice->issue_date) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            @if ($invoice->status == 'sent')
                                                <td style="padding-bottom: 0px  !important; font-size: 15px !important;">
                                                    <b>{{ __('Status') }} :</b>
                                                    <span
                                                        class="p-2 px-3 rounded badge bg-warning">{{ __('Sent') }}</span>
                                                </td>
                                            @elseif($invoice->status == 'paid')
                                                <td style="padding-bottom: 0px  !important; font-size: 15px !important;">
                                                    <b>{{ __('Status') }} :</b>
                                                    <span
                                                        class="p-2 px-3 rounded badge bg-success">{{ __('Paid') }}</span>
                                                </td>
                                            @elseif($invoice->status == 'canceled')
                                                <td style="padding-bottom: 0px  !important; font-size: 15px !important;">
                                                    <b>{{ __('Status') }} :</b>
                                                    <span
                                                        class="p-2 px-3 rounded badge bg-danger">{{ __('Canceled') }}</span>
                                                </td>
                                            @endif
                                        </tr>
                                        <tr>
                                            <td style="padding-bottom: 0px  !important; font-size: 15px !important;">
                                                <b>{{ __('Due Date') }} :</b>
                                                {{ App\Models\Utility::dateFormat($invoice->due_date) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @if ($currentWorkspace->qr_display == 'on')
                        <div class="col-md-1  qr_code ">
                            <div class="text-end mr-3">
                                {!! DNS2D::getBarcodeHTML(
                                    route('pay.invoice', [$currentWorkspace->slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)]),
                                    'QRCODE',
                                    2,
                                    2,
                                ) !!}
                            </div>
                        </div>
                    @endif
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <h5 class="px-2 py-2 mb-4"><b>{{ __('Order Summary') }}</b></h5>
                        <div class="table-responsive">
                            <table class="table invoice-detail-table">
                                <thead>
                                    <tr class="thead-default">
                                        <th>#</th>
                                        <th>{{ __('Item') }}</th>
                                        <th>{{ __('Totals') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($invoice->items as $key => $item)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $item->task ? $item->task->title : '' }}-
                                                <b>{{ $item->task ? $item->task->project->name : '' }}</b>
                                            </td>
                                            <td>{{ $currentWorkspace->priceFormat($item->price * $item->qty) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="invoice-total">
                            <table class="table table-responsive invoice-table ">
                                <tbody>
                                    <tr>
                                        <th>{{ __('Subtotal') }} :</th>
                                        <td>{{ $currentWorkspace->priceFormat($invoice->getSubTotal()) }}</td>
                                    </tr>
                                    @if ($invoice->discount)
                                        <tr>
                                            <th>{{ __('Discount') }} :</th>
                                            <td>{{ $currentWorkspace->priceFormat($invoice->discount) }}</td>
                                        </tr>
                                    @endif
                                    @if ($invoice->tax)
                                        <tr>
                                            <th>
                                                {{ __('Tax') }} {{ $invoice->tax->name }}
                                                ({{ $invoice->tax->rate }}%):
                                            </th>
                                            <td>{{ $currentWorkspace->priceFormat($invoice->getTaxTotal()) }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th class="text-primary m-r-10 ">{{ __('Total') }} : </th>
                                        <td class="text-primary m-r-10 px-2">
                                            {{ $currentWorkspace->priceFormat($invoice->getTotal()) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="text-primary m-r-10 ">{{ __('Due Amount') }} : </th>
                                        <td class="text-primary m-r-10 px-2">
                                            {{ $currentWorkspace->priceFormat($invoice->getDueAmount()) }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                @php
                    $payments = App\Models\InvoicePayment::where('invoice_id', $invoice->id)
                        ->orderBy('updated_at', 'DESC')
                        ->get();
                @endphp

                @if ($payments)
                    <div class="row">
                        <div class="col-sm-12">
                            <h5 class="px-2 py-2 mb-4"><b>{{ __('Payments') }}</b></h5>
                            <div class="table-responsive">
                                <table class="table invoice-detail-table">
                                    <thead>
                                        <tr class="thead-default">
                                            <th>#</th>
                                            <th>{{ __('Amount') }}</th>
                                            <th>{{ __('Currency') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Payment Type') }}</th>
                                            <th>{{ __('Date') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($payments as $key => $payment)
                                            <tr>
                                                <td>{{ $payment->order_id }}</td>
                                                <td>{{ $currentWorkspace->priceFormat($payment->amount) }}</td>
                                                <td>{{ strtoupper($payment->currency) }}</td>
                                                <td>
                                                    @if ($payment->payment_status == 'succeeded' || $payment->payment_status == 'approved')
                                                        <i class="fas fa-circle text-success"></i>
                                                        {{ __(ucfirst($payment->payment_status)) }}
                                                    @else
                                                        <i class="fas fa-circle text-danger"></i>
                                                        {{ __(ucfirst($payment->payment_status)) }}
                                                    @endif
                                                </td>
                                                <td>{{ __($payment->payment_type) }}</td>
                                                <td>{{ App\Models\Utility::dateFormat($payment->created_at) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <!-- [ Invoice ] end -->
    </div>

    @if ($invoice->getDueAmounts($invoice->id) > 0)
        <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> {{ __('Add Payment') }}</h5>
                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="card-box">
                            @if (
                                $currentWorkspace->is_stripe_enabled == 1 ||
                                    $currentWorkspace->is_paypal_enabled == 1 ||
                                    (isset($paymentSetting['is_bank_enabled']) && $paymentSetting['is_bank_enabled'] == 'on') ||
                                    (isset($paymentSetting['is_paystack_enabled']) && $paymentSetting['is_paystack_enabled'] == 'on') ||
                                    (isset($paymentSetting['is_flutterwave_enabled']) && $paymentSetting['is_flutterwave_enabled'] == 'on') ||
                                    (isset($paymentSetting['is_razorpay_enabled']) && $paymentSetting['is_razorpay_enabled'] == 'on') ||
                                    (isset($paymentSetting['is_mercado_enabled']) && $paymentSetting['is_mercado_enabled'] == 'on') ||
                                    (isset($paymentSetting['is_paytm_enabled']) && $paymentSetting['is_paytm_enabled'] == 'on') ||
                                    (isset($paymentSetting['is_mollie_enabled']) && $paymentSetting['is_mollie_enabled'] == 'on') ||
                                    (isset($paymentSetting['is_skrill_enabled']) && $paymentSetting['is_skrill_enabled'] == 'on') ||
                                    (isset($paymentSetting['is_coingate_enabled']) && $paymentSetting['is_coingate_enabled'] == 'on') ||
                                    (isset($paymentSetting['is_paymentwall_enabled']) && $paymentSetting['is_paymentwall_enabled'] == 'on') ||
                                    (isset($paymentSetting['is_toyyibpay_enabled']) && $paymentSetting['is_toyyibpay_enabled'] == 'on') ||
                                    (isset($paymentSetting['is_payfast_enabled']) && $paymentSetting['is_payfast_enabled'] == 'on') ||
                                    (isset($paymentSetting['is_iyzipay_enabled']) && $paymentSetting['is_iyzipay_enabled'] == 'on') ||
                                    (isset($paymentSetting['is_sspay_enabled']) && $paymentSetting['is_sspay_enabled'] == 'on') ||
                                    (isset($paymentSetting['is_paytab_enabled']) && $paymentSetting['is_paytab_enabled'] == 'on') ||
                                    (isset($paymentSetting['is_benefit_enabled']) && $paymentSetting['is_benefit_enabled'] == 'on') ||
                                    (isset($paymentSetting['is_cashfree_enabled']) && $paymentSetting['is_cashfree_enabled'] == 'on') ||
                                    (isset($paymentSetting['is_aamarpay_enabled']) && $paymentSetting['is_aamarpay_enabled'] == 'on') ||
                                    (isset($paymentSetting['is_paytr_enabled']) && $paymentSetting['is_paytr_enabled'] == 'on') ||
                                    (isset($paymentSetting['is_midtrans_enabled']) && $paymentSetting['is_midtrans_enabled'] == 'on') ||
                                    (isset($paymentSetting['is_xendit_enabled']) && $paymentSetting['is_xendit_enabled'] == 'on') ||
                                    (isset($paymentSetting['is_yookassa_enabled']) && $paymentSetting['is_yookassa_enabled'] == 'on') ||
                                    (isset($paymentSetting['is_paiementpro_enabled']) && $paymentSetting['is_paiementpro_enabled'] == 'on') ||
                                    (isset($paymentSetting['is_nepalste_enabled']) && $paymentSetting['is_nepalste_enabled'] == 'on') ||
                                    (isset($paymentSetting['is_cinetpay_enabled']) && $paymentSetting['is_cinetpay_enabled'] == 'on') ||
                                    (isset($paymentSetting['is_fedapay_enabled']) && $paymentSetting['is_fedapay_enabled'] == 'on') ||
                                    (isset($paymentSetting['is_payhere_enabled']) && $paymentSetting['is_payhere_enabled'] == 'on') ||
                                    (isset($paymentSetting['is_powertranz_enabled']) && $paymentSetting['is_powertranz_enabled'] == 'on') ||
                                    (isset($paymentSetting['is_payu_enabled']) && $paymentSetting['is_payu_enabled'] == 'on'))
                                <ul class="nav nav-tabs bordar_styless py-3">
                                    @if (isset($paymentSetting['is_bank_enabled']) && $paymentSetting['is_bank_enabled'] == 'on')
                                        <li>
                                            <a data-toggle="tab" href="#bank-payment" role="tab" class="active"
                                                aria-controls="bank" aria-selected="false">{{ __('Bank Transfer') }}</a>
                                        </li>
                                    @endif
                                    @if ($currentWorkspace->is_stripe_enabled == 1)
                                        <li>
                                            <a data-toggle="tab" href="#stripe-payment" class="">
                                                {{ __('Stripe') }}</a>
                                        </li>
                                    @endif
                                    @if ($currentWorkspace->is_paypal_enabled == 1)
                                        <li>
                                            <a data-toggle="tab" href="#paypal-payment" class="">
                                                {{ __('Paypal') }}</a>
                                        </li>
                                    @endif
                                    @if (isset($paymentSetting['is_paystack_enabled']) && $paymentSetting['is_paystack_enabled'] == 'on')
                                        <li>
                                            <a data-toggle="tab" href="#paystack-payment" role="tab"
                                                aria-controls="paystack" aria-selected="false">{{ __('Paystack') }}</a>
                                        </li>
                                    @endif
                                    @if (isset($paymentSetting['is_flutterwave_enabled']) && $paymentSetting['is_flutterwave_enabled'] == 'on')
                                        <li>
                                            <a data-toggle="tab" href="#flutterwave-payment" role="tab"
                                                aria-controls="flutterwave"
                                                aria-selected="false">{{ __('Flutterwave') }}</a>
                                        </li>
                                    @endif
                                    @if (isset($paymentSetting['is_razorpay_enabled']) && $paymentSetting['is_razorpay_enabled'] == 'on')
                                        <li>
                                            <a data-toggle="tab" href="#razorpay-payment" role="tab"
                                                aria-controls="razorpay" aria-selected="false">{{ __('Razorpay') }}</a>
                                        </li>
                                    @endif
                                    @if (isset($paymentSetting['is_mercado_enabled']) && $paymentSetting['is_mercado_enabled'] == 'on')
                                        <li class="pt-3">
                                            <a data-toggle="tab" href="#mercado-payment" role="tab"
                                                aria-controls="mercado"
                                                aria-selected="false">{{ __('Mercado Pago') }}</a>
                                        </li>
                                    @endif
                                    @if (isset($paymentSetting['is_paytm_enabled']) && $paymentSetting['is_paytm_enabled'] == 'on')
                                        <li class="pt-3">
                                            <a data-toggle="tab" href="#paytm-payment" role="tab"
                                                aria-controls="paytm" aria-selected="false">{{ __('Paytm') }}</a>
                                        </li>
                                    @endif

                                    @if (isset($paymentSetting['is_mollie_enabled']) && $paymentSetting['is_mollie_enabled'] == 'on')
                                        <li class="pt-3">
                                            <a data-toggle="tab" href="#mollie-payment" role="tab"
                                                aria-controls="mollie" aria-selected="false">{{ __('Mollie') }}</a>
                                        </li>
                                    @endif
                                    @if (isset($paymentSetting['is_skrill_enabled']) && $paymentSetting['is_skrill_enabled'] == 'on')
                                        <li class="pt-3">
                                            <a data-toggle="tab" href="#skrill-payment" role="tab"
                                                aria-controls="skrill" aria-selected="false">{{ __('Skrill') }}</a>
                                        </li>
                                    @endif
                                    @if (isset($paymentSetting['is_coingate_enabled']) && $paymentSetting['is_coingate_enabled'] == 'on')
                                        <li class="pt-3">
                                            <a data-toggle="tab" href="#coingate-payment" role="tab"
                                                aria-controls="coingate" aria-selected="false">{{ __('CoinGate') }}</a>
                                        </li>
                                    @endif
                                    @if (isset($paymentSetting['is_paymentwall_enabled']) && $paymentSetting['is_paymentwall_enabled'] == 'on')
                                        <li class="pt-3">
                                            <a data-toggle="tab" href="#paymentwall-payment" role="tab"
                                                aria-controls="coingate"
                                                aria-selected="false">{{ __('Paymentwall') }}</a>
                                        </li>
                                    @endif
                                    @if (isset($paymentSetting['is_toyyibpay_enabled']) && $paymentSetting['is_toyyibpay_enabled'] == 'on')
                                        <li class="pt-3">
                                            <a data-toggle="tab" href="#toyyibpay-payment" role="tab"
                                                aria-controls="coingate" aria-selected="false">{{ __('Toyyibpay') }}</a>
                                        </li>
                                    @endif
                                    @if (isset($paymentSetting['is_payfast_enabled']) && $paymentSetting['is_payfast_enabled'] == 'on')
                                        <li class="pt-3">
                                            <a data-toggle="tab" href="#payfast-payment" role="tab"
                                                aria-controls="payfast" aria-selected="false">{{ __('Payfast') }}</a>
                                        </li>
                                    @endif
                                    @if (isset($paymentSetting['is_iyzipay_enabled']) && $paymentSetting['is_iyzipay_enabled'] == 'on')
                                        <li class="pt-3">
                                            <a data-toggle="tab" href="#iyzipay-payment" role="tab"
                                                aria-controls="iyzipay" aria-selected="false">{{ __('Iyzipay') }}</a>
                                        </li>
                                    @endif
                                    @if (isset($paymentSetting['is_sspay_enabled']) && $paymentSetting['is_sspay_enabled'] == 'on')
                                        <li class="pt-3">
                                            <a data-toggle="tab" href="#sspay-payment" role="tab"
                                                aria-controls="sspay" aria-selected="false">{{ __('Sspay') }}</a>
                                        </li>
                                    @endif
                                    @if (isset($paymentSetting['is_paytab_enabled']) && $paymentSetting['is_paytab_enabled'] == 'on')
                                        <li class="pt-3">
                                            <a data-toggle="tab" href="#paytab-payment" role="tab"
                                                aria-controls="paytab" aria-selected="false">{{ __('Paytab') }}</a>
                                        </li>
                                    @endif
                                    @if (isset($paymentSetting['is_benefit_enabled']) && $paymentSetting['is_benefit_enabled'] == 'on')
                                        <li class="pt-3">
                                            <a data-toggle="tab" href="#benefit-payment" role="tab"
                                                aria-controls="benefit" aria-selected="false">{{ __('Benefit') }}</a>
                                        </li>
                                    @endif
                                    @if (isset($paymentSetting['is_cashfree_enabled']) && $paymentSetting['is_cashfree_enabled'] == 'on')
                                        <li class="pt-3">
                                            <a data-toggle="tab" href="#cashfree-payment" role="tab"
                                                aria-controls="cashfree" aria-selected="false">{{ __('Cashfree') }}</a>
                                        </li>
                                    @endif
                                    @if (isset($paymentSetting['is_aamarpay_enabled']) && $paymentSetting['is_aamarpay_enabled'] == 'on')
                                        <li class="pt-3">
                                            <a data-toggle="tab" href="#aamarpay-payment" role="tab"
                                                aria-controls="aamarpay" aria-selected="false">{{ __('Aamarpay') }}</a>
                                        </li>
                                    @endif
                                    @if (isset($paymentSetting['is_paytr_enabled']) && $paymentSetting['is_paytr_enabled'] == 'on')
                                        <li class="pt-3">
                                            <a data-toggle="tab" href="#paytr-payment" role="tab"
                                                aria-controls="paytr" aria-selected="false">{{ __('PayTr') }}</a>
                                        </li>
                                    @endif
                                    @if (isset($paymentSetting['is_midtrans_enabled']) && $paymentSetting['is_midtrans_enabled'] == 'on')
                                        <li class="pt-3">
                                            <a data-toggle="tab" href="#midtrans-payment" role="tab"
                                                aria-controls="paytr" aria-selected="false">{{ __('Midtrans') }}</a>
                                        </li>
                                    @endif
                                    @if (isset($paymentSetting['is_xendit_enabled']) && $paymentSetting['is_xendit_enabled'] == 'on')
                                        <li class="pt-3">
                                            <a data-toggle="tab" href="#xendit-payment" role="tab"
                                                aria-controls="xendit" aria-selected="false">{{ __('Xendit') }}</a>
                                        </li>
                                    @endif
                                    @if (isset($paymentSetting['is_yookassa_enabled']) && $paymentSetting['is_yookassa_enabled'] == 'on')
                                        <li class="pt-3">
                                            <a data-toggle="tab" href="#yookassa-payment" role="tab"
                                                aria-controls="yookassa" aria-selected="false">{{ __('Yookassa') }}</a>
                                        </li>
                                    @endif
                                    @if (isset($paymentSetting['is_paiementpro_enabled']) && $paymentSetting['is_paiementpro_enabled'] == 'on')
                                        <li class="pt-3">
                                            <a data-toggle="tab" href="#paiementpro-payment" role="tab"
                                                aria-controls="paiementpro"
                                                aria-selected="false">{{ __('Paiementpro') }}</a>
                                        </li>
                                    @endif
                                    @if (isset($paymentSetting['is_nepalste_enabled']) && $paymentSetting['is_nepalste_enabled'] == 'on')
                                        <li class="pt-3">
                                            <a data-toggle="tab" href="#nepalste-payment" role="tab"
                                                aria-controls="nepalste" aria-selected="false">{{ __('Nepalste') }}</a>
                                        </li>
                                    @endif
                                    @if (isset($paymentSetting['is_cinetpay_enabled']) && $paymentSetting['is_cinetpay_enabled'] == 'on')
                                        <li class="pt-3">
                                            <a data-toggle="tab" href="#cinetpay-payment" role="tab"
                                                aria-controls="cinetpay" aria-selected="false">{{ __('Cinetpay') }}</a>
                                        </li>
                                    @endif
                                    @if (isset($paymentSetting['is_fedapay_enabled']) && $paymentSetting['is_fedapay_enabled'] == 'on')
                                        <li class="pt-3">
                                            <a data-toggle="tab" href="#fedapay-payment" role="tab"
                                                aria-controls="fedapay" aria-selected="false">{{ __('Fedapay') }}</a>
                                        </li>
                                    @endif
                                    @if (isset($paymentSetting['is_payhere_enabled']) && $paymentSetting['is_payhere_enabled'] == 'on')
                                        <li class="pt-3">
                                            <a data-toggle="tab" href="#payhere-payment" role="tab"
                                                aria-controls="payhere" aria-selected="false">{{ __('Payhere') }}</a>
                                        </li>
                                    @endif
                                    @if (isset($paymentSetting['is_powertranz_enabled']) && $paymentSetting['is_powertranz_enabled'] == 'on')
                                        <li class="pt-3">
                                            <a data-toggle="tab" href="#powertranz-payment" role="tab"
                                                aria-controls="powertranz"
                                                aria-selected="false">{{ __('PowerTranz') }}</a>
                                        </li>
                                    @endif
                                    @if (isset($paymentSetting['is_payu_enabled']) && $paymentSetting['is_payu_enabled'] == 'on')
                                        <li class="pt-3">
                                            <a data-toggle="tab" href="#payu-payment" role="tab"
                                                aria-controls="payu"
                                                aria-selected="false">{{ __('PayU') }}</a>
                                        </li>
                                    @endif
                                </ul>
                            @endif

                            <div class="tab-content mt-3">

                                @if (isset($paymentSetting['is_bank_enabled']) && $paymentSetting['is_bank_enabled'] == 'on')
                                    <div class="tab-pane fade show active" id="bank-payment" role="tabpanel"
                                        aria-labelledby="bank-payment">
                                        <form method="post"
                                            action="{{ route('invoice.pay.with.bank', [$currentWorkspace->slug, \Illuminate\Support\Facades\Crypt::encrypt($invoice->id)]) }}"
                                            class="require-validation" id="bank-payment-form"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-6 form-group">
                                                            <label class="form-label" for="bank_details"
                                                                class="form-label">{{ __('Bank Details : ') }}</label><br>
                                                            {!! isset($paymentSetting['bank_details']) ? nl2br($paymentSetting['bank_details']) : '' !!}
                                                            <input type="hidden" name="bank_details" id="bank_details"
                                                                value="{{ isset($paymentSetting['bank_details']) ? $paymentSetting['bank_details'] : '' }}">
                                                        </div>
                                                        <div class="col-md-6 form-group">
                                                            <label class="form-label" for="payment_receipt"
                                                                class="form-label">{{ __('Payment Receipt') }}</label>
                                                            <input type="file" name="payment_receipt"
                                                                class="form-control" required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="amount"
                                                        class="col-form-label">{{ __('Amount') }}</label>
                                                    <div class="form-icon-user">
                                                        <span
                                                            class="currency-icon bg-primary">{{ !empty($currentWorkspace->currency) ? $currentWorkspace->currency : '$' }}</span>
                                                        <input class="form-control currency_input" required="required"
                                                            min="0" name="amount" type="number"
                                                            value="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            min="0" step="0.01"
                                                            max="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            id="amount">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-12 modal-footer">
                                                <button class="btn btn-primary" type="submit"
                                                    id="pay_with_bank">{{ __('Make Payment') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                @if ($currentWorkspace->is_stripe_enabled == 1)
                                    <div class="tab-pane fade
                                    {{-- {{ ($currentWorkspace->is_stripe_enabled == 1 && $currentWorkspace->is_paypal_enabled == 1) || $currentWorkspace->is_stripe_enabled == 1 ? 'show active' : '' }} --}}
                                    "
                                        id="stripe-payment" role="tabpanel" aria-labelledby="stripe-payment">
                                        <form method="post"
                                            action="{{ route('invoice.payment', [$currentWorkspace->slug, $invoice->id]) }}"
                                            class="require-validation" id="payment-form">
                                            @csrf
                                            <div class="row">
                                                <div class="col-sm-8">
                                                    <div class="custom-radio">
                                                        <label
                                                            class="font-16 col-form-label">{{ __('Credit / Debit Card') }}</label>
                                                    </div>
                                                    <p class="mb-0 pt-1 text-sm">
                                                        {{ __('Safe money transfer using your bank account. We support Mastercard, Visa, Discover and American express.') }}
                                                    </p>
                                                </div>
                                                <div class="col-sm-4 text-sm-right mt-3 mt-sm-0">
                                                    <img src="{{ asset('assets/img/payments/master.png') }}"
                                                        height="24" alt="master-card-img">
                                                    <img src="{{ asset('assets/img/payments/discover.png') }}"
                                                        height="24" alt="discover-card-img">
                                                    <img src="{{ asset('assets/img/payments/visa.png') }}"
                                                        height="24" alt="visa-card-img">
                                                    <img src="{{ asset('assets/img/payments/american express.png') }}"
                                                        height="24" alt="american-express-card-img">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="form-group">
                                                        <label for="card-name-on"
                                                            class="col-form-label">{{ __('Name on card') }}</label>
                                                        <input type="text" name="name" id="card-name-on"
                                                            class="form-control" placeholder="" required>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div id="card-element">
                                                    </div>
                                                    <div id="card-errors" role="alert"></div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="amount"
                                                        class="col-form-label">{{ __('Amount') }}</label>
                                                    <div class="form-icon-user">
                                                        <span
                                                            class="currency-icon bg-primary">{{ !empty($currentWorkspace->currency) ? $currentWorkspace->currency : '$' }}</span>
                                                        <input class="form-control currency_input" required="required"
                                                            min="0" name="amount" type="number"
                                                            value="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            min="0" step="0.01"
                                                            max="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            id="amount">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="error" style="display: none;">
                                                        <div class='alert-danger alert'>
                                                            {{ __('Please correct the errors and try again.') }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 modal-footer">
                                                    <input type="submit" class="btn btn-primary"
                                                        value="{{ __('Make Payment') }}">
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                @if ($currentWorkspace->is_paypal_enabled == 1)
                                    <div class="tab-pane fade {{ $currentWorkspace->is_stripe_enabled == 0 && $currentWorkspace->is_paypal_enabled == 1 ? 'show active' : '' }}"
                                        id="paypal-payment" role="tabpanel" aria-labelledby="paypal-payment">
                                        <form class="w3-container w3-display-middle w3-card-4 " method="POST"
                                            id="payment-form"
                                            action="{{ route('pay.with.paypal', [$currentWorkspace->slug, $invoice->id]) }}">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="amount"
                                                        class="col-form-label">{{ __('Amount') }}</label>
                                                    <div class="form-icon-user">
                                                        <span
                                                            class="currency-icon bg-primary">{{ !empty($currentWorkspace->currency) ? $currentWorkspace->currency : '$' }}</span>
                                                        <input class="form-control currency_input" required="required"
                                                            min="0" name="amount" type="number"
                                                            value="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            min="0" step="0.01"
                                                            max="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            id="amount">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 modal-footer">
                                                <input type="submit" class="btn btn-primary"
                                                    value="{{ __('Make Payment') }}">
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                @if ($paymentSetting['is_paystack_enabled'] == 'on')
                                    <div class="tab-pane fade" id="paystack-payment" role="tabpanel"
                                        aria-labelledby="paystack-payment">
                                        <form method="post"
                                            action="{{ route('invoice.pay.with.paystack', [$currentWorkspace->slug, $invoice->id]) }}"
                                            class="require-validation" id="paystack-payment-form">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label for="amount"
                                                        class="col-form-label">{{ __('Amount') }}</label>
                                                    <div class="form-icon-user">
                                                        <span
                                                            class="currency-icon bg-primary">{{ !empty($currentWorkspace->currency) ? $currentWorkspace->currency : '$' }}</span>
                                                        <input class="form-control currency_input" required="required"
                                                            min="0" name="amount" type="number"
                                                            value="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            min="0" step="0.01"
                                                            max="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            id="amount">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 modal-footer">
                                                <button class="btn btn-primary" type="button"
                                                    id="pay_with_paystack">{{ __('Make Payment') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                @if (isset($paymentSetting['is_flutterwave_enabled']) && $paymentSetting['is_flutterwave_enabled'] == 'on')
                                    <div class="tab-pane fade" id="flutterwave-payment" role="tabpanel"
                                        aria-labelledby="flutterwave-payment">
                                        <form method="post"
                                            action="{{ route('invoice.pay.with.flaterwave', [$currentWorkspace->slug, $invoice->id]) }}"
                                            class="require-validation" id="flaterwave-payment-form">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label class="col-form-label"
                                                        for="amount">{{ __('Amount') }}</label>
                                                    <div class="form-icon-user">
                                                        <span
                                                            class="currency-icon bg-primary">{{ !empty($currentWorkspace->currency) ? $currentWorkspace->currency : '$' }}</span>
                                                        <input class="form-control currency_input" required="required"
                                                            min="0" name="amount" type="number"
                                                            value="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            min="0" step="0.01"
                                                            max="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            id="amount">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 modal-footer">
                                                <button class="btn btn-primary" type="button"
                                                    id="pay_with_flaterwave">{{ __('Make Payment') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                @if (isset($paymentSetting['is_razorpay_enabled']) && $paymentSetting['is_razorpay_enabled'] == 'on')
                                    <div class="tab-pane fade" id="razorpay-payment" role="tabpanel"
                                        aria-labelledby="razorpay-payment">
                                        <form method="post"
                                            action="{{ route('invoice.pay.with.razorpay', [$currentWorkspace->slug, $invoice->id]) }}"
                                            class="require-validation" id="razorpay-payment-form">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label class="col-form-label"
                                                        for="amount">{{ __('Amount') }}</label>
                                                    <div class="form-icon-user">
                                                        <span
                                                            class="currency-icon bg-primary">{{ !empty($currentWorkspace->currency) ? $currentWorkspace->currency : '$' }}</span>
                                                        <input class="form-control currency_input" required="required"
                                                            min="0" name="amount" type="number"
                                                            value="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            min="0" step="0.01"
                                                            max="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            id="amount">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 modal-footer">
                                                <button class="btn btn-primary" type="button"
                                                    id="pay_with_razerpay">{{ __('Make Payment') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                @if (isset($paymentSetting['is_mercado_enabled']) && $paymentSetting['is_mercado_enabled'] == 'on')
                                    <div class="tab-pane fade" id="mercado-payment" role="tabpanel"
                                        aria-labelledby="mercado-payment">
                                        <form method="post"
                                            action="{{ route('invoice.pay.with.mercado', [$currentWorkspace->slug, $invoice->id]) }}"
                                            class="require-validation" id="mercado-form">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label class="col-form-label"
                                                        for="amount">{{ __('Amount') }}</label>
                                                    <div class="form-icon-user">
                                                        <span
                                                            class="currency-icon bg-primary">{{ !empty($currentWorkspace->currency) ? $currentWorkspace->currency : '$' }}</span>
                                                        <input class="form-control currency_input" required="required"
                                                            min="0" name="amount" type="number"
                                                            value="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            min="0" step="0.01"
                                                            max="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            id="amount">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 modal-footer">
                                                <button class="btn btn-primary"
                                                    type="submit">{{ __('Make Payment') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                @if (isset($paymentSetting['is_paytm_enabled']) && $paymentSetting['is_paytm_enabled'] == 'on')
                                    <div class="tab-pane fade" id="paytm-payment" role="tabpanel"
                                        aria-labelledby="paytm-payment">
                                        <form method="post"
                                            action="{{ route('invoice.pay.with.paytm', [$currentWorkspace->slug, $invoice->id]) }}"
                                            class="require-validation" id="paytm-form">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label class="col-form-label"
                                                        for="amount">{{ __('Amount') }}</label>
                                                    <div class="form-icon-user">
                                                        <span
                                                            class="currency-icon bg-primary">{{ !empty($currentWorkspace->currency) ? $currentWorkspace->currency : '$' }}</span>
                                                        <input class="form-control currency_input" required="required"
                                                            min="0" name="amount" type="number"
                                                            value="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            min="0" step="0.01"
                                                            max="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            id="amount">
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label for="mobile"
                                                            class="col-form-label text-dark">{{ __('Mobile Number') }}</label>
                                                        <input type="text" id="mobile" name="mobile"
                                                            class="form-control mobile" data-from="mobile"
                                                            placeholder="Enter Mobile Number" required="">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 modal-footer">
                                                <button class="btn btn-primary"
                                                    type="submit">{{ __('Make Payment') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                @if (isset($paymentSetting['is_mollie_enabled']) && $paymentSetting['is_mollie_enabled'] == 'on')
                                    <div class="tab-pane fade" id="mollie-payment" role="tabpanel"
                                        aria-labelledby="mollie-payment">
                                        <form method="post"
                                            action="{{ route('invoice.pay.with.mollie', [$currentWorkspace->slug, $invoice->id]) }}"
                                            class="require-validation" id="mollie-form">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label class="col-form-label"
                                                        for="amount">{{ __('Amount') }}</label>
                                                    <div class="form-icon-user">
                                                        <span
                                                            class="currency-icon bg-primary">{{ !empty($currentWorkspace->currency) ? $currentWorkspace->currency : '$' }}</span>
                                                        <input class="form-control currency_input" required="required"
                                                            min="0" name="amount" type="number"
                                                            value="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            min="0" step="0.01"
                                                            max="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            id="amount">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 modal-footer">
                                                <button class="btn btn-primary"
                                                    type="submit">{{ __('Make Payment') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                @if (isset($paymentSetting['is_skrill_enabled']) && $paymentSetting['is_skrill_enabled'] == 'on')
                                    <div class="tab-pane fade" id="skrill-payment" role="tabpanel"
                                        aria-labelledby="skrill-payment">
                                        <form method="post"
                                            action="{{ route('invoice.pay.with.skrill', [$currentWorkspace->slug, $invoice->id]) }}"
                                            class="require-validation" id="skrill-form">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label class="col-form-label"
                                                        for="amount">{{ __('Amount') }}</label>
                                                    <div class="form-icon-user">
                                                        <span
                                                            class="currency-icon bg-primary">{{ !empty($currentWorkspace->currency) ? $currentWorkspace->currency : '$' }}</span>
                                                        <input class="form-control currency_input" required="required"
                                                            min="0" name="amount" type="number"
                                                            value="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            min="0" step="0.01"
                                                            max="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            id="amount">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 modal-footer">
                                                <button class="btn btn-primary"
                                                    type="submit">{{ __('Make Payment') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                @if (isset($paymentSetting['is_coingate_enabled']) && $paymentSetting['is_coingate_enabled'] == 'on')
                                    <div class="tab-pane fade" id="coingate-payment" role="tabpanel"
                                        aria-labelledby="coingate-payment">
                                        <form method="post"
                                            action="{{ route('invoice.pay.with.coingate', [$currentWorkspace->slug, $invoice->id]) }}"
                                            class="require-validation" id="coingate-form">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label class="col-form-label"
                                                        for="amount">{{ __('Amount') }}</label>
                                                    <div class="form-icon-user">
                                                        <span
                                                            class="currency-icon bg-primary">{{ !empty($currentWorkspace->currency) ? $currentWorkspace->currency : '$' }}</span>
                                                        <input class="form-control currency_input" required="required"
                                                            min="0" name="amount" type="number"
                                                            value="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            min="0" step="0.01"
                                                            max="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            id="amount">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 modal-footer">
                                                <button class="btn btn-primary"
                                                    type="submit">{{ __('Make Payment') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                @if (isset($paymentSetting['is_paymentwall_enabled']) && $paymentSetting['is_paymentwall_enabled'] == 'on')
                                    <div class="tab-pane fade" id="paymentwall-payment" role="tabpanel"
                                        aria-labelledby="paymentwall-payment">
                                        <form method="post"
                                            action="{{ route('paymentwall.index', [$currentWorkspace->slug, $invoice->id]) }}"
                                            class="require-validation" id="coingate-form">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label class="col-form-label"
                                                        for="amount">{{ __('Amount') }}</label>
                                                    <div class="form-icon-user">
                                                        <span
                                                            class="currency-icon bg-primary">{{ !empty($currentWorkspace->currency) ? $currentWorkspace->currency : '$' }}</span>
                                                        <input class="form-control currency_input" required="required"
                                                            min="0" name="amount" type="number"
                                                            value="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            min="0" step="0.01"
                                                            max="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            id="amount">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 modal-footer">
                                                <button class="btn btn-primary"
                                                    type="submit">{{ __('Make Payment') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                @if (isset($paymentSetting['is_toyyibpay_enabled']) && $paymentSetting['is_toyyibpay_enabled'] == 'on')
                                    <div class="tab-pane fade" id="toyyibpay-payment" role="tabpanel"
                                        aria-labelledby="toyyibpay-payment">
                                        <form method="post"
                                            action="{{ route('invoice.pay.with.toyyibpay', [$currentWorkspace->slug, $invoice->id]) }}"
                                            class="require-validation" id="toyyibpay-form">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label class="col-form-label"
                                                        for="amount">{{ __('Amount') }}</label>
                                                    <div class="form-icon-user">
                                                        <span
                                                            class="currency-icon bg-primary">{{ !empty($currentWorkspace->currency) ? $currentWorkspace->currency : '$' }}</span>
                                                        <input class="form-control currency_input" required="required"
                                                            min="0" name="amount" type="number"
                                                            value="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            min="0" step="0.01"
                                                            max="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            id="amount">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 modal-footer">
                                                <button class="btn btn-primary"
                                                    type="submit">{{ __('Make Payment') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                @if (isset($paymentSetting['is_payfast_enabled']) && $paymentSetting['is_payfast_enabled'] == 'on')
                                    @php
                                        $pfHost =
                                            $paymentSetting['payfast_mode'] == 'sandbox'
                                                ? 'sandbox.payfast.co.za'
                                                : 'www.payfast.co.za';
                                    @endphp
                                    <div class="tab-pane fade" id="payfast-payment" role="tabpanel"
                                        aria-labelledby="payfast-payment">
                                        <form method="post"
                                            action="{{ route('invoice.pay.with.payfast', [$currentWorkspace->slug, $invoice->id]) }}"
                                            class="require-validation" id="payfast-form">

                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label class="col-form-label"
                                                        for="amount">{{ __('Amount') }}</label>
                                                    <div class="form-icon-user" id="payfast_amount">
                                                        <span
                                                            class="currency-icon bg-primary ">{{ !empty($currentWorkspace->currency) ? $currentWorkspace->currency : '$' }}</span>
                                                        <input class="form-control currency_input  payfast_amount_keyup"
                                                            required="required" min="0" name="amount"
                                                            type="number"
                                                            value="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            min="0" step="0.01"
                                                            max="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            id="amount">
                                                        <div id="get-payfast-inputs"></div>

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 modal-footer">
                                                <button class="btn btn-primary"
                                                    type="submit">{{ __('Make Payment') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                @if (isset($paymentSetting['is_iyzipay_enabled']) && $paymentSetting['is_iyzipay_enabled'] == 'on')
                                    <div class="tab-pane fade" id="iyzipay-payment" role="tabpanel"
                                        aria-labelledby="iyzipay-payment">
                                        <form method="post"
                                            action="{{ route('invoice.pay.with.iyzipay', [$currentWorkspace->slug, $invoice->id]) }}"
                                            class="require-validation" id="iyzipay-form">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label class="col-form-label"
                                                        for="amount">{{ __('Amount') }}</label>
                                                    <div class="form-icon-user">
                                                        <span
                                                            class="currency-icon bg-primary">{{ !empty($currentWorkspace->currency) ? $currentWorkspace->currency : '$' }}</span>
                                                        <input class="form-control currency_input" required="required"
                                                            min="0" name="amount" type="number"
                                                            value="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            min="0" step="0.01"
                                                            max="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            id="amount">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 modal-footer">
                                                <button class="btn btn-primary"
                                                    type="submit">{{ __('Make Payment') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                @if (isset($paymentSetting['is_sspay_enabled']) && $paymentSetting['is_sspay_enabled'] == 'on')
                                    <div class="tab-pane fade" id="sspay-payment" role="tabpanel"
                                        aria-labelledby="sspay-payment">
                                        <form method="post"
                                            action="{{ route('invoice.pay.with.sspay', [$currentWorkspace->slug, $invoice->id]) }}"
                                            class="require-validation" id="sspay-form">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label class="col-form-label"
                                                        for="amount">{{ __('Amount') }}</label>
                                                    <div class="form-icon-user">
                                                        <span
                                                            class="currency-icon bg-primary">{{ !empty($currentWorkspace->currency) ? $currentWorkspace->currency : '$' }}</span>
                                                        <input class="form-control currency_input" required="required"
                                                            min="0" name="amount" type="number"
                                                            value="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            min="0" step="0.01"
                                                            max="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            id="amount">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 modal-footer">
                                                <button class="btn btn-primary"
                                                    type="submit">{{ __('Make Payment') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                @if (isset($paymentSetting['is_paytab_enabled']) && $paymentSetting['is_paytab_enabled'] == 'on')
                                    <div class="tab-pane fade" id="paytab-payment" role="tabpanel"
                                        aria-labelledby="paytab-payment">
                                        <form method="post"
                                            action="{{ route('invoice.pay.with.paytab', [$currentWorkspace->slug, $invoice->id]) }}"
                                            class="require-validation" id="paytab-form">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label class="col-form-label"
                                                        for="amount">{{ __('Amount') }}</label>
                                                    <div class="form-icon-user">
                                                        <span
                                                            class="currency-icon bg-primary">{{ !empty($currentWorkspace->currency) ? $currentWorkspace->currency : '$' }}</span>
                                                        <input class="form-control currency_input" required="required"
                                                            min="0" name="amount" type="number"
                                                            value="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            min="0" step="0.01"
                                                            max="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            id="amount">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 modal-footer">
                                                <button class="btn btn-primary"
                                                    type="submit">{{ __('Make Payment') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                @if (isset($paymentSetting['is_benefit_enabled']) && $paymentSetting['is_benefit_enabled'] == 'on')
                                    <div class="tab-pane fade" id="benefit-payment" role="tabpanel"
                                        aria-labelledby="benefit-payment">
                                        <form method="post"
                                            action="{{ route('invoice.pay.with.benefit', [$currentWorkspace->slug, $invoice->id]) }}"
                                            class="require-validation" id="benefit-form">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label class="col-form-label"
                                                        for="amount">{{ __('Amount') }}</label>
                                                    <div class="form-icon-user">
                                                        <span
                                                            class="currency-icon bg-primary">{{ !empty($currentWorkspace->currency) ? $currentWorkspace->currency : '$' }}</span>
                                                        <input class="form-control currency_input" required="required"
                                                            min="0" name="amount" type="number"
                                                            value="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            min="0" step="0.01"
                                                            max="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            id="amount">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 modal-footer">
                                                <button class="btn btn-primary"
                                                    type="submit">{{ __('Make Payment') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                @if (isset($paymentSetting['is_cashfree_enabled']) && $paymentSetting['is_cashfree_enabled'] == 'on')
                                    <div class="tab-pane fade" id="cashfree-payment" role="tabpanel"
                                        aria-labelledby="cashfree-payment">
                                        <form method="post"
                                            action="{{ route('invoice.pay.with.cashfree', [$currentWorkspace->slug, $invoice->id]) }}"
                                            class="require-validation" id="cashfree-form">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label class="col-form-label"
                                                        for="amount">{{ __('Amount') }}</label>
                                                    <div class="form-icon-user">
                                                        <span
                                                            class="currency-icon bg-primary">{{ !empty($currentWorkspace->currency) ? $currentWorkspace->currency : '$' }}</span>
                                                        <input class="form-control currency_input" required="required"
                                                            min="0" name="amount" type="number"
                                                            value="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            min="0" step="0.01"
                                                            max="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            id="amount">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 modal-footer">
                                                <button class="btn btn-primary"
                                                    type="submit">{{ __('Make Payment') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                @if (isset($paymentSetting['is_aamarpay_enabled']) && $paymentSetting['is_aamarpay_enabled'] == 'on')
                                    <div class="tab-pane fade" id="aamarpay-payment" role="tabpanel"
                                        aria-labelledby="aamarpay-payment">
                                        <form method="post"
                                            action="{{ route('invoice.pay.with.aamarpay', [$currentWorkspace->slug, $invoice->id]) }}"
                                            class="require-validation" id="aamarpay-form">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label class="col-form-label"
                                                        for="amount">{{ __('Amount') }}</label>
                                                    <div class="form-icon-user">
                                                        <span
                                                            class="currency-icon bg-primary">{{ !empty($currentWorkspace->currency) ? $currentWorkspace->currency : '$' }}</span>
                                                        <input class="form-control currency_input" required="required"
                                                            min="0" name="amount" type="number"
                                                            value="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            min="0" step="0.01"
                                                            max="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            id="amount">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 modal-footer">
                                                <button class="btn btn-primary"
                                                    type="submit">{{ __('Make Payment') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                @if (isset($paymentSetting['is_paytr_enabled']) && $paymentSetting['is_paytr_enabled'] == 'on')
                                    <div class="tab-pane fade" id="paytr-payment" role="tabpanel"
                                        aria-labelledby="paytr-payment">
                                        <form method="post"
                                            action="{{ route('invoice.pay.with.paytr', [$currentWorkspace->slug, $invoice->id]) }}"
                                            class="require-validation" id="paytr-form">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label class="col-form-label"
                                                        for="amount">{{ __('Amount') }}</label>
                                                    <div class="form-icon-user">
                                                        <span
                                                            class="currency-icon bg-primary">{{ !empty($currentWorkspace->currency) ? $currentWorkspace->currency : '$' }}</span>
                                                        <input class="form-control currency_input" required="required"
                                                            min="0" name="amount" type="number"
                                                            value="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            min="0" step="0.01"
                                                            max="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            id="amount">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 modal-footer">
                                                <button class="btn btn-primary"
                                                    type="submit">{{ __('Make Payment') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                @if (isset($paymentSetting['is_midtrans_enabled']) && $paymentSetting['is_midtrans_enabled'] == 'on')
                                    <div class="tab-pane fade" id="midtrans-payment" role="tabpanel"
                                        aria-labelledby="midtrans-payment">
                                        <form method="post"
                                            action="{{ route('invoice.pay.with.midtrans', [$currentWorkspace->slug, $invoice->id]) }}"
                                            class="require-validation" id="midtrans-form">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label class="col-form-label"
                                                        for="amount">{{ __('Amount') }}</label>
                                                    <div class="form-icon-user">
                                                        <span
                                                            class="currency-icon bg-primary">{{ !empty($currentWorkspace->currency) ? $currentWorkspace->currency : '$' }}</span>
                                                        <input class="form-control currency_input" required="required"
                                                            min="0" name="amount" type="number"
                                                            value="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            min="0" step="0.01"
                                                            max="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            id="amount">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 modal-footer">
                                                <button class="btn btn-primary"
                                                    type="submit">{{ __('Make Payment') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                @if (isset($paymentSetting['is_xendit_enabled']) && $paymentSetting['is_xendit_enabled'] == 'on')
                                    <div class="tab-pane fade" id="xendit-payment" role="tabpanel"
                                        aria-labelledby="xendit-payment">
                                        <form method="post"
                                            action="{{ route('invoice.pay.with.xendit', [$currentWorkspace->slug, $invoice->id]) }}"
                                            class="require-validation" id="midtrans-form">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label class="col-form-label"
                                                        for="amount">{{ __('Amount') }}</label>
                                                    <div class="form-icon-user">
                                                        <span
                                                            class="currency-icon bg-primary">{{ !empty($currentWorkspace->currency) ? $currentWorkspace->currency : '$' }}</span>
                                                        <input class="form-control currency_input" required="required"
                                                            min="0" name="amount" type="number"
                                                            value="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            min="0" step="0.01"
                                                            max="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            id="amount">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 modal-footer">
                                                <button class="btn btn-primary"
                                                    type="submit">{{ __('Make Payment') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                @if (isset($paymentSetting['is_yookassa_enabled']) && $paymentSetting['is_yookassa_enabled'] == 'on')
                                    <div class="tab-pane fade" id="yookassa-payment" role="tabpanel"
                                        aria-labelledby="yookassa-payment">
                                        <form method="post"
                                            action="{{ route('invoice.pay.with.yookassa', [$currentWorkspace->slug, $invoice->id]) }}"
                                            class="require-validation" id="yookassa-form">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label class="col-form-label"
                                                        for="amount">{{ __('Amount') }}</label>
                                                    <div class="form-icon-user">
                                                        <span
                                                            class="currency-icon bg-primary">{{ !empty($currentWorkspace->currency) ? $currentWorkspace->currency : '$' }}</span>
                                                        <input class="form-control currency_input" required="required"
                                                            min="0" name="amount" type="number"
                                                            value="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            min="0" step="0.01"
                                                            max="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            id="amount">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 modal-footer">
                                                <button class="btn btn-primary"
                                                    type="submit">{{ __('Make Payment') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                                {{-- paiementpro payment --}}
                                @if (isset($paymentSetting['is_paiementpro_enabled']) && $paymentSetting['is_paiementpro_enabled'] == 'on')
                                    <div class="tab-pane fade" id="paiementpro-payment" role="tabpanel"
                                        aria-labelledby="paiementpro-payment">
                                        <form method="post"
                                            action="{{ route('invoice.pay.with.paiementpro', [$currentWorkspace->slug, $invoice->id]) }}"
                                            class="require-validation" id="paiementpro-form">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for=""
                                                            class="form-label">{{ __('Mobile Number') }}</label>
                                                        <input type="text" name="mobile_number"
                                                            placeholder="Enter Mobile Number" class="form-control"
                                                            required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for=""
                                                            class="form-label">{{ __('Channel') }}</label>
                                                        <input type="text" name="channel"
                                                            placeholder="Enter Channel" class="form-control" required>
                                                        <small
                                                            class="text-danger">{{ __('Example : OMCIV2,MOMO,CARD,FLOOZ ,PAYPAL') }}</small>
                                                    </div>
                                                </div>
                                                <div class="form-group col-md-12">
                                                    <label class="col-form-label"
                                                        for="amount">{{ __('Amount') }}</label>
                                                    <div class="form-icon-user">
                                                        <span
                                                            class="currency-icon bg-primary">{{ !empty($currentWorkspace->currency) ? $currentWorkspace->currency : '$' }}</span>
                                                        <input class="form-control currency_input" required="required"
                                                            min="0" name="amount" type="number"
                                                            value="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            min="0" step="0.01"
                                                            max="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            id="amount">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 modal-footer">
                                                <button class="btn btn-primary"
                                                    type="submit">{{ __('Make Payment') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                                {{-- end --}}

                                {{-- nepalste payment --}}
                                @if (isset($paymentSetting['is_nepalste_enabled']) && $paymentSetting['is_nepalste_enabled'] == 'on')
                                    <div class="tab-pane fade" id="nepalste-payment" role="tabpanel"
                                        aria-labelledby="nepalste-payment">
                                        <form method="post"
                                            action="{{ route('invoice.pay.with.nepalste', [$currentWorkspace->slug, $invoice->id]) }}"
                                            class="require-validation" id="nepalste-form">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label class="col-form-label"
                                                        for="amount">{{ __('Amount') }}</label>
                                                    <div class="form-icon-user">
                                                        <span
                                                            class="currency-icon bg-primary">{{ !empty($currentWorkspace->currency) ? $currentWorkspace->currency : '$' }}</span>
                                                        <input class="form-control currency_input" required="required"
                                                            min="0" name="amount" type="number"
                                                            value="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            min="0" step="0.01"
                                                            max="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            id="amount">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 modal-footer">
                                                <button class="btn btn-primary"
                                                    type="submit">{{ __('Make Payment') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                                {{-- end --}}


                                {{-- cinetpay payment --}}
                                @if (isset($paymentSetting['is_cinetpay_enabled']) && $paymentSetting['is_cinetpay_enabled'] == 'on')
                                    <div class="tab-pane fade" id="cinetpay-payment" role="tabpanel"
                                        aria-labelledby="cinetpay-payment">
                                        <form method="post"
                                            action="{{ route('invoice.pay.with.cinetpay', [$currentWorkspace->slug, $invoice->id]) }}"
                                            class="require-validation" id="cinetpay-form">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label class="col-form-label"
                                                        for="amount">{{ __('Amount') }}</label>
                                                    <div class="form-icon-user">
                                                        <span
                                                            class="currency-icon bg-primary">{{ !empty($currentWorkspace->currency) ? $currentWorkspace->currency : '$' }}</span>
                                                        <input class="form-control currency_input" required="required"
                                                            min="0" name="amount" type="number"
                                                            value="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            min="0" step="0.01"
                                                            max="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            id="amount">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 modal-footer">
                                                <button class="btn btn-primary"
                                                    type="submit">{{ __('Make Payment') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                                {{-- end --}}

                                {{-- fedapay payment --}}
                                @if (isset($paymentSetting['is_fedapay_enabled']) && $paymentSetting['is_fedapay_enabled'] == 'on')
                                    <div class="tab-pane fade" id="fedapay-payment" role="tabpanel"
                                        aria-labelledby="fedapay-payment">
                                        <form method="post"
                                            action="{{ route('invoice.pay.with.fedapay', [$currentWorkspace->slug, $invoice->id]) }}"
                                            class="require-validation" id="fedapay-form">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label class="col-form-label"
                                                        for="amount">{{ __('Amount') }}</label>
                                                    <div class="form-icon-user">
                                                        <span
                                                            class="currency-icon bg-primary">{{ !empty($currentWorkspace->currency) ? $currentWorkspace->currency : '$' }}</span>
                                                        <input class="form-control currency_input" required="required"
                                                            min="0" name="amount" type="number"
                                                            value="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            min="0" step="0.01"
                                                            max="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            id="amount">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 modal-footer">
                                                <button class="btn btn-primary"
                                                    type="submit">{{ __('Make Payment') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                                {{-- end --}}

                                {{-- payhere payment --}}
                                @if (isset($paymentSetting['is_payhere_enabled']) && $paymentSetting['is_payhere_enabled'] == 'on')
                                    <div class="tab-pane fade" id="payhere-payment" role="tabpanel"
                                        aria-labelledby="payhere-payment">
                                        <form method="post"
                                            action="{{ route('invoice.pay.with.payhere', [$currentWorkspace->slug, $invoice->id]) }}"
                                            class="require-validation" id="payhere-form">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label class="col-form-label"
                                                        for="amount">{{ __('Amount') }}</label>
                                                    <div class="form-icon-user">
                                                        <span
                                                            class="currency-icon bg-primary">{{ !empty($currentWorkspace->currency) ? $currentWorkspace->currency : '$' }}</span>
                                                        <input class="form-control currency_input" required="required"
                                                            min="0" name="amount" type="number"
                                                            value="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            min="0" step="0.01"
                                                            max="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            id="amount">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 modal-footer">
                                                <button class="btn btn-primary"
                                                    type="submit">{{ __('Make Payment') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                                {{-- end --}}

                                {{-- powertranz payment --}}
                                @if (isset($paymentSetting['is_powertranz_enabled']) && $paymentSetting['is_powertranz_enabled'] == 'on')
                                    <div class="tab-pane fade" id="powertranz-payment" role="tabpanel"
                                        aria-labelledby="powertranz-payment">
                                        <form method="post"
                                            action="{{ route('invoice.pay.powertranz.view', [$currentWorkspace->slug, $invoice->id]) }}"
                                            class="require-validation" id="powertranz-form">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label class="col-form-label"
                                                        for="amount">{{ __('Amount') }}</label>
                                                    <div class="form-icon-user">
                                                        <span
                                                            class="currency-icon bg-primary">{{ !empty($currentWorkspace->currency) ? $currentWorkspace->currency : '$' }}</span>
                                                        <input class="form-control currency_input" required="required"
                                                            min="0" name="amount" type="number"
                                                            value="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            min="0" step="0.01"
                                                            max="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            id="amount">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 modal-footer">
                                                <button class="btn btn-primary"
                                                    type="submit">{{ __('Make Payment') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                                {{-- end --}}

                                {{-- payu payment --}}
                                @if (isset($paymentSetting['is_payu_enabled']) && $paymentSetting['is_payu_enabled'] == 'on')
                                    <div class="tab-pane fade" id="payu-payment" role="tabpanel"
                                        aria-labelledby="payu-payment">
                                        <form method="post"
                                            action="{{ route('invoice.pay.with.payu', [$currentWorkspace->slug, $invoice->id]) }}"
                                            class="require-validation" id="payu-form">
                                            @csrf
                                            <div class="row">
                                                <div class="form-group col-md-12">
                                                    <label class="col-form-label"
                                                        for="amount">{{ __('Amount') }}</label>
                                                    <div class="form-icon-user">
                                                        <span
                                                            class="currency-icon bg-primary">{{ !empty($currentWorkspace->currency) ? $currentWorkspace->currency : '$' }}</span>
                                                        <input class="form-control currency_input" required="required"
                                                            min="0" name="amount" type="number"
                                                            value="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            min="0" step="0.01"
                                                            max="{{ $invoice->getDueAmounts($invoice->id) }}"
                                                            id="amount">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 modal-footer">
                                                <button class="btn btn-primary"
                                                    type="submit">{{ __('Make Payment') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                                {{-- end --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif


@endsection
@if (
    ($invoice->getDueAmount() > 0 && $currentWorkspace->is_stripe_enabled == 1) ||
        $currentWorkspace->is_paypal_enabled == 1 ||
        (isset($paymentSetting['is_paypal_enabled']) && $paymentSetting['is_paypal_enabled'] == 'on') ||
        (isset($paymentSetting['is_paystack_enabled']) && $paymentSetting['is_paystack_enabled'] == 'on') ||
        (isset($paymentSetting['is_flutterwave_enabled']) && $paymentSetting['is_flutterwave_enabled'] == 'on') ||
        (isset($paymentSetting['is_razorpay_enabled']) && $paymentSetting['is_razorpay_enabled'] == 'on') ||
        (isset($paymentSetting['is_mercado_enabled']) && $paymentSetting['is_mercado_enabled'] == 'on') ||
        (isset($paymentSetting['is_paytm_enabled']) && $paymentSetting['is_paytm_enabled'] == 'on') ||
        (isset($paymentSetting['is_mollie_enabled']) && $paymentSetting['is_mollie_enabled'] == 'on') ||
        (isset($paymentSetting['is_skrill_enabled']) && $paymentSetting['is_skrill_enabled'] == 'on') ||
        (isset($paymentSetting['is_coingate_enabled']) && $paymentSetting['is_coingate_enabled'] == 'on'))
    @push('css-page')
        <style>
            #card-element {
                border: 1px solid #e4e6fc;
                border-radius: 5px;
                padding: 10px;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://js.stripe.com/v3/"></script>
        <script type="text/javascript">
            var stripe = Stripe('{{ $currentWorkspace->stripe_key }}');
            var elements = stripe.elements();

            // Custom styling can be passed to options when creating an Element.
            var style = {
                base: {
                    // Add your base input styles here. For example:
                    fontSize: '14px',
                    color: '#32325d',
                },
            };

            // Create an instance of the card Element.
            var card = elements.create('card', {
                style: style
            });

            // Add an instance of the card Element into the `card-element` <div>.
            card.mount('#card-element');

            // Create a token or display an error when the form is submitted.
            var form = document.getElementById('payment-form');
            form.addEventListener('submit', function(event) {
                event.preventDefault();

                stripe.createToken(card).then(function(result) {
                    if (result.error) {
                        show_toastr('Error', result.error.message, 'error');
                    } else {
                        // Send the token to your server.
                        stripeTokenHandler(result.token);
                    }
                });
            });

            function stripeTokenHandler(token) {
                // Insert the token ID into the form so it gets submitted to the server
                var form = document.getElementById('payment-form');
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'stripeToken');
                hiddenInput.setAttribute('value', token.id);
                form.appendChild(hiddenInput);

                // Submit the form
                form.submit();
            }
        </script>
        <script src="{{ url('assets/custom/js/jquery.form.js') }}"></script>

        @if (isset($paymentSetting['is_paystack_enabled']) && $paymentSetting['is_paystack_enabled'] == 'on')
            <script src="https://js.paystack.co/v1/inline.js"></script>
            <script>
                //    Paystack Payment
                $(document).on("click", "#pay_with_paystack", function() {
                    $('#paystack-payment-form').ajaxForm(function(res) {
                        if (res.flag == 1) {
                            var coupon_id = res.coupon;
                            var paystack_callback =
                                "{{ url('/' . $currentWorkspace->slug . '/invoice/paystack') }}";
                            var order_id = '{{ time() }}';
                            var handler = PaystackPop.setup({
                                key: '{{ $paymentSetting['paystack_public_key'] }}',
                                email: res.email,
                                amount: res.total_price * 100,
                                currency: res.currency,
                                ref: 'pay_ref_id' + Math.floor((Math.random() * 1000000000) +
                                    1
                                ), // generates a pseudo-unique reference. Please replace with a reference you generated. Or remove the line entirely so our API will generate one for you
                                metadata: {
                                    custom_fields: [{
                                        display_name: "Email",
                                        variable_name: "email",
                                        value: res.email,
                                    }]
                                },

                                callback: function(response) {
                                    // console.log(response.reference, order_id);
                                    window.location.href = paystack_callback + '/' + response
                                        .reference + '/' + '{{ encrypt($invoice->id) }}';
                                    {{-- window.location.href = paystack_callback + '/' + '{{$invoice->id}}'; --}}
                                },
                                onClose: function() {
                                    alert('window closed');
                                }
                            });
                            handler.openIframe();
                        } else {
                            show_toastr('Error', data.message, 'msg');
                        }

                    }).submit();
                });
            </script>
        @endif

        @if (isset($paymentSetting['is_flutterwave_enabled']) && $paymentSetting['is_flutterwave_enabled'] == 'on')
            <script src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>
            <script>
                //    Flaterwave Payment
                $(document).on("click", "#pay_with_flaterwave", function() {
                    // console.log('hello');
                    $('#flaterwave-payment-form').ajaxForm(function(res) {
                        if (res.flag == 1) {
                            var coupon_id = res.coupon;

                            var API_publicKey = '{{ $paymentSetting['flutterwave_public_key'] }}';
                            var nowTim = "{{ date('d-m-Y-h-i-a') }}";
                            var flutter_callback = "{{ url($currentWorkspace->slug . '/invoice/flaterwave') }}";
                            var x = getpaidSetup({
                                PBFPubKey: API_publicKey,
                                // customer_email: @if (Auth::check()) ?  '{{ Auth::user()->email }}' :' {{ $client->email }}' @else 'client@example.com' @endif,
                                customer_email: @if (Auth::check())
                                    '{{ Auth::user()->email }}'
                                @else
                                    '{{ $client->email }}'
                                @endunless ,
                                amount: res.total_price,
                                currency: res.currency,
                                txref: nowTim + '__' + Math.floor((Math.random() * 1000000000)) +
                                    'fluttpay_online-' +
                                    {{ date('Y-m-d') }},
                                meta: [{
                                    metaname: "payment_id",
                                    metavalue: "id"
                                }],
                                onclose: function() {},
                                callback: function(response) {
                                    var txref = response.tx.txRef;
                                    if (
                                        response.tx.chargeResponseCode == "00" ||
                                        response.tx.chargeResponseCode == "0"
                                    ) {
                                        window.location.href = flutter_callback + '/' + txref + '/' +
                                            '{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}';
                                    } else {
                                        // redirect to a failure page.
                                    }
                                    x.close(); // use this to close the modal immediately after payment.
                                }
                            });
                        }
                    }).submit();
                });
            </script>
        @endif

        @if (isset($paymentSetting['is_razorpay_enabled']) && $paymentSetting['is_razorpay_enabled'] == 'on')
            <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
            <script>
                // Razorpay Payment
                $(document).on("click", "#pay_with_razerpay", function() {
                    $('#razorpay-payment-form').ajaxForm(function(res) {
                        if (res.flag == 1) {
                            var razorPay_callback =
                                '{{ url($currentWorkspace->slug . '/invoice/razorpay') }}';
                            var totalAmount = res.total_price * 100;
                            var coupon_id = res.coupon;
                            var options = {
                                "key": "{{ $paymentSetting['razorpay_public_key'] }}", // your Razorpay Key Id
                                "amount": totalAmount,
                                "name": 'Plan',
                                "currency": res.currency,
                                "description": "",
                                "handler": function(response) {
                                    window.location.href = razorPay_callback + '/' + response
                                        .razorpay_payment_id + '/' +
                                        '{{ \Illuminate\Support\Facades\Crypt::encrypt($invoice->id) }}?coupon_id=' +
                                        coupon_id + '&payment_frequency=' + res.payment_frequency;
                                },
                                "theme": {
                                    "color": "#528FF0"
                                }
                            };
                            var rzp1 = new Razorpay(options);
                            rzp1.open();
                        } else {
                            show_toastr('Error', res.msg, 'msg');
                        }

                    }).submit();
                });
            </script>
        @endif

        {{-- @if ($paymentSetting['is_payfast_enabled'] == 'on' && !empty($paymentSetting['payfast_merchant_id']) && !empty($paymentSetting['payfast_merchant_key']))
                <script>
                $(document).ready(function() {
                    get_payfast_status(amount = 0,coupon = null);
                });
                $(".payfast_amount_keyup").keyup(function(){
                    get_payfast_status(amount = 0,coupon = null);
                });

                function get_payfast_status(amount,coupon){

                    var invoice_id = {{ $invoice->id }};
                    var amount = $('#payfast_amount input[type=number]').val();
                    $.ajax({
                        url: '{{ route('invoice.pay.with.payfast') }}',
                        method: 'POST',
                        data : {
                            'invoice_id' : invoice_id,
                            'amount' : amount
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function (data) {

                            if (data.success == true) {
                                $('#get-payfast-inputs').append(data.inputs);

                            }else{
                                show_toastr('Error', data.inputs, 'error')
                            }
                        }
                    });
                }
                </script>
        @endif --}}
    @endpush
@endif
