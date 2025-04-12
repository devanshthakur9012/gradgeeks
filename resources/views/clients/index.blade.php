@extends('layouts.admin')

@section('page-title')
    {{ __('Clients') }}
@endsection

@section('links')
    @if (\Auth::guard('client')->check())
        <li class="breadcrumb-item"><a href="{{ route('client.home') }}">{{ __('Home') }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    @endif
    <li class="breadcrumb-item"> {{ __('Clients') }}</li>
@endsection

@php
    $logo = \App\Models\Utility::get_file('users-avatar/');
@endphp

@section('action-button')
    @auth('web')
        <a href="{{ route('client.export') }}" class="btn btn-sm btn-primary " data-toggle="tooltip"
            title="{{ __('Export') }}">
            <i class="ti ti-file-x"></i>
        </a>

        <a href="#" class="btn btn-sm btn-primary " data-ajax-popup="true" data-size="md"
            data-title="{{ __('Import Client') }}" data-url="{{ route('client.file.import', $currentWorkspace->slug) }}"
            data-toggle="tooltip" title="{{ __('Import') }}">
            <i class="ti ti-file-import"></i>
        </a>

        @if (isset($currentWorkspace) && $currentWorkspace->creater->id == Auth::id())
            <a href="#" class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="lg"
                data-title="{{ __('Create Client') }}" data-url="{{ route('clients.create', $currentWorkspace->slug) }}"
                data-toggle="tooltip" title="{{ __('Create') }}">
                <i class="ti ti-plus"></i>
            </a>
        @endif
    @endauth
@endsection

@section('content')
    @if ($currentWorkspace)
        <div class="row row-gap-2 mb-4">
            @foreach ($clients as $client)
                <div class="col-xl-3 col-lg-4 col-sm-6">
                    <div class="card user-card">
                        <div class="card-header">
                            @if ($client->is_active)
                                <div class="card-header-right">
                                    <div class="btn-group card-option">

                                        <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <i class="feather icon-more-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a href="#" class="dropdown-item" data-ajax-popup="true" data-size="lg"
                                                data-title="{{ __('Update Client') }}"
                                                data-url="{{ route('clients.edit', [$currentWorkspace->slug, $client->id]) }}"><i
                                                    class="ti ti-pencil"></i><span> {{ __('Edit') }}</span>
                                            </a>

                                            <a href="#" class="dropdown-item" data-ajax-popup="true" data-size="md"
                                                data-title="{{ __('Reset Password') }}"
                                                data-url="{{ route('client.reset.password', [$currentWorkspace->slug, $client->id]) }}"><i
                                                    class="ti ti-key"></i><span> {{ __('Reset Password') }}</span>
                                            </a>

                                            <a href="#" class="dropdown-item bs-pass-para"
                                                data-confirm="{{ __('Are You Sure?') }}"
                                                data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                data-confirm-yes="delete-form-{{ $client->id }}"><i
                                                    class="ti ti-trash"></i><span> {{ __('Delete') }}</span>
                                            </a>

                                            {!! Form::open([
                                                'method' => 'DELETE',
                                                'route' => ['clients.destroy', [$currentWorkspace->slug, $client->id]],
                                                'id' => 'delete-form-' . $client->id,
                                            ]) !!}
                                            {!! Form::close() !!}

                                            @if ($client->is_enable_login == 1)
                                                <a href="{{ route('client.login.manage', ['clientId' => \Crypt::encrypt($client->id)]) }}"
                                                    class="dropdown-item">
                                                    <i class="ti ti-road-sign"></i>
                                                    <span class="text-danger"> {{ __('Login Disable') }}</span>
                                                </a>
                                            @elseif($client->is_enable_login == 0 && $client->password == null)
                                                <a data-url="{{ route('client.reset.password', [$currentWorkspace->slug, $client->id]) }}"
                                                    data-ajax-popup="true" data-size="md" class="dropdown-item login_enable"
                                                    data-title="{{ __('Set Password') }}"
                                                    onclick="setTimeout(appendHiddenInput, 2000)">
                                                    <i class="ti ti-road-sign"></i>
                                                    <span class="text-success"> {{ __('Login Enable') }}</span>
                                                </a>
                                            @else
                                                <a href="{{ route('client.login.manage', ['clientId' => \Crypt::encrypt($client->id)]) }}"
                                                    class="dropdown-item">
                                                    <i class="ti ti-road-sign"></i>
                                                    <span class="text-success"> {{ __('Login Enable') }}</span>
                                                </a>
                                            @endif


                                        </div>

                                    </div>
                                </div>
                            @else
                                <div class="card-header-right">
                                    <div class="btn-group card-option">
                                        <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown"
                                            aria-haspopup="true" aria-expanded="false">
                                            <i class="ti ti-lock"></i>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="card-body text-center">
                            <div class="user-image rounded border-2 border border-primary m-auto">
                                <img alt="user-image" class="h-100 w-100"
                                    @if ($client->avatar) src="{{ asset($logo . $client->avatar) }}" @else avatar="{{ $client->name }}" @endif>
                            </div>
                            <h4 class="mt-2">{{ $client->name }}</h4>
                            <span class="text-dark text-md">{{ $client->email }}</span>
                        </div>
                    </div>
                </div>
            @endforeach

            @auth('web')
                @if (isset($currentWorkspace) && $currentWorkspace->creater->id == Auth::id())
                    <div class="col-xl-3 col-lg-4 col-sm-6">
                        <a href="#" class="btn-addnew-project border-primary p-4" data-ajax-popup="true" data-size="lg"
                            data-title="{{ __('Create Client') }}"
                            data-url="{{ route('clients.create', $currentWorkspace->slug) }}">
                            <div class="bg-primary proj-add-icon" data-toggle="tooltip" title="{{ __('Create') }}">
                                <i class="ti ti-plus my-2"></i>
                            </div>
                            <h6 class="mt-4 mb-2">{{ __('New Client') }}</h6>
                            <p class="text-muted text-center">{{ __('Click here to Create New Client') }}</p>
                        </a>
                    </div>
                @endif
            @endauth
        </div>
    @else
        <div class="container mt-5">
            <div class="card">
                <div class="card-body p-4">
                    <div class="page-error">
                        <div class="page-inner">
                            <h1>404</h1>
                            <div class="page-description">
                                {{ __('Page Not Found') }}
                            </div>
                            <div class="page-search">
                                <p class="text-muted mt-3">
                                    {{ __("It's looking like you may have taken a wrong turn. Don't worry... it happens to the best of us. Here's a little tip that might help you get back on track.") }}
                                </p>
                                <div class="mt-3">
                                    <a class="btn-return-home badge-blue" href="{{ route('home') }}"><i
                                            class="fas fa-reply"></i> {{ __('Return Home') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $(document).on('change', '#password_switch', function() {
                if ($(this).is(':checked')) {
                    $('.ps_div').removeClass('d-none');
                    $('#password').attr("required", true);
                } else {
                    $('.ps_div').addClass('d-none');
                    $('#password').val(null);
                    $('#password').removeAttr("required");
                }
            });
        });

        function appendHiddenInput() {
            var hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'login_enable';
            hiddenInput.value = 'true';

            var modalBody = document.querySelector('.client-reset-password');
            if (modalBody) {
                modalBody.appendChild(hiddenInput);
            }
        }
    </script>
@endpush
