<form method="POST" action="{{ route('users.store') }}" class="needs-validation" novalidate>
    @csrf
    <div class="modal-body">
        <div class="form-group">
            <label for="fullname" class="form-label">{{ __('Full Name') }}</label><x-required></x-required>
            <input class="form-control" name="name" type="text" id="fullname"
                placeholder="{{ __('Enter Your Name') }}" value="{{ old('name') }}" required autocomplete="name">
        </div>
        <div class="form-group">
            <label for="workspace_name" class="form-label">{{ __('Workspace Name') }}</label><x-required></x-required>
            <input class="form-control" name="workspace" type="text" id="workspace_name"
                placeholder="{{ __('Enter Workspace Name') }}" value="{{ old('workspace') }}" required
                autocomplete="workspace">
        </div>
        <div class="form-group">
            <label for="emailaddress" class="form-label">{{ __('Email Address') }}</label><x-required></x-required>
            <input class="form-control" name="email" type="email" id="emailaddress" required autocomplete="email"
                placeholder="{{ __('Enter Your Email') }}" value="{{ old('email') }}">
        </div>
        <div class="col-md-5 mb-3">
            <label for="password_switch">{{ __('Login is enable') }}</label>
            <div class="form-check form-switch custom-switch-v1 float-end">
                <input type="checkbox" name="password_switch" class="form-check-input input-primary pointer"
                    value="on" id="password_switch">
                <label class="form-check-label" for="password_switch"></label>
            </div>
        </div>
        <div class="form-group ps_div d-none">
            <label for="password" class="form-label">{{ __('Password') }}</label><x-required></x-required>
            <input class="form-control" name="password" type="password" autocomplete="new-password" id="password"
                placeholder="{{ __('Enter Your Password') }}">
        </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{ __('Cancel') }}" class="btn  btn-secondary" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
    </div>
</form>
