@extends('layouts.admin')

@section('page-title')
    @if (\Auth::user()->type == 'admin')
        {{ __('Manage Companies') }}
    @else
        {{ __('Users') }}
    @endif
@endsection

@php
    $logo = \App\Models\Utility::get_file('avatars/');
@endphp

@section('links')
    @if (\Auth::guard('client')->check())
        <li class="breadcrumb-item"><a href="{{ route('client.home') }}">{{ __('Home') }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    @endif
    @if (\Auth::user()->type == 'admin')
        <li class="breadcrumb-item"> {{ __('company') }}</li>
    @else
        <li class="breadcrumb-item"> {{ __('users') }}</li>
    @endif
@endsection

@section('action-button')
    @auth('web')
        @if (Auth::user()->type == 'admin')
            <a href="{{ route('user.export') }}" class="btn btn-sm btn-primary " data-toggle="tooltip"
                title="{{ __('Export') }}">
                <i class="ti ti-file-x"></i>
            </a>
            <a href="#" class="btn btn-sm btn-primary " data-ajax-popup="true" data-size="md"
                data-title="{{ __('Import Companies') }}" data-url="{{ route('user.file.import') }}" data-toggle="tooltip"
                title="{{ __('Import') }}">
                <i class="ti ti-file-import"></i>
            </a>
            <a href="#" class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="md"
                data-title="{{ __('Create Company') }}" data-url="{{ route('users.create') }}" data-toggle="tooltip"
                title="{{ __('Create') }}">
                <i class="ti ti-plus"></i>
            </a>
        @elseif(isset($currentWorkspace) && $currentWorkspace->creater->id == Auth::id())
            <a href="{{ route('users_logs.index', $currentWorkspace->slug) }}" class="btn btn-sm btn-primary "
                data-title="{{ __('User Logs') }}" data-toggle="tooltip" title="{{ __('User Logs') }}">
                <i class="ti ti-user-check"></i>
            </a>
            <a href="#" class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="md"
                data-title="{{ __('Invite User') }}" data-url="{{ route('users.invite', $currentWorkspace->slug) }}"
                data-toggle="tooltip" title="{{ __('Invite') }}">
                <i class="ti ti-plus"></i>
            </a>
        @endif
    @endauth
@endsection

@section('content')
    @if ((isset($currentWorkspace) && $currentWorkspace) || Auth::user()->type == 'admin')
        <div class="row row-gap-2 mb-4">
            @foreach ($users as $user)
                @php($workspace_id = isset($currentWorkspace) && $currentWorkspace ? $currentWorkspace->id : '')
                <div class="col-xxl-3 col-xl-4 col-md-6">
                    <div class="card user-card">
                        <div class="card-header border-bottom">
                            <div class="d-flex align-items-center">
                                <h6 class="mb-0">
                                    @if (Auth::user()->type != 'admin')
                                        @if ($user->permission == 'Owner')
                                            <div class="badge p-2 px-3 bg-success">{{ __('Owner') }}</div>
                                        @else
                                            <div class="badge p-2 px-3 bg-warning">{{ __('Member') }}</div>
                                        @endif
                                    @endif
                                </h6>
                            </div>
                            @if (Auth::user()->type == 'admin' && Auth::user()->id != $user->id)
                                <div class="card-header-right">
                                    <div class="btn-group card-option">
                                        <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                            <i class="feather icon-more-vertical"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end">

                                            <a href="#" class="dropdown-item" data-ajax-popup="true" data-size="md"
                                                data-title="{{ __('Reset Password') }}"
                                                data-url="{{ route('users.reset.password', $user->id) }}">
                                                <i class="ti ti-key"></i>
                                                <span> {{ __('Reset Password') }}</span>
                                            </a>

                                            <a href="{{ route('login.with.admin', $user->id) }}" class="dropdown-item">
                                                <i class="ti ti-replace"></i>
                                                <span> {{ __('Login As Company') }}</span>
                                            </a>

                                            <a href="#" class="dropdown-item bs-pass-para "
                                                data-confirm="{{ __('Are You Sure?') }}"
                                                data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                data-confirm-yes="delete_user_{{ $user->id }}">
                                                <i class="ti ti-trash"></i>
                                                <span> {{ __('Delete') }}</span>
                                            </a>
                                            <form action="{{ route('users.delete', $user->id) }}" method="post"
                                                id="delete_user_{{ $user->id }}" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>

                                            @if ($user->is_enable_login == 1)
                                                <a href="{{ route('login.manage', ['userId' => \Crypt::encrypt($user->id)]) }}"
                                                    class="dropdown-item">
                                                    <i class="ti ti-road-sign"></i>
                                                    <span class="text-danger"> {{ __('Login Disable') }}</span>
                                                </a>
                                            @elseif($user->is_enable_login == 0 && $user->password == null)
                                                <a href="#" data-url="{{ route('users.reset.password', $user->id) }}"
                                                    data-ajax-popup="true" data-size="md" class="dropdown-item login_enable"
                                                    data-title="{{ __('Set Password') }}"
                                                    onclick="setTimeout(appendHiddenInput, 2000)">
                                                    <i class="ti ti-road-sign"></i>
                                                    <span class="text-success"> {{ __('Login Enable') }}</span>
                                                </a>
                                            @else
                                                <a href="{{ route('login.manage', ['userId' => \Crypt::encrypt($user->id)]) }}"
                                                    class="dropdown-item">
                                                    <i class="ti ti-road-sign"></i>
                                                    <span class="text-success"> {{ __('Login Enable') }}</span>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if (isset($currentWorkspace) &&
                                    $currentWorkspace &&
                                    $currentWorkspace->permission == 'Owner' &&
                                    Auth::user()->id != $user->id)
                                @if ($user->is_active == 1)
                                    <div class="card-header-right">
                                        <div class="btn-group card-option">
                                            <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                                <i class="feather icon-more-vertical"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end"
                                                data-popper-placement="bottom-end">
                                                @if (isset($currentWorkspace) &&
                                                        $currentWorkspace &&
                                                        $currentWorkspace->permission == 'Owner' &&
                                                        Auth::user()->id != $user->id)
                                                    <a href="#" class="dropdown-item" data-ajax-popup="true"
                                                        data-size="md" data-title="{{ __('Update User') }}"
                                                        data-url="{{ route('users.edit', [$currentWorkspace->slug, $user->id]) }}">
                                                        <i class="ti ti-pencil"></i>
                                                        <span> {{ __('Edit') }}</span>
                                                    </a>

                                                    <a href="#" class="dropdown-item" data-ajax-popup="true"
                                                        data-size="md" data-title="{{ __('Reset Password') }}"
                                                        data-url="{{ route('users.reset.password', $user->id) }}">
                                                        <i class="ti ti-key"></i>
                                                        <span> {{ __('Reset Password') }}</span>
                                                    </a>

                                                    <a href="#" class="dropdown-item bs-pass-para"
                                                        data-confirm="{{ __('Are You Sure?') }}"
                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="remove_user_{{ $user->id }}">
                                                        <i class="ti ti-trash"></i>
                                                        <span> {{ __('Remove user From Workspace') }}</span>
                                                    </a>
                                                    <form
                                                        action="{{ route('users.remove', [$currentWorkspace->slug, $user->id]) }}"
                                                        method="post" id="remove_user_{{ $user->id }}"
                                                        style="display: none;">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>

                                                    @if ($user->is_enable_login == 1)
                                                        <a href="{{ route('login.manage', ['userId' => \Crypt::encrypt($user->id)]) }}"
                                                            class="dropdown-item">
                                                            <i class="ti ti-road-sign"></i>
                                                            <span class="text-danger"> {{ __('Login Disable') }}</span>
                                                        </a>
                                                    @elseif($user->is_enable_login == 0 && $user->password == null)
                                                        <a href="#"
                                                            data-url="{{ route('users.reset.password', $user->id) }}"
                                                            data-ajax-popup="true" data-size="md"
                                                            class="dropdown-item login_enable"
                                                            data-title="{{ __('Set Password') }}"
                                                            onclick="setTimeout(appendHiddenInput, 2000)">
                                                            <i class="ti ti-road-sign"></i>
                                                            <span class="text-success"> {{ __('Login Enable') }}</span>
                                                        </a>
                                                    @else
                                                        <a href="{{ route('login.manage', ['userId' => \Crypt::encrypt($user->id)]) }}"
                                                            class="dropdown-item">
                                                            <i class="ti ti-road-sign"></i>
                                                            <span class="text-success"> {{ __('Login Enable') }}</span>
                                                        </a>
                                                    @endif
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
                            @endif
                        </div>

                        <div class="card-body text-center">
                            <div class="user-image rounded border-2 border border-primary m-auto">
                                <img alt="user-image" class="h-100 w-100"
                                    @if ($user->avatar) src="{{ asset($logo . $user->avatar) }}"
                                    @else
                                      avatar="{{ $user->name }}" @endif>
                            </div>
                            <h4 class="mt-2">{{ $user->name }}</h4>
                            <small>{{ $user->email }}</small>

                            <div class="mt-4">
                                <div class="row d-flex justify-content-around">
                                    @if (Auth::user()->type == 'admin')
                                        <div class="col-6">
                                            <h6 class="mb-0 px-3">{{ $user->countWorkspace() }}</h6>
                                            <p class="text-muted text-sm mb-0">{{ __('Workspaces') }}</p>
                                        </div>
                                        <div class="col-6">
                                            <h6 class="mb-0 px-3">{{ $user->countUsers($workspace_id) }}</h6>
                                            <p class="text-muted text-sm mb-0">{{ __('Users') }}</p>
                                        </div>
                                        <div class="col-6 mt-2">
                                            <h6 class="mb-0 px-3">{{ $user->countClients($workspace_id) }}</h6>
                                            <p class="text-muted text-sm mb-0">{{ __('Clients') }}</p>
                                        </div>
                                        <div class="col-6  mt-2">
                                            <h6 class="mb-0 px-3">{{ $user->countProject($workspace_id) }}</h6>
                                            <p class="text-muted text-sm mb-0">{{ __('Projects') }}</p>
                                        </div>
                                        <div class="col-12 text-center Id mt-3">
                                            <a href="#" data-url="{{ route('company.info', $user->id) }}"
                                                data-size="lg" data-ajax-popup="true" class="btn btn-outline-primary"
                                                data-title="{{ __('Company Info') }}">{{ __('AdminHub') }}</a>
                                        </div>
                                    @else
                                        <div class="col-6">
                                            <h6 class="mb-0 px-3">{{ $user->countProject($workspace_id) }}</h6>
                                            <p class="text-muted text-sm mb-0">{{ __('Projects') }}</p>
                                        </div>
                                        <div class="col-6">
                                            <h6 class="mb-0 px-3">{{ $user->countTask($workspace_id) }}</h6>
                                            <p class="text-muted text-sm mb-0">{{ __('Tasks') }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            @auth('web')
                <div class="col-xxl-3 col-xl-4 col-md-6">
                    @if (Auth::user()->type == 'admin')
                        <a href="#" class="btn-addnew-project border-primary p-4" data-ajax-popup="true"
                            data-size="md" data-title="{{ __('Create Company') }}" data-url="{{ route('users.create') }}">
                            <div class="bg-primary proj-add-icon" data-toggle="tooltip" title="{{ __('Create') }}">
                                <i class="ti ti-plus my-2"></i>
                            </div>
                            <h6 class="mt-4 mb-2">{{ __('New Company') }}</h6>
                            <p class="text-muted text-center">{{ __('Click here to add New Company') }}</p>
                        </a>
                    @elseif(isset($currentWorkspace) && $currentWorkspace->creater->id == Auth::id())
                        <a href="#" class="btn-addnew-project border-primary p-4" data-ajax-popup="true"
                            data-size="md" data-title="{{ __('Invite User') }}"
                            data-url="{{ route('users.invite', $currentWorkspace->slug) }}">
                            <div class="bg-primary proj-add-icon" data-toggle="tooltip" title="{{ __('Invite') }}">
                                <i class="ti ti-plus my-2"></i>
                            </div>
                            <h6 class="mt-4 mb-2">{{ __('Invite User') }}</h6>
                            <p class="text-muted text-center">{{ __('Click here to Invite User') }}</p>
                        </a>
                    @endif
                </div>
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
                                    <a class="btn-return-home badge-blue" href="{{ route('home') }}">
                                        <i class="fas fa-reply"></i> {{ __('Return Home') }}</a>
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

            var modalBody = document.querySelector('.resetPasswordForm');
            // console.log(modalBody);
            if (modalBody) {
                modalBody.appendChild(hiddenInput);
            }
        }
        $(".fc-daygrid-event fc-daygrid-block-event fc-h-event fc-event fc-event-draggable fc-event-resizable fc-event-end fc-event-past bg-danger border-danger")
            .click(function() {
                alert("Handler for .click() called.");
            });
    </script>
@endpush
