@php
    use App\Models\Utility;
    $languages = App\Models\Utility::languages();
@endphp
<x-guest-layout>
    <x-auth-card>
        @section('page-title')
            {{ __('Email Varification') }}
        @endsection
        @section('language-bar')
            {{-- <a href="#" class="btn-primary">
                <select name="language" id="language" class=" btn-primary btn "
                    onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
                    @foreach (App\Models\Utility::languages() as $language)
                        <option class="login_lang" @if ($lang == $language) selected @endif
                            value="{{ route('verification.notice', $language) }}">
                            {{ ucfirst(\App\Models\Utility::getlang_fullname($language)) }}</option>
                    @endforeach
                </select>
            </a> --}}
            <div href="#" class="lang-dropdown-only-desk">
                <li class="dropdown dash-h-item drp-language">
                    <a class="dash-head-link dropdown-toggle btn" href="#" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        <span class="drp-text"> {{ ucFirst($languages[$lang]) }}
                        </span>
                    </a>
                    <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                        @foreach ($languages as $languageCode => $languageFullName)
                            <a href="{{ route('verification.notice', $languageCode) }}" tabindex="0"
                                class="dropdown-item {{ $languageCode == $lang ? 'active' : '' }}">
                                <span>{{ ucFirst($languageFullName) }}</span>
                            </a>
                        @endforeach
                    </div>
                </li>
            </div>
        @endsection
        @section('content')
            <div class="card-body">
                @if (session('statuss') == 'verification-link-sent')
                    <div class="mb-4 font-medium text-sm text-green-600 text-primary">
                        {{ __('A new verification link has been sent to the email address you provided during registration.') }}
                    </div>
                @endif
                <div class="mb-4 text-sm text-gray-600">
                    {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
                </div>
                <div class="mt-4 flex items-center justify-between">
                    <div class="row">
                        <div class="col-auto">
                            <form method="POST" action="{{ route('verification.send') }}">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm">
                                    {{ __('Resend Verification Email') }}
                                </button>
                            </form>
                        </div>
                        <div class="col-auto">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm">
                                    {{ __('Logout') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            {{-- <div class="col-xl-6 img-card-side">
                <div class="auth-img-content">
                    <img src="{{ asset('assets/images/auth/img-auth-3.svg') }}" alt="" class="img-fluid">
                    <h3 class="text-white mb-4 mt-5">{{ __('“Attention is the new currency”') }}</h3>
                    <p class="text-white">
                        {{ __('The more effortless the writing looks, the more effort the writer actually put into the process.') }}
                    </p>
                </div>
            </div> --}}
        @endsection
    </x-auth-card>
</x-guest-layout>
