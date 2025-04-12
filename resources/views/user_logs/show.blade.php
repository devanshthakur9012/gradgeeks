<div class="modal-body">
    <div class="row">
        <div class="col-md-6 ">
            <div class="form-control-label"><b>{{ __('Status') }}</b></div>
            <p class="text-muted mb-4">{{ isset($user->status) ? $user->status : '--' }}</p>
        </div>
        <div class="col-md-6 ">
            <div class="form-control-label"><b>{{ __('Country') }} </b></div>
            <p class="text-muted mb-4">{{ isset($user->country) ? $user->country : '--' }}</p>
        </div>
        <div class="col-md-6 ">
            <div class="form-control-label"><b>{{ __('Country Code') }} </b></div>
            <p class="text-muted mb-4">{{ isset($user->countryCode) ? $user->countryCode : '--' }}</p>
        </div>
        <div class="col-md-6 ">
            <div class="form-control-label"><b>{{ __('Region') }}</b></div>
            <p class="mt-1">{{ isset($user->region) ? $user->region : '--' }}</p>
        </div>
        <div class="col-md-6 ">
            <div class="form-control-label"><b>{{ __('Region Name') }}</b></div>
            <p class="mt-1">{{ isset($user->regionName) ? $user->regionName : '--' }}</p>
        </div>
        <div class="col-md-6 ">
            <div class="form-control-label"><b>{{ __('City') }}</b></div>
            <p class="mt-1">{{ isset($user->city) ? $user->city : '--' }}</p>
        </div>
        <div class="col-md-6 ">
            <div class="form-control-label"><b>{{ __('Zip') }}</b></div>
            <p class="mt-1">{{ isset($user->zip) ? $user->zip : '--' }}</p>
        </div>
        <div class="col-md-6 ">
            <div class="form-control-label"><b>{{ __('Latitude') }}</b></div>
            <p class="mt-1">{{ isset($user->lat) ? $user->lat : '--' }}</p>
        </div>
        <div class="col-md-6 ">
            <div class="form-control-label"><b>{{ __('Longitude') }}</b></div>
            <p class="mt-1">{{ isset($user->lon) ? $user->lon : '--' }}</p>
        </div>
        <div class="col-md-6 ">
            <div class="form-control-label"><b>{{ __('Timezone') }}</b></div>
            <p class="mt-1">{{ isset($user->timezone) ? $user->timezone : '--' }}</p>
        </div>
        <div class="col-md-6 ">
            <div class="form-control-label"><b>{{ __('Isp') }}</b></div>
            <p class="mt-1">{{ isset($user->isp) ? $user->isp : '--' }}</p>
        </div>
        <div class="col-md-6 ">
            <div class="form-control-label"><b>{{ __('Org') }}</b></div>
            <p class="mt-1">{{ isset($user->org) ? $user->org : '--' }}</p>
        </div>
        <div class="col-md-6 ">
            <div class="form-control-label"><b>{{ __('As') }}</b></div>
            <p class="mt-1">{{ isset($user->as) ? $user->as : '--' }}</p>
        </div>
        <div class="col-md-6 ">
            <div class="form-control-label"><b>{{ __('Query') }}</b></div>
            <p class="mt-1">{{ isset($user->query) ? $user->query : '--' }}</p>
        </div>
        <div class="col-md-6 ">
            <div class="form-control-label"><b>{{ __('Browser Name') }}</b></div>
            <p class="mt-1">{{ isset($user->browser_name) ? $user->browser_name : '--' }}</p>
        </div>
        <div class="col-md-6 ">
            <div class="form-control-label"><b>{{ __('Os Name') }}</b></div>
            <p class="mt-1">{{ isset($user->os_name) ? $user->os_name : '--' }}</p>
        </div>
        <div class="col-md-6 ">
            <div class="form-control-label"><b>{{ __('Browser Language') }}</b></div>
            <p class="mt-1">{{ isset($user->browser_language) ? $user->browser_language : '--' }}</p>
        </div>
        <div class="col-md-6 ">
            <div class="form-control-label"><b>{{ __('Device Type') }}</b></div>
            <p class="mt-1">{{ isset($user->device_type) ? $user->device_type : '--' }}</p>
        </div>
        <div class="col-md-6 ">
            <div class="form-control-label"><b>{{ __('Referrer Host') }}</b></div>
            <p class="mt-1">{{ isset($user->referrer_host) ? $user->referrer_host : '--' }}</p>
        </div>
        <div class="col-md-6 ">
            <div class="form-control-label"><b>{{ __('Referrer Path') }}</b></div>
            <p class="mt-1">{{ isset($user->referrer_path) ? $user->referrer_path : '--' }}</p>
        </div>
    </div>
</div>
