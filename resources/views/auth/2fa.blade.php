@extends('layouts.guest')

@section('page-title')
    {{ __('Authentication') }}
@endsection

@section('content')
    <div class="row mt-5 align-items-center text-start">
        <div class="col-lg-7 col-md-10 col-12 text-center">
            <div class="card form-main-card">
                <div class="card-body w-100">
                    <div>
                        <h3 class="mb-3 f-w-600">{{ __('Login') }}</h3>
                    </div>
                    <form method="POST" class="needs-validation create-form mb-0 pb-4" novalidate
                        action="{{ route('2faVerify') }}" id="form_data">
                        @csrf
                        <input type="hidden" name="2fa_referrer"
                            value="{{ request()->get('2fa_referrer') ?? URL()->current() }}">
                        <div class="ticket-form-row row">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <p>{{ __('Please enter the') }} <strong>{{ __(' OTP') }}</strong>
                                        {{ __(' generated on your Authenticator App') }}. <br>
                                        {{ __('Ensure you submit the current one because it refreshes every 30 seconds') }}.
                                    </p>
                                    <label for="one_time_password"
                                        class="col-md-12 form-label">{{ __('One Time Password') }}</label>
                                    <input id="one_time_password" type="password"
                                        class="form-control @if ($errors->any()) is-invalid @endif"
                                        name="one_time_password" required="required" autofocus>
                                    @if ($errors->any())
                                        <span class="error invalid-email text-danger" role="alert">
                                            @foreach ($errors->all() as $error)
                                                <small>{{ $error }}</small>
                                            @endforeach
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12 d-flex flex-column align-items-center mt-3">
                                    <div class="d-flex justify-content-center gap-3">
                                        <button type="submit" class="btn btn-primary">
                                            {{ __('Login') }}
                                        </button>

                                        <a href="{{ route('logout') }}" class="btn btn-danger text-white"
                                            onclick="event.preventDefault(); document.getElementById('frm-logout').submit();">
                                            {{ __('Logout') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <form id="frm-logout" action="{{ route('logout') }}" method="POST" class="d-none">
                        {{ csrf_field() }}
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
