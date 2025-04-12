<form method="post" action="{{ route('client.change.password', [$currentWorkspace->slug, $client->id]) }}"
    class="needs-validation" novalidate>
    @csrf
    <div class="modal-body client-reset-password">
        <div class="row">
            <div class="col-md-12">
                <label for="password" class="col-form-label">{{ __('New Password') }}</label><x-required></x-required>
                <input type="password" class="form-control" id="password" name="password" required />
            </div>
            <div class="col-md-12">
                <label for="password_confirmation"
                    class="col-form-label">{{ __('Confirm Password') }}</label><x-required></x-required>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                    required />
            </div>
        </div>
    </div>
    <div class=" modal-footer">
        <input type="button" value="{{ __('Cancel') }}" class="btn  btn-secondary" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Reset') }}" class="btn  btn-primary">
    </div>
</form>
