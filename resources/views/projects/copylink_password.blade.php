@extends('layouts.guest')

@section('page-title')
    {{ __('Copylink') }}
@endsection

{{-- @section('language-bar')
    <a href="#" class="btn-primary">
        <select name="language" id="language" class=" btn-primary btn "
            onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
            @foreach (App\Models\Utility::languages() as $langCode => $language)
                <option class="login_lang" @if ($lang == $langCode) selected @endif
                    value="{{ route('verification.notice', $langCode) }}">{{ Str::upper($language) }}</option>
            @endforeach
        </select>
    </a>
@endsection --}}

<link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
@section('content')
    <div class="card-body">
        <div class="mb-3">
            <h2 class="h3">{{ __('Password required') }}</h2>
            <h6>{{ __('This document is password-protected. Please enter a password.') }}</h6>
        </div>
        <span class="clearfix"></span>
        <form method="POST" class="needs-validation" novalidate
            action="{{ route('projects.link', [$slug, \Illuminate\Support\Facades\Crypt::encrypt($projectID)]) }}">
            @csrf
            <div class="form-group mb-2">
                <label class="form-control-label">{{ __('Password') }}</label>
                <div class="input-group input-group-merge">
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                        name="password" required autocomplete="new-password">
                    <div class="input-group-append">
                        <span class="input-group-text py-3" id="togglePassword">
                            <a href="#" data-toggle="password-text" data-target="#password">
                                <i class="fas fa-eye-slash"></i>
                            </a>
                        </span>
                    </div>
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">
                    <span class="btn-inner--text">{{ __('Save') }}</span>
                </button>
            </div>
        </form>
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

@push('custom-scripts')
    <script src="{{ asset('assets/custom/libs/jquery/dist/jquery.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('togglePassword').addEventListener('click', function() {
                var passInput = document.getElementById('password');
                if (passInput.type === 'password') {
                    passInput.type = 'text';
                } else {
                    passInput.type = 'password';
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $("#form_data").submit(function(e) {
                $("#login_button").attr("disabled", true);
                return true;
            });
        });
    </script>
@endpush
