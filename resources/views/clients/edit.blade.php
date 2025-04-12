<form method="post" action="{{ route('clients.update', [$currentWorkspace->slug, $client->id]) }}"
    class="needs-validation" novalidate>
    @csrf
    <div class="modal-body">
        <div class="row">

            <div class="col-md-12">
                <label for="name" class="col-form-label">{{ __('Name') }}</label><x-required></x-required>
                <input class="form-control" type="text" id="name" name="name" required
                    placeholder="{{ __('Enter Name') }}" value="{{ $client->name }}">
            </div>

            <div class="form-group ">
                <label for="email" class="col-form-label">{{ __('Email') }}</label><x-required></x-required>
                <input class="form-control" type="email" id="email" name="email" required
                    placeholder="{{ __('Enter Email') }}" value="{{ $client->email }}">
            </div>

            <div class="col-md-6">
                <div class="form-group ">
                    <label for="city" class="col-form-label">{{ __('City') }}</label><x-required></x-required>
                    <input class="form-control" type="city" id="city" name="city" required
                        placeholder="{{ __('Enter City') }}" value="{{ isset($client->city) ? $client->city : '' }}">
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group ">
                    <label for="state" class="col-form-label">{{ __('State') }}</label><x-required></x-required>
                    <input class="form-control" type="state" id="state" name="state" required
                        placeholder="{{ __('Enter State') }}"
                        value="{{ isset($client->state) ? $client->state : '' }}">
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group ">
                    <label for="zip_code"
                        class="col-form-label">{{ __('Zip/Post Code') }}</label><x-required></x-required>
                    <input class="form-control" type="zip_code" id="zip_code" name="zip_code" required
                        placeholder="{{ __('Enter Zip/Post Code') }}"
                        value="{{ isset($client->zipcode) ? $client->zipcode : '' }}">
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group ">
                    <label for="country" class="col-form-label">{{ __('Country') }}</label><x-required></x-required>
                    <input class="form-control" type="country" id="country" name="country" required
                        placeholder="{{ __('Enter Country') }}"
                        value="{{ isset($client->country) ? $client->country : '' }}">
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group ">
                    <label for="telephone"
                        class="col-form-label">{{ __('Telephone') }}</label><x-required></x-required>
                    <input class="form-control" type="telephone" id="telephone" name="telephone" required
                        placeholder="{{ __('Enter Telephone') }}" pattern="^\+\d{1,3}\d{9,13}$"
                        value="{{ isset($client->telephone) ? $client->telephone : '' }}">
                    <div class=" text-xs text-danger">
                        {{ __('Please use with country code. (ex. +91)') }}
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="form-group ">
                    <label for="address" class="col-form-label">{{ __('Address') }}</label><x-required></x-required>
                    <textarea name="address" id="address" cols="2" rows="2" class="form-control" required>{{ isset($client->address) ? $client->address : '' }}</textarea>
                </div>
            </div>
        </div>
    </div>
    <div class=" modal-footer">
        <input type="button" value="{{ __('Cancel') }}" class="btn  btn-secondary" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
    </div>
</form>
