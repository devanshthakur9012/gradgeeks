<form method="post" action="{{ route('clients.store', $currentWorkspace->slug) }}" class="needs-validation" novalidate>
    @csrf
    <div class="modal-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group ">
                    <label for="name" class="col-form-label">{{ __('Name') }}</label><x-required></x-required>
                    <input class="form-control" type="text" id="name" name="name" required=""
                        placeholder="{{ __('Enter Name') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group ">
                    <label for="email" class="col-form-label">{{ __('Email') }}</label><x-required></x-required>
                    <input class="form-control" type="email" id="email" name="email" required=""
                        placeholder="{{ __('Enter Email') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group ">
                    <label for="city" class="col-form-label">{{ __('City') }}</label><x-required></x-required>
                    <input class="form-control" type="city" id="city" name="city" required
                        placeholder="{{ __('Enter City') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group ">
                    <label for="state" class="col-form-label">{{ __('State') }}</label><x-required></x-required>
                    <input class="form-control" type="state" id="state" name="state" required
                        placeholder="{{ __('Enter State') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group ">
                    <label for="zip_code"
                        class="col-form-label">{{ __('Zip/Post Code') }}</label><x-required></x-required>
                    <input class="form-control" type="zip_code" id="zip_code" name="zip_code" required
                        placeholder="{{ __('Enter Zip/Post Code') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group ">
                    <label for="country" class="col-form-label">{{ __('Country') }}</label><x-required></x-required>
                    <input class="form-control" type="country" id="country" name="country" required
                        placeholder="{{ __('Enter Country') }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group ">
                    <label for="telephone"
                        class="col-form-label">{{ __('Telephone') }}</label><x-required></x-required>
                    <input class="form-control" type="telephone" id="telephone" name="telephone" required
                        placeholder="{{ __('Enter Telephone') }}" pattern="^\+\d{1,3}\d{9,13}$">
                    <div class=" text-xs text-danger">
                        {{ __('Please use with country code. (ex. +91)') }}
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group ">
                    <label for="address" class="col-form-label">{{ __('Address') }}</label><x-required></x-required>
                    <textarea name="address" id="address" cols="2" rows="2" class="form-control" required></textarea>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <label for="password_switch">{{ __('Login is enable') }}</label>
                <div class="form-check form-switch custom-switch-v1 float-end">
                    <input type="checkbox" name="password_switch" class="form-check-input input-primary pointer"
                        value="on" id="password_switch">
                    <label class="form-check-label" for="password_switch"></label>
                </div>
            </div>
            <div class="form-group ps_div d-none">
                <label for="password" class="col-form-label">{{ __('Password') }}</label><x-required></x-required>
                <input class="form-control" type="password" id="password" name="password"
                    placeholder="{{ __('Enter Password') }}">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{ __('Cancel') }}" class="btn  btn-secondary" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
    </div>
</form>
