<x-guest-layout>
    <x-auth-card>

        @php
            $setting = \App\Models\Utility::getAdminPaymentSettings();
            $languages = \App\Models\Utility::languages();
            App\models\Utility::setCaptchaConfig();
            $landinpagesettings = \Modules\LandingPage\Entities\LandingPageSetting::settings();
        @endphp

        @section('page-title')
            {{ __('Register') }}
        @endsection

        @section('language-bar')
            {{-- <a href="#" class="monthly-btn btn-primary ">
                <select name="language" id="language" class="btn-primary btn"
                    onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
                    @foreach (App\Models\Utility::languages() as $language)
                        <option class="login_lang" @if ($lang == $language) selected @endif
                            value="{{ route('register', $language) }}">
                            {{ ucfirst(\App\Models\Utility::getlang_fullname($language)) }}
                        </option>
                    @endforeach
                </select>
            </a> --}}
            <div href="#" class="lang-dropdown-only-desk">
                <li class="dropdown dash-h-item drp-language">
                    <a class="dash-head-link dropdown-toggle btn" href="#" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <span class="drp-text"> {{ ucFirst($languages[$lang]) }} </span>
                    </a>
                    <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                        @foreach ($languages as $code => $language)
                            <a href="{{ route('login', $code) }}" tabindex="0"
                                class="dropdown-item {{ $code == $lang ? 'active' : '' }}">
                                <span>{{ ucFirst($language) }}</span>
                            </a>
                        @endforeach
                    </div>
                </li>
            </div>
        @endsection

        @section('content')
            <div class="card-body">
                <div class="">
                    <h2 class="mb-3 f-w-600">{{ __('Register') }}</h2>
                </div>
                <form method="POST" action="{{ route('register') }}" class="needs-validation" novalidate>
                    @if (session('statuss'))
                        <div class="mb-4 font-medium text-lg text-green-600 text-danger">
                            {{ __('Email SMTP settings does not configured so please contact to your site admin.') }}
                        </div>
                    @endif
                    @csrf
                    <div class="">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">{{ __('Full Name') }}</label>
                            <input type="text" class="form-control  @error('name') is-invalid @enderror" name="name"
                                id="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                                placeholder="{{ __('Enter Your Name') }}">
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="workspace" class="form-label">{{ __('Workspace Name') }}</label>
                            <input type="text" class="form-control  @error('workspace') is-invalid @enderror"
                                name="workspace" id="workspace" value="{{ old('workspace') }}" required
                                autocomplete="workspace" placeholder="{{ __('Enter Your Workspace Name') }}">
                            @error('workspace')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="email" class="form-label">{{ __('Email') }}</label>
                            <input type="email" class="form-control  @error('email') is-invalid @enderror" name="email"
                                id="emailaddress" value="{{ old('email') }}" required autocomplete="email"
                                placeholder="{{ __('Enter Your Email') }}">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        <div class="form-group mb-3">
                            <label for="password" class="form-label">{{ __('Password') }}</label>
                            <input type="password" class="form-control @error('password') is-invalid @enderror"
                                name="password" required autocomplete="new-password" id="password"
                                placeholder="{{ __('Enter Your Password') }}">
                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"
                                name="password_confirmation" required autocomplete="new-password" id="password_confirmation"
                                placeholder="{{ __('Confirm Your Password') }}">
                            @error('password_confirmation')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="form-check custom-checkbox">
                            <input type="checkbox" class="form-check-input" id="termsCheckbox" name="terms" required>
                            <label class="form-check-label text-sm" for="termsCheckbox">
                                @if (is_array(json_decode($landinpagesettings['menubar_page'])) ||
                                        is_object(json_decode($landinpagesettings['menubar_page'])))
                                    @php
                                        $pages = json_decode($landinpagesettings['menubar_page']);
                                        $termsAndConditions = null;
                                        $privacyPolicy = null;

                                        foreach ($pages as $page) {
                                            if ($page->page_slug == 'terms_and_conditions') {
                                                $termsAndConditions = $page;
                                            }
                                            if ($page->page_slug == 'privacy_policy') {
                                                $privacyPolicy = $page;
                                            }
                                        }
                                    @endphp
                                    @if ($termsAndConditions || $privacyPolicy)
                                        {{ __('I agree to the ') }}
                                    @endif
                                    @if ($termsAndConditions)
                                        <a href="{{ route('custom.page', $termsAndConditions->page_slug) }}"
                                            target="_blank">{{ $termsAndConditions->menubar_page_name }}</a>
                                    @endif
                                    @if ($termsAndConditions && $privacyPolicy)
                                        {{ __('and the ') }}
                                    @endif
                                    @if ($privacyPolicy)
                                        <a href="{{ route('custom.page', $privacyPolicy->page_slug) }}"
                                            target="_blank">{{ $privacyPolicy->menubar_page_name }}</a>
                                    @endif
                                @endif
                            </label>
                        </div>

                        @if ($setting['recaptcha_module'] == 'on')
                            @if (isset($setting['google_recaptcha_version']) && $setting['google_recaptcha_version'] == 'v2-checkbox')
                                <div class="form-group mb-3">
                                    {!! NoCaptcha::display($setting['cust_darklayout'] == 'on' ? ['data-theme' => 'dark'] : []) !!}
                                    @error('g-recaptcha-response')
                                        <span class="small text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            @else
                                <div class="form-group col-lg-12 col-md-12 mt-3">
                                    <input type="hidden" id="g-recaptcha-response" name="g-recaptcha-response"
                                        class="form-control">
                                    @error('g-recaptcha-response')
                                        <span class="error small text-danger" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            @endif
                        @endif

                        <div class="d-grid">
                            <button type="submit" id="login_button" class="btn btn-primary btn-block mt-2">
                                {{ __('Register') }}
                            </button>
                        </div>
                </form>
                <p class="mb-2 mt-2 text-center">{{ __('Already have an account?') }}
                    <a href="{{ route('login', $lang) }}" class="f-w-400 text-primary">{{ __('Login') }}</a>
                </p>
            </div>
        @endsection
        @push('custom-scripts')
            @if ($setting['recaptcha_module'] == 'on')
                @if (isset($setting['google_recaptcha_version']) && $setting['google_recaptcha_version'] == 'v2-checkbox')
                    {!! NoCaptcha::renderJs() !!}
                @elseif(isset($setting['google_recaptcha_version']) && $setting['google_recaptcha_version'] == 'v3')
                    <script src="https://www.google.com/recaptcha/api.js?render={{ $setting['google_recaptcha_key'] }}"></script>
                    <script>
                        $(document).ready(function() {
                            grecaptcha.ready(function() {
                                grecaptcha.execute('{{ $setting['google_recaptcha_key'] }}', {
                                    action: 'submit'
                                }).then(function(token) {
                                    $('#g-recaptcha-response').val(token);
                                });
                            });
                        });
                    </script>
                @endif
            @endif
        @endpush
    </x-auth-card>
</x-guest-layout>
