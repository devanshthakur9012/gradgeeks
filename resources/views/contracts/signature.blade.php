<form id='form_pad' method="post" enctype="multipart/form-data">
    @method('POST')
    @csrf
    <div class="modal-body" id="">
        <div class="row">
            <input type="hidden" name="contract_id" value="{{ $contract->id }}">
            <label for="" class="col-form-label">{{ __('Sign') }}</label>
            @if (\Auth::user()->getGuard() == 'client')
                <div class="form-control">
                    <canvas id="signature-pad" class="signature-pad" height=200></canvas>
                    <input type="hidden" name="client_signature" id="SignupImage1">
                </div>
            @endif
            @if ($currentWorkspace->permission == 'Owner')
                <div class="form-control">
                    <canvas id="signature-pad" class="signature-pad" height=200></canvas>
                    <input type="hidden" name="company_signature" id="SignupImage1">
                </div>
            @endif
            <div class="mt-1">
                <input type="button" value="{{ __('Clear') }}" class="btn btn-danger" id="clearSig">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal">
        <input type="button" value="{{ __('Save') }}" class="btn btn-primary" id="addSig">
    </div>
</form>

@php
    $client_keyword = Auth::user()->getGuard() == 'client' ? 'client.' : '';
@endphp
<script src="{{ asset('assets/js/plugins/signature_pad/signature_pad.min.js') }}"></script>
<script>
    var signature = {
        canvas: null,
        clearButton: null,

        init: function init() {

            this.canvas = document.querySelector(".signature-pad");
            this.clearButton = document.getElementById('clearSig');
            this.saveButton = document.getElementById('addSig');
            signaturePad = new SignaturePad(this.canvas);
            this.clearButton.addEventListener('click', function(event) {

                signaturePad.clear();
            });

            this.saveButton.addEventListener('click', function(event) {
                var data = signaturePad.toDataURL('image/png');
                $('#SignupImage1').val(data);
                $.ajax({

                    url: '{{ route($client_keyword . 'signaturestore', [$currentWorkspace->slug]) }}',
                    type: 'POST',
                    data: $("form").serialize(),
                    success: function(data) {
                        if (data.message) {
                            show_toastr('success', data.message, 'success');
                        } else {
                            show_toastr('error', 'Some Thing Is Wrong!');
                        }
                        $('#commonModal').modal('hide');
                    },
                    error: function(data) {
                        data = data.responseJSON;
                        if (data.message) {
                            show_toastr('error', data.message);
                        } else {
                            show_toastr('error', 'Some Thing Is Wrong!');
                        }
                    }
                });
            });
        }
    };

    signature.init();
</script>
