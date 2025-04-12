<form class="needs-validation" novalidate method="post" action="{{ route('users.change.password', [$user_id]) }}">
    @csrf
    <div class="modal-body resetPasswordForm">
        <div class="row">
            <div class="col-md-12">
                <label for="password" class="col-form-label">{{ __('New Password') }}</label><x-required></x-required>
                <input type="password" class="form-control" id="password" name="password"
                    placeholder="Enter The New Password" required>
            </div>
            <div class="col-md-12">
                <label for="password_confirmation"
                    class="col-form-label">{{ __('Confirm New Password') }}</label><x-required></x-required>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                    placeholder="Enter the confirm Password" required>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{ __('Cancel') }}" class="btn  btn-secondary" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Reset') }}" class="btn  btn-primary">
    </div>
</form>
