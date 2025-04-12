@php
    if (isset($currentWorkspace)) {
        $setting = App\Models\Utility::getcompanySettings($currentWorkspace->id);
        $SITE_RTL = $setting->site_rtl;
        if ($setting->theme_color) {
            $color = $setting->theme_color;
        } else {
            $color = 'theme-3';
        }
    } else {
        $setting = App\Models\Utility::getAdminPaymentSettings();
        // $SITE_RTL = env('SITE_RTL');
        $SITE_RTL = $setting['site_rtl'];
        if ($setting['color']) {
            $color = $setting['color'];
        } else {
            $color = 'theme-3';
        }
    }

    if (\App::getLocale() == 'ar' || \App::getLocale() == 'he') {
        $SITE_RTL = 'on';
    }

    $meta_setting = App\Models\Utility::getAdminPaymentSettings();
    $meta_images = \App\Models\Utility::get_file('uploads/logo/');
    $logo = \App\Models\Utility::get_file('logo/');
    use App\Models\Utility;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $SITE_RTL == 'on' ? 'rtl' : '' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="title" content="{{ $meta_setting['meta_keywords'] }}">
    <meta name="description" content="{{ $meta_setting['meta_description'] }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content= "{{ env('APP_URL') }}">
    <meta property="og:title" content="{{ $meta_setting['meta_keywords'] }}">
    <meta property="og:description" content="{{ $meta_setting['meta_description'] }}">
    <meta property="og:image" content="{{ asset($meta_images . $meta_setting['meta_image']) }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ env('APP_URL') }}">
    <meta property="twitter:title" content="{{ $meta_setting['meta_keywords'] }}">
    <meta property="twitter:description" content="{{ $meta_setting['meta_description'] }}">
    <meta property="twitter:image" content="{{ asset($meta_images . $meta_setting['meta_image']) }}">

    <title>
        {{ config('app.name', 'Taskly') }} - @yield('page-title')
    </title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ $logo . 'favicon.png' . '?' . time() }}">


    @if ($setting['cust_darklayout'] == 'on')
        @if (isset($SITE_RTL) && $SITE_RTL == 'on')
            <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css') }}" id="main-style-link">
        @endif
        <link rel="stylesheet" href="{{ asset('assets/css/style-dark.css') }}">
    @else
        @if (isset($SITE_RTL) && $SITE_RTL == 'on')
            <link rel="stylesheet" href="{{ asset('assets/css/style-rtl.css') }}" id="main-style-link">
        @else
            <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
        @endif
    @endif

    @if (isset($SITE_RTL) && $SITE_RTL == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/custom-auth-rtl.css') }}" id="main-style-link">
    @else
        <link rel="stylesheet" href="{{ asset('assets/css/custom-auth.css') }}" id="main-style-link">
    @endif

    @if ($setting['cust_darklayout'] == 'on')
        <link rel="stylesheet" href="{{ asset('assets/css/custom-dark.css') }}" id="main-style-link">
    @endif

    <style>
        :root {
            --color-customColor: <?=$color ?>;
        }

        .big-logo {
            height: 60px;
            width: 150px;
        }

        .g-recaptcha {
            filter: invert(1) hue-rotate(180deg) !important;
        }

        .grecaptcha-badge {
            z-index: 99999999 !important;
        }
    </style>

    <link rel="stylesheet" href="{{ asset('css/custom-color.css') }}">
</head>


@php
    $color = !empty($setting['color']) ? $setting['color'] : 'theme-3';

    if (isset($setting['color_flag']) && $setting['color_flag'] == 'true') {
        $color = 'custom-color';
    } else {
        $color = $color;
    }
@endphp

<body class="{{ $color }}">
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

    <script src="{{ asset('assets/js/vendor-all.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
    <script src="{{ asset('assets/custom/libs/jquery/dist/jquery.min.js') }}"></script>

    <script>
        feather.replace();
    </script>

    <script>
        feather.replace();
        // var pctoggle = document.querySelector("#pct-toggler");
        // if (pctoggle) {
        //     pctoggle.addEventListener("click", function() {
        //         if (
        //             !document.querySelector(".pct-customizer").classList.contains("active")
        //         ) {
        //             document.querySelector(".pct-customizer").classList.add("active");
        //         } else {
        //             document.querySelector(".pct-customizer").classList.remove("active");
        //         }
        //     });
        // }

        // var themescolors = document.querySelectorAll(".themes-color > a");
        // for (var h = 0; h < themescolors.length; h++) {
        //     var c = themescolors[h];

        //     c.addEventListener("click", function(event) {
        //         var targetElement = event.target;
        //         if (targetElement.tagName == "SPAN") {
        //             targetElement = targetElement.parentNode;
        //         }
        //         var temp = targetElement.getAttribute("data-value");
        //         removeClassByPrefix(document.querySelector("body"), "theme-");
        //         document.querySelector("body").classList.add(temp);
        //     });
        // }
        // var custthemebg = document.querySelector("#cust-theme-bg");
        // custthemebg.addEventListener("click", function() {
        //     if (custthemebg.checked) {
        //         document.querySelector(".dash-sidebar").classList.add("transprent-bg");
        //         document
        //             .querySelector(".dash-header:not(.dash-mob-header)")
        //             .classList.add("transprent-bg");
        //     } else {
        //         document.querySelector(".dash-sidebar").classList.remove("transprent-bg");
        //         document
        //             .querySelector(".dash-header:not(.dash-mob-header)")
        //             .classList.remove("transprent-bg");
        //     }
        // });

        // var custdarklayout = document.querySelector("#cust-darklayout");
        // custdarklayout.addEventListener("click", function() {
        //     if (custdarklayout.checked) {
        //         document
        //             .querySelector(".m-header > .b-brand > .logo-lg")
        //             .setAttribute("src", "../assets/images/logo.svg");
        //         document
        //             .querySelector("#main-style-link")
        //             .setAttribute("href", "../assets/css/style-dark.css");
        //     } else {
        //         document
        //             .querySelector(".m-header > .b-brand > .logo-lg")
        //             .setAttribute("src", "../assets/images/logo-dark.svg");
        //         document
        //             .querySelector("#main-style-link")
        //             .setAttribute("href", "../assets/css/style.css");
        //     }
        // });

        function removeClassByPrefix(node, prefix) {
            for (let i = 0; i < node.classList.length; i++) {
                let value = node.classList[i];
                if (value.startsWith(prefix)) {
                    node.classList.remove(value);
                }
            }
        }
    </script>

    @stack('custom-scripts')
    <!-- [ auth-signup ] start -->

    @php
        $company_logo = App\Models\Utility::get_logo();
    @endphp

    <div class="custom-login">
        <div class="login-bg-img">
            {{-- <img src="{{ asset('assets/img/' . $color . '.svg') }}" class="login-bg-1">
            <img src="{{ asset('assets/img/user2.svg') }}" class="login-bg-2"> --}}
            @if ($color == 'custom-color')
                <!-- Show theme-3 image -->
                <img src="{{ asset('assets/img/theme-3.svg') }}" class="login-bg-1">
                <img src="{{ asset('assets/img/user2.svg') }}" class="login-bg-2">
            @else
                <img src="{{ asset('assets/img/' . $color . '.svg') }}" class="login-bg-1">
                <img src="{{ asset('assets/img/user2.svg') }}" class="login-bg-2">
            @endif
        </div>
        <div class="bg-login bg-primary"></div>
        <div class="custom-login-inner">
            <header class="dash-header">
                <nav class="navbar navbar-expand-md default">
                    <div class="container">
                        <div class="navbar-brand">
                            <a class="" href="#">
                                <img src="{{ asset($logo . $company_logo . '?v=' . time()) }}" class=""
                                    alt="logo">
                            </a>
                        </div>
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarlogin" aria-controls="navbarTogglerDemo01" aria-expanded="false"
                            aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                        </button>

                        <div class="collapse navbar-collapse" id="navbarlogin">
                            <ul class="navbar-nav align-items-center ms-auto mb-2 mb-lg-0">
                                @include('landingpage::layouts.buttons')
                                <a class="" href="#">@yield('language-bar')</a>
                            </ul>
                        </div>
                    </div>
                </nav>
            </header>

            <main class="custom-wrapper">
                <div class="custom-row">
                    <div class="card">
                        @yield('content')
                    </div>
                </div>
            </main>

            <div class="row justify-content-center">
                <div class="col-md-4">
                    @if (session()->has('info'))
                        <div class="alert alert-primary">
                            {{ session()->get('info') }}
                        </div>
                    @endif
                </div>
            </div>

            <footer>
                <div class="auth-footer">
                    <div class="container">
                        <div class="row">
                            <div class="col-12">
                                {{-- <span class=""> {{ env('FOOTER_TEXT') }}</span> --}}
                                <span>
                                    &copy; {{ date('Y') }}
                                    {{ Utility::getValByName('footer_text') ? Utility::getValByName('footer_text') : config('app.name', 'Taskly') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </footer>
            {{-- @yield('content') --}}

        </div>
        {{-- <div class="auth-footer">
            <div class="container-fluid">
                <div class="row">

                    <div class="col-12">
                        <ul class="list-inline mb-1">

                        </ul>
                        <p class=""> {{ env('FOOTER_TEXT') }}</p>
                    </div>

                </div>
            </div>
        </div> --}}
    </div>
    <!-- [ auth-signup ] end -->

    <!-- Required Js -->

    {{-- <div class="pct-customizer">
        <div class="pct-c-btn">
            <button class="btn btn-primary" id="pct-toggler">
                <i data-feather="settings"></i>
            </button>
        </div>
        <div class="pct-c-content">
            <div class="pct-header bg-primary">
                <h5 class="mb-0 text-white f-w-500">Theme Customizer</h5>
            </div>
            <div class="pct-body">
                <h6 class="mt-2">
                    <i data-feather="credit-card" class="me-2"></i>Primary color settings
                </h6>
                <hr class="my-2" />
                <div class="theme-color themes-color">
                    <a href="#!" class="" data-value="theme-1"></a>
                    <a href="#!" class="" data-value="theme-2"></a>
                    <a href="#!" class="" data-value="theme-3"></a>
                    <a href="#!" class="" data-value="theme-4"></a>
                </div>

                <h6 class="mt-4">
                    <i data-feather="layout" class="me-2"></i>Sidebar settings
                </h6>
                <hr class="my-2" />
                <div class="form-check form-switch">
                    <input type="checkbox" class="form-check-input" id="cust-theme-bg" checked />
                    <label class="form-check-label f-w-600 pl-1" for="cust-theme-bg">Transparent layout</label>
                </div>
                <h6 class="mt-4">
                    <i data-feather="sun" class="me-2"></i>Layout settings
                </h6>
                <hr class="my-2" />
                <div class="form-check form-switch mt-2">
                    <input type="checkbox" class="form-check-input" id="cust-darklayout" />
                    <label class="form-check-label f-w-600 pl-1" for="cust-darklayout">Dark Layout</label>
                </div>
            </div>
        </div>
    </div> --}}
    @if ($meta_setting['enable_cookie'] == 'on')
        @include('layouts.cookie_consent')
    @endif
</body>

</html>
