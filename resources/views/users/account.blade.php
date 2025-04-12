@extends('layouts.admin')

@section('page-title')
    {{ __('User Profile') }}
@endsection

@section('links')
    @if (\Auth::guard('client')->check())
        <li class="breadcrumb-item"><a href="{{ route('client.home') }}">{{ __('Home') }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    @endif
    <li class="breadcrumb-item"> {{ __('User Profile') }}</li>
@endsection

@php
    // $logo = \App\Models\Utility::get_file('users-avatar/');
    $logo = \App\Models\Utility::get_file('avatars/');
@endphp

@section('content')
    <div class="row">
        <div class="col-xl-3">
            <div class="card sticky-top" style="top:30px">
                <div class="list-group list-group-flush" id="useradd-sidenav">
                    <a href="#v-pills-home" class="list-group-item list-group-item-action border-0">{{ __('Account') }} <div
                            class="float-end"><i class="ti ti-chevron-right"></i></div></a>

                    <a href="#v-pills-profile"
                        class="list-group-item list-group-item-action border-0">{{ __('Change Password') }} <div
                            class="float-end"><i class="ti ti-chevron-right"></i></div></a>

                    @auth('client')
                        <a href="#v-pills-billing"
                            class="list-group-item list-group-item-action border-0">{{ __('Billing Details') }} <div
                                class="float-end"><i class="ti ti-chevron-right"></i></div></a>
                    @endauth

                    @if ($data['hasPermission'])
                        <a href="#authentication-sidenav" class="list-group-item border-0 list-group-item-action">
                            {{ __('Two Factor Authentication') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-xl-9">
            <div id="v-pills-home" class="card ">
                <div class="card-header">
                    <h5>{{ __('Avatar') }}</h5>
                </div>
                @php
                    $workspace = $currentWorkspace ? $currentWorkspace->id : 0;
                    $user_id = $user ? $user->id : 0;
                @endphp
                <div class="card-body">
                    <form method="post" class="needs-validation" novalidate
                        action="@auth('web'){{ route('update.account', [$workspace, $user_id]) }}@elseauth{{ route('client.update.account', [$workspace, $user_id]) }}@endauth"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">

                                    <img @if ($user->avatar) src="{{ asset($logo . $user->avatar) }}" @else avatar="{{ $user->name }}" @endif
                                        id="myAvatar" alt="user-image" class="rounded-circle img-thumbnail img_hight w-25">
                                    {{-- @if ($user->avatar != '')
                                        <div class=" ">
                                            <a href="#"
                                                class=" action-btn btn-danger  btn btn-sm  mb-1 d-inline-flex align-items-center bs-pass-para"
                                                data-confirm="{{ __('Are You Sure?') }}"
                                                data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                data-confirm-yes="delete_avatar"><i class="ti ti-trash text-white"></i></a>
                                        </div>
                                    @endif --}}
                                    <div class="choose-file ">
                                        <label for="avatar">
                                            <div class=" bg-primary"> <i
                                                    class="ti ti-upload px-1"></i>{{ __('Choose file here') }}</div>
                                            <input type="file" class="form-control choose_file_custom" name="avatar"
                                                id="avatar" data-filename="avatar-logo">
                                        </label>
                                        <br><small
                                            class="">{{ __('Please upload a valid image file. Size of image should not be more than 2MB.') }}</small>
                                        @error('avatar')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="name"
                                        class="form-label">{{ __('Full Name') }}</label><x-required></x-required>
                                    <input class="form-control @error('name') is-invalid @enderror" name="name"
                                        type="text" id="fullname" placeholder="{{ __('Enter Your Name') }}"
                                        value="{{ $user->name }}" required autocomplete="name">
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                <div class="form-group">
                                    <label for="email"
                                        class="form-label">{{ __('Email') }}</label><x-required></x-required>
                                    <input class="form-control @error('email') is-invalid @enderror" name="email"
                                        type="text" id="email" placeholder="{{ __('Enter Your Email Address') }}"
                                        value="{{ $user->email }}" required autocomplete="email">
                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class=" row">
                                <div class="text-end">
                                    <button type="submit" class="btn-submit btn btn-primary col-sm-auto col-12">
                                        {{ __('Save Changes') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    @if ($user->avatar != '')
                        <form
                            action="@auth('web'){{ route('delete.avatar') }}@elseauth{{ route('client.delete.avatar') }}@endauth"
                            method="post" id="delete_avatar">
                            @csrf
                            @method('DELETE')
                        </form>
                    @endif
                    @auth('web')
                        <div class="text-end">
                            <a href="#" class="btn btn-danger delete_btn bs-pass-para "
                                data-confirm="{{ __('Are You Sure?') }}"
                                data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                data-confirm-yes="delete-my-account">
                                {{ __('Delete') }} {{ __('My Account') }}
                            </a>
                            <form action="{{ route('delete.my.account') }}" method="post" id="delete-my-account">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    @endauth
                </div>
            </div>

            <div class="card" id="v-pills-profile">
                <div class="card-header">
                    <h5>{{ __('Change Password') }}</h5>
                </div>
                <div class="card-body">
                    <form method="post" class="needs-validation" novalidate
                        action="@auth('web'){{ route('update.password') }}@elseauth{{ route('client.update.password') }}@endauth">
                        @csrf

                        <div class="col-lg-12">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="old_password"
                                            class="form-label">{{ __('Old Password') }}</label><x-required></x-required>
                                        <input class="form-control @error('old_password') is-invalid @enderror"
                                            name="old_password" type="password" id="old_password"
                                            autocomplete="old_password" placeholder="{{ __('Enter Old Password') }}"
                                            required>
                                        @error('old_password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="password"
                                            class="form-label">{{ __('New Password') }}</label><x-required></x-required>
                                        <input class="form-control @error('password') is-invalid @enderror"
                                            name="password" type="password" autocomplete="new-password" id="password"
                                            placeholder="{{ __('Enter new password') }}" required>
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label for="password_confirmation"
                                            class="form-label">{{ __('Confirm New Password') }}</label><x-required></x-required>
                                        <input class="form-control @error('password_confirmation') is-invalid @enderror"
                                            name="password_confirmation" type="password" ` autocomplete="new-password"
                                            id="password_confirmation" placeholder="{{ __('Enter confirm password') }}"
                                            required>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="text-end">
                                <button type="submit" class="btn-submit btn btn-primary "> {{ __('Change Password') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @auth('client')
                <div class="card" id="v-pills-billing">

                    <div class="card-header">
                        <h5>{{ __('Billing Details') }}</h5>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('client.update.billing') }}" class="needs-validation"
                            novalidate>
                            @csrf
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label for="address" class="form-label">{{ __('Address') }}</label>
                                    <input class="form-control font-style" name="address" type="text"
                                        value="{{ $user->address }}" id="address">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="city" class="form-label">{{ __('City') }}</label>
                                    <input class="form-control font-style" name="city" type="text"
                                        value="{{ $user->city }}" id="city">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="state" class="form-label">{{ __('State') }}</label>
                                    <input class="form-control font-style" name="state" type="text"
                                        value="{{ $user->state }}" id="state">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="zipcode" class="form-label">{{ __('Zip/Post Code') }}</label>
                                    <input class="form-control" name="zipcode" type="text" value="{{ $user->zipcode }}"
                                        id="zipcode">
                                </div>
                                <div class="form-group  col-md-6">
                                    <label for="country" class="form-label">{{ __('Country') }}</label>
                                    <input class="form-control font-style" name="country" type="text"
                                        value="{{ $user->country }}" id="country">
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="telephone" class="form-label">{{ __('Telephone') }}</label>
                                    <input class="form-control" type="telephone" id="telephone" name="telephone"
                                        pattern="^\+\d{1,3}\d{9,13}$"
                                        value="{{ isset($user->telephone) ? $user->telephone : '' }}">
                                    <div class=" text-xs text-danger">
                                        {{ __('Please use with country code. (ex. +91)') }}
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="text-end">
                                    <button type="submit" class="btn-submit btn btn-primary">
                                        {{ __('Save Changes') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endauth

            @if ($data['hasPermission'])
                <div class="card" id="authentication-sidenav">
                    <div class="card-header">
                        <h5 class="mb-0">{{ __('Two Factor Authentication') }}</h5>
                    </div>
                    <div class="card-body">
                        <p>{{ __('Two factor authentication (2FA) strengthens access security by requiring two methods (also referred to as factors) to verify your identity. Two factor authentication protects against phishing, social engineering and password brute force attacks and secures your logins from attackers exploiting weak or stolen credentials.') }}
                        </p>
                        @if ($data['user']->google2fa_secret == null)
                            <form class="form-horizontal" method="POST" action="{{ route('generate2faSecret') }}">
                                {{ csrf_field() }}
                                <div class="col-lg-12 text-center">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Generate Secret Key to Enable 2FA') }}
                                    </button>
                                </div>
                            </form>
                        @elseif($data['user']->google2fa_enable == 0 && $data['user']->google2fa_secret != null)
                            1. {{ __('Install “Google Authentication App” on your') }} <a
                                href="https://apps.apple.com/us/app/google-authenticator/id388497605" target="_black">
                                {{ __('IOS') }}</a> {{ __('or') }} <a
                                href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2"
                                target="_black">{{ __('Android phone.') }}</a><br />
                            2. {{ __('Open the Google Authentication App and scan the below QR code.') }}<br />
                            @php
                                $f = finfo_open();
                                $mime_type = finfo_buffer($f, $data['google2fa_url'], FILEINFO_MIME_TYPE);
                            @endphp
                            @if ($mime_type == 'text/plain')
                                <img src="{{ $data['google2fa_url'] }}" alt="">
                            @else
                                {!! $data['google2fa_url'] !!}
                            @endif
                            <br /><br />
                            {{ __('Alternatively, you can use the code:') }} <code>{{ $data['secret'] }}</code>.<br />
                            3. {{ __('Enter the 6-digit Google Authentication code from the app') }}<br /><br />
                            <form class="form-horizontal needs-validation" novalidate method="POST"
                                action="{{ route('enable2fa') }}">
                                {{ csrf_field() }}
                                <div class="form-group{{ $errors->has('verify-code') ? ' has-error' : '' }}">
                                    <label for="secret" class="col-form-label">{{ __('Authenticator Code') }}</label>
                                    <input id="secret" type="password" class="form-control" name="secret"
                                        required="required">
                                </div>
                                <div class="col-lg-12 text-center">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Enable 2FA') }}
                                    </button>
                                </div>
                            </form>
                        @elseif($data['user']->google2fa_enable == 1 && $data['user']->google2fa_secret != null)
                            <div class="alert alert-success">
                                {{ __('2FA is currently') }} <strong>{{ __('Enabled') }}</strong>
                                {{ __('on your account.') }}
                            </div>
                            <p>{{ __('If you are looking to disable Two Factor Authentication. Please confirm your password and Click Disable 2FA Button.') }}
                            </p>

                            <form class="form-horizontal needs-validation" novalidate method="POST"
                                action="{{ route('disable2fa') }}">
                                {{ csrf_field() }}

                                <div class="form-group{{ $errors->has('current-password') ? ' has-error' : '' }}">
                                    <label for="change-password"
                                        class="col-form-label">{{ __('Current Password') }}</label>
                                    <input id="current-password" type="password" class="form-control"
                                        name="current-password" required="required">
                                    @if ($errors->has('current-password'))
                                        <span class="help-block">
                                            <strong class="text-danger">{{ $errors->first('current-password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                <div class="col-lg-12 text-center">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Disable 2FA') }}
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </div>
            @endif
        </div> <!-- end col -->
    </div> <!-- end row -->
@endsection
@push('scripts')
    <script type="text/javascript">
        $('#avatar').change(function() {
            let reader = new FileReader();
            reader.onload = (e) => {
                $('#myAvatar').attr('src', e.target.result);
            }
            reader.readAsDataURL(this.files[0]);
        });
    </script>
    <script>
        $(document).on('click', '.list-group-item', function() {
            $('.list-group-item').removeClass('active');
            $('.list-group-item').removeClass('text-primary');
            setTimeout(() => {
                $(this).addClass('active').removeClass('text-primary');
            }, 10);
        });

        var type = window.location.hash.substr(1);
        $('.list-group-item').removeClass('active');
        $('.list-group-item').removeClass('text-primary');
        if (type != '') {
            $('a[href="#' + type + '"]').addClass('active').removeClass('text-primary');
        } else {
            $('.list-group-item:eq(0)').addClass('active').removeClass('text-primary');
        }

        var scrollSpy = new bootstrap.ScrollSpy(document.body, {
            target: '#useradd-sidenav',
            offset: 300
        })
    </script>
@endpush
