@php
    $setting = \App\Models\Utility::getAdminPaymentSettings();
    $languages = App\Models\Utility::languages();
    App\models\Utility::setCaptchaConfig();
@endphp

<x-guest-layout>
    <x-auth-card>

        <?php
        $dir = base_path() . '/resources/lang/';
        $glob = glob($dir . '*', GLOB_ONLYDIR);
        $arrLang = array_map(function ($value) use ($dir) {
            return str_replace($dir, '', $value);
        }, $glob);
        $arrLang = array_map(function ($value) use ($dir) {
            return preg_replace('/[0-9]+/', '', $value);
        }, $arrLang);
        $arrLang = array_filter($arrLang);
        $currantLang = basename(App::getLocale());
        $client_keyword = Request::route()->getName() == 'client.login' ? 'client.' : '';
        ?>

        @section('page-title')
            {{ __('Forgot Password') }}
        @endsection

        @section('language-bar')
            <div href="#" class="lang-dropdown-only-desk">
                <li class="dropdown dash-h-item drp-language">
                    <a class="dash-head-link dropdown-toggle btn" href="#" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <span class="drp-text">
                            {{ ucFirst($languages[$lang]) }}
                        </span>
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
            @if (session()->has('status'))
                <div class="alert alert-info">
                    {{ session()->get('status') }}
                </div>
            @endif
            <div class="card-body">
                <div class="">
                    <h2 class="mb-3 f-w-600">{{ __('Forgot Password') }}</h2>
                </div>
                <form method="POST" action="{{ route('password.email') }}" class="needs-validation" novalidate>
                    @csrf
                    <div class="">
                        <div class="form-group mb-3">
                            <label for="email" class="form-label">{{ __('Email') }}</label>
                            <input type="email" class="form-control  @error('email') is-invalid @enderror" name="email"
                                id="emailaddress" value="{{ old('email') }}" required autocomplete="email" autofocus
                                placeholder="{{ __('Enter Your Email') }}">
                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
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
                            <button type="submit" class="btn btn-primary btn-block mt-2">
                                {{ __('Send Password Reset Link') }}
                            </button>
                        </div>
                        <p class="mb-2 mt-2 text-center">Back to
                            <a href="{{ route('login', $lang) }}" class="f-w-400 text-primary">{{ __('Login') }}</a>
                        </p>
                </form>
            </div>
            {{-- <div class="col-xl-6 img-card-side">
                <div class="auth-img-content">
                    <img src="{{ asset('assets/images/auth/img-auth-3.svg') }}" alt="" class="img-fluid">
                    <h3 class="text-white mb-4 mt-5">“Attention is the new currency”</h3>
                    <p class="text-white">The more effortless the writing looks, the more effort the writer
                        actually put into the process.</p>
                </div>
            </div> --}}
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
