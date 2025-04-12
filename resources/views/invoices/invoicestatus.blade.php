{{ Form::model($order, ['route' => ['invoicebankPaymentApproval.response', $order->id], 'method' => 'POST']) }}
<div class="row p-3">
    <div class="col-12">
        <div class="row">
            <div class="col-md-6">
                <label class="form-label" for="bank_details" class="form-label">{{ __('Order Id : ') }}</label><br>
            </div>
            <div class="col-md-6">
                {{ $order->order_id }}
            </div>
        </div>
        <hr class="my-3">
        <div class="row">
            <div class="col-md-6">
                <label class="form-label" for="bank_details" class="form-label">{{ __('Invoice : ') }}</label><br>
            </div>
            <div class="col-md-6">
                {{ App\Models\Utility::invoiceNumberFormat($order->invoice_id) }}
            </div>
        </div>
        <hr class="my-3">
        <div class="row">
            <div class="col-md-6">
                <label class="form-label" for="bank_details" class="form-label">{{ __('Amount : ') }}</label><br>
            </div>
            <div class="col-md-6">
                {{ $currentWorkspace->currency . $order->amount }}
            </div>
        </div>
        <hr class="my-3">
        <div class="row">
            <div class="col-md-6">
                <label class="form-label" for="bank_details" class="form-label">{{ __('Payment Type : ') }}</label><br>
            </div>
            <div class="col-md-6">
                {{ $order->payment_type }}
            </div>
        </div>
        <hr class="my-3">
        <div class="row">
            <div class="col-md-6">
                <label class="form-label" for="bank_details"
                    class="form-label">{{ __('Payment Status : ') }}</label><br>
            </div>
            <div class="col-md-6">
                {{ $order->payment_status }}
            </div>
        </div>
        <hr class="my-3">
        <div class="row">
            <div class="col-md-6">
                <label class="form-label" for="bank_details" class="form-label">{{ __('Bank Details : ') }}</label><br>
            </div>
            <div class="col-md-6 ">
                {!! nl2br($admin_payment_setting['bank_details']) !!}
            </div>
        </div>
        <hr class="my-3">
        <div class="row">
            <div class="col-md-6">
                <label class="form-label" for="bank_details"
                    class="form-label">{{ __('Payment Receipt : ') }}</label><br>
            </div>
            <div class="col-md-6">
                <a class="btn btn-primary" href="{{ asset('storage/uploads/invoice_receipt/' . $order->receipt) }}"
                    download="">
                    <i class="ti ti-download text-white" data-bs-toggle="tooltip"
                        data-bs-original-title="{{ __('Download') }}"></i>
                </a>
            </div>
        </div>
    </div>
    <hr class="my-3">
    <div class="col-12">
        <div class="modal-footer border-0 p-0">
            {{ Form::hidden('payment_approval', null, ['class' => 'payment_approval']) }}
            <input type="submit" class="btn btn-danger denypayment_request_button" value="{{ __('Deny') }}">
            <input type="submit" class="btn btn-primary approvepayment_request_button" value="{{ __('Approve') }}">
        </div>
    </div>
</div>
{{ Form::close() }}


<script>
    $(document).on('click', '.approvepayment_request_button', function() {
        $(this).parent().find('.payment_approval').attr('value', 1);
    });

    $(document).on('click', '.denypayment_request_button', function() {
        $(this).parent().find('.payment_approval').attr('value', 0);
    });
</script>
