<form method="post" action="{{ route('store_lang_workspace') }}" class="needs-validation" novalidate>
    @csrf
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <label for="lang_code" class="col-form-label">{{ __('Language Code') }}</label><x-required></x-required>
                <input class="form-control" type="text" id="lang_code" name="lang_code" required=""
                    placeholder="{{ __('Enter Language Code') }}">
            </div>
            <div class="col-md-12">
                <label for="lang_fullname"
                    class="col-form-label">{{ __('Language Fullname') }}</label><x-required></x-required>
                <input class="form-control" type="text" id="lang_fullname" name="lang_fullname" required=""
                    placeholder="{{ __('Enter Language Fullname') }}">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{ __('Cancel') }}" class="btn  btn-secondary" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
    </div>
</form>
