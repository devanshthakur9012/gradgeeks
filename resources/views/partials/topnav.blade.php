@php
    $unseenCounter = App\Models\ChMessage::where('to_id', Auth::user()->id)
        ->where('seen', 0)
        ->count();
    $logo = \App\Models\Utility::get_file('avatars/');
@endphp

@php
    $languages = \App\Models\Utility::languages();
    if (Auth::user()->type == 'admin') {
        $setting = App\Models\Utility::getAdminPaymentSettings();
        if ($setting['color']) {
            $color = $setting['color'];
        } else {
            $color = 'theme-3';
        }
        $dark_mode = $setting['cust_darklayout'];
        $cust_theme_bg = $setting['cust_theme_bg'];
        $SITE_RTL = $setting['site_rtl'];
    } else {
        $adminSetting = App\Models\Utility::getAdminPaymentSettings();
        $setting = App\Models\Utility::getcompanySettings($currentWorkspace->id);
        $color = $setting->theme_color;
        $dark_mode = $setting->cust_darklayout;
        $SITE_RTL = $setting->site_rtl;
        $cust_theme_bg = $setting->cust_theme_bg;
    }

    if ($color == '' || $color == null) {
        $settings = App\Models\Utility::getAdminPaymentSettings();
        $color = $settings['color'];
    }

    if ($dark_mode == '' || $dark_mode == null) {
        $dark_mode = $settings['cust_darklayout'];
    }

    if ($cust_theme_bg == '' || $dark_mode == null) {
        $cust_theme_bg = $settings['cust_theme_bg'];
    }

    if ($SITE_RTL == '' || $SITE_RTL == null) {
        $SITE_RTL = env('SITE_RTL');
    }

    $currantLang = basename(App::getLocale());
    if ($currantLang == '') {
        $currantLang = 'en';
    }
@endphp


<style type="text/css">
    .top_header {
        left: auto !important;
        top: 60px !important;
    }

    .noti-body {
        height: 300px;
        overflow: auto;
    }
</style>

<header class="dash-header {{ isset($cust_theme_bg) && $cust_theme_bg == 'on' ? 'transprent-bg' : '' }}">
    <div class="header-wrapper">
        <div class="dash-mob-drp">
            <ul class="list-unstyled">
                <li class="dash-h-item mob-hamburger">
                    <a href="#!" class="dash-head-link" id="mobile-collapse">
                        <div class="hamburger hamburger--arrowturn">
                            <div class="hamburger-box">
                                <div class="hamburger-inner"></div>
                            </div>
                        </div>
                    </a>
                </li>
                @if (Auth::user()->type != 'admin')
                    <li class="dropdown dash-h-item">
                        <a class="dash-head-link dropdown-toggle arrow-none ms-0" data-bs-toggle="dropdown"
                            href="#" role="button" aria-haspopup="false" aria-expanded="false">
                            <i class="ti ti-search"></i>
                        </a>
                        <div class="dropdown-menu dash-h-dropdown drp-search drp-search-custom">
                            <form class="form-inline mr-auto mb-0">
                                <div class="search-element">
                                    <input class="" type="type here" placeholder="Search here Project & Task. . ."
                                        aria-label="Search">
                                    <div class="search-backdrop"></div>
                                </div>
                            </form>
                        </div>
                    </li>
                @endif
                <li class="dropdown dash-h-item drp-company">
                    <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#"
                        role="button" aria-haspopup="false" aria-expanded="false">
                        <img class="theme-avtar"
                            @if (Auth::user()->avatar) src="{{ asset($logo . Auth::user()->avatar) }}" @else avatar="{{ Auth::user()->name }}" @endif
                            alt="{{ Auth::user()->name }}">
                        <span class="hide-mob ms-2">{{ __('Hi') }}, {{ Auth::user()->name }} !</span>
                        <i class="ti ti-chevron-down drp-arrow nocolor hide-mob"></i>
                    </a>
                    <div class="dropdown-menu dash-h-dropdown">

                        @php
                            $login_status = false;
                        @endphp

                        @foreach (Auth::user()->workspace as $workspace)
                            @if ($workspace->is_active)

                                @php
                                    $user = Auth::user();
                                    $userWorkspace = App\Models\UserWorkspace::where([
                                        ['user_id', $user->id],
                                        ['workspace_id', $workspace->id],
                                    ])->first();
                                @endphp

                                @if (isset($userWorkspace))
                                    @if ($userWorkspace->is_active == 1)
                                        @php
                                            $login_status = true;
                                        @endphp
                                        <a href="@if ($currentWorkspace->id == $workspace->id) #@else @auth('web'){{ route('change-workspace', $workspace->id) }}@elseauth{{ route('client.change-workspace', $workspace->id) }}@endauth @endif"
                                            title="{{ $workspace->name }}" class="dropdown-item">
                                            @if ($currentWorkspace->id == $workspace->id)
                                                <i class="ti ti-checks text-success"></i>
                                            @endif
                                            <span>{{ $workspace->name }}</span>
                                            @if (isset($workspace->pivot->permission))
                                                @if ($workspace->pivot->permission == 'Owner')
                                                    <span
                                                        class="badge bg-primary">{{ __($workspace->pivot->permission) }}</span>
                                                @else
                                                    <span class="badge bg-dark">{{ __('Shared') }}</span>
                                                @endif
                                            @endif
                                        </a>
                                    @endif
                                @endif
                            @else
                                <a href="#" class="dropdown-item" title="{{ __('Locked') }}">
                                    <i class="ti ti-lock"></i>
                                    <span>{{ $workspace->name }}</span>
                                    @if (isset($workspace->pivot->permission))
                                        @if ($workspace->pivot->permission == 'Owner')
                                            <span
                                                class="badge badge-success-primary">{{ __($workspace->pivot->permission) }}</span>
                                        @else
                                            <span class="badge bg-dark">{{ __('Shared') }}</span>
                                        @endif
                                    @endif
                                </a>
                            @endif
                        @endforeach

                        {{-- For Client  --}}
                        @if (Auth::user()->getGuard() == 'client')
                            @php
                                $client = Auth::user();
                            @endphp

                            @foreach ($client->workspace as $workspace)
                                @if ($workspace->is_active == 1)
                                    @php
                                        $clientWorkspace = App\Models\ClientWorkspace::where([
                                            ['client_id', $client->id],
                                            ['workspace_id', $workspace->id],
                                        ])->first();
                                    @endphp
                                    @if (isset($clientWorkspace))
                                        @php
                                            $login_status = true;
                                        @endphp
                                        @if ($clientWorkspace->is_active == 1)
                                            <a href="@if ($currentWorkspace->id == $workspace->id) #@else @auth('web'){{ route('change-workspace', $workspace->id) }}@elseauth{{ route('client.change-workspace', $workspace->id) }}@endauth @endif"
                                                title="{{ $workspace->name }}" class="dropdown-item">
                                                @if ($currentWorkspace->id == $workspace->id)
                                                    <i class="ti ti-checks text-success"></i>
                                                @endif
                                                <span>{{ $workspace->name }}</span>
                                                @if (isset($workspace->pivot->permission))
                                                    @if ($workspace->pivot->permission == 'Owner')
                                                        <span
                                                            class="badge bg-primary">{{ __($workspace->pivot->permission) }}</span>
                                                    @else
                                                        <span class="badge bg-dark">{{ __('Shared') }}</span>
                                                    @endif
                                                @endif
                                            </a>
                                        @endif
                                    @endif
                                @endif
                            @endforeach

                        @endif

                        <!--   <hr class="dropdown-divider" /> -->
                        {{-- @auth('web')
                            @if (Auth::user()->type == 'user' && $workspace->pivot->permission == 'Owner')
                                <a href="#!" class="dropdown-item" data-toggle="modal"
                                    data-target="#modelCreateWorkspace">
                                    <i class="ti ti-circle-plus"></i>
                                    <span>{{ __('Create New Workspace') }}</span>
                                </a>
                            @endif
                        @endauth --}}

                        @auth('web')
                            @foreach (Auth::user()->workspace as $workspace)
                                @if ($currentWorkspace->id == $workspace->id)
                                    @if ($workspace->pivot->permission == 'Owner')
                                        <a href="#!" class="dropdown-item" data-toggle="modal"
                                            data-target="#modelCreateWorkspace">
                                            <i class="ti ti-circle-plus"></i>
                                            <span>{{ __('Create New Workspace') }}</span>
                                        </a>
                                    @endif
                                @endif
                            @endforeach
                        @endauth


                        @if (isset($currentWorkspace) && $currentWorkspace)
                            @auth('web')
                                @if (Auth::user()->id == $currentWorkspace->created_by)
                                    <a href="#" class="dropdown-item bs-pass-para"
                                        data-confirm="{{ __('Are You Sure?') }}"
                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                        data-confirm-yes="remove-workspace-form">
                                        <i class="ti ti-circle-x"></i>
                                        <span>{{ __('Remove Me From This Workspace') }}</span>
                                    </a>
                                    <form id="remove-workspace-form"
                                        action="{{ route('delete-workspace', ['id' => $currentWorkspace->id]) }}"
                                        method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                @else
                                    <a href="#" class="dropdown-item bs-pass-para"
                                        data-confirm="{{ __('Are You Sure?') }}"
                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                        data-confirm-yes="remove-workspace-form">
                                        <i class="ti ti-circle-x"></i>
                                        <span>{{ __('Leave Me From This Workspace') }}</span>
                                    </a>
                                    <form id="remove-workspace-form"
                                        action="{{ route('leave-workspace', ['id' => $currentWorkspace->id]) }}"
                                        method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                @endif
                            @endauth
                        @endif

                        <a href="@auth('web'){{ route('users.my.account') }}@elseauth{{ route('client.users.my.account') }}@endauth"
                            class="dropdown-item">
                            <i class="ti ti-user"></i>
                            <span>{{ __('My Profile') }}</span>
                        </a>

                        @if (env('CHAT_MODULE') == 'on')
                            @if (\Auth::user()->type == 'user')
                                <a href="{{ url('chats') }}" class="dropdown-item">
                                    <i class="ti ti-message-circle"></i>
                                    <span>{{ __('Chats') }}</span>
                                </a>
                            @endif
                        @endif

                        <a href="#" class="dropdown-item "
                            onclick="event.preventDefault();document.getElementById('logout-form1').submit();">
                            <i class="ti ti-power"></i>
                            <span>{{ __('Logout') }}</span>
                        </a>

                        <form id="logout-form1"
                            action="@auth('web'){{ route('logout') }}@elseauth{{ route('client.logout') }}@endauth"
                            method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </li>
            </ul>
        </div>
        <!-- Brand + Toggler (for mobile devices) -->

        <div class="ms-auto">
            <ul class="list-unstyled">
                @if (Auth::user()->getGuard() != 'client')
                    @impersonating($guard = null)
                        <li class="dropdown dash-h-item drp-company">
                            <a class="btn company-login-btn btn-danger btn-sm me-3" href="{{ route('exit.admin') }}">
                                <i class="ti ti-ban"></i>
                                <span> {{ __('Exit Company Login') }}</span>
                            </a>
                        </li>
                    @endImpersonating
                @endif

                @if (\Auth::user()->type == 'user')
                    @if ($adminSetting['enable_chat'] == 'on')
                        <li class="dash-h-item">
                            <a class="dash-head-link me-0" href="{{ url('chats') }}">
                                <i class="ti ti-message-circle"></i>
                                <span
                                    class="bg-danger dash-h-badge message-counter custom_messanger_counter">{{ $unseenCounter }}
                                    <span class="sr-only"></span>
                                </span>
                            </a>
                        </li>
                    @endif
                @endif

                @if (\Auth::user()->type == 'user')
                    <li class="dropdown dash-h-item drp-notification">
                        @if (isset($currentWorkspace) && $currentWorkspace)
                            @auth('web')
                                @php
                                    $notifications = Auth::user()->notifications($currentWorkspace->id);
                                @endphp

                                <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                                    href="#" role="button" aria-haspopup="false" aria-expanded="false">
                                    <i class="ti ti-bell"></i>
                                    <span class="@if (count($notifications) > 0) bg-danger dash-h-badge dots @endif">
                                        <span class="sr-only"></span>
                                    </span>
                                </a>
                                <div class="dropdown-menu dash-h-dropdown dropdown-menu-end notification_menu_all">
                                    <div class="noti-header">
                                        <h5 class="m-0">Notification</h5>
                                        <a href="#"
                                            data-url="{{ route('delete_all.notifications', $currentWorkspace->slug) }}"
                                            class="dash-head-link clear_all_notifications">Clear All</a>
                                    </div>
                                    <div class="noti-body">
                                        <div class="limited">
                                            @foreach ($notifications as $notification)
                                                @php
                                                    $project = $notification->project;
                                                    $task = $notification->task;
                                                    $notifyingUser = $notification->user;

                                                    // Define variables for the notification data
                                                    $projectTitle = $project ? $project->title : '';
                                                    $taskTitle = $task ? $task->title : '';
                                                    $notifyingUserName = $notifyingUser->name;

                                                    // Define other variables you need for HTML
                                                    $link = ''; // Replace with the actual link
                                                    $name = ''; // Replace with the notification icon or name
                                                    $text = ''; // Replace with the notification text
                                                    $date = $notification->created_at->diffForHumans();
                                                    $data = json_decode($notification->data);
                                                @endphp
                                                @if ($notification->user && trim($notification->user->name) != '')
                                                    @php
                                                        $name = '';
                                                        $nameParts = explode(' ', $notification->user->name);
                                                    @endphp

                                                    @foreach ($nameParts as $word)
                                                        @php
                                                            $name .= strtoupper($word[0]);
                                                        @endphp
                                                    @endforeach
                                                @endif

                                                @if ($notification->type == 'task_assign')
                                                    @php
                                                        if ($project) {
                                                            $link = route('projects.task.board', [
                                                                $notification->workspace_id,
                                                                $notification->project_id,
                                                            ]);

                                                            $text =
                                                                __('New task assign') .
                                                                ' <b>' .
                                                                $data->title .
                                                                '</b> ' .
                                                                __('in project') .
                                                                ' <b>' .
                                                                $project->name .
                                                                '</b>';
                                                            $icon = 'fa fa-clock-o';
                                                            if ($data->priority == 'Low') {
                                                                $icon_color = 'bg-success';
                                                            } elseif ($data->priority == 'High') {
                                                                $icon_color = 'bg-danger';
                                                            }
                                                        } else {
                                                            // return '';
                                                        }
                                                    @endphp
                                                @elseif($notification->type == 'project_assign')
                                                    @php
                                                        $link = route('projects.show', [
                                                            $notification->workspace_id,
                                                            $notification->data->id,
                                                        ]);
                                                        $text =
                                                            __('New project assign') . ' <b>' . $data->title . '</b>';
                                                        $icon = 'fa fa-suitcase';
                                                    @endphp
                                                @elseif($notification->type == 'bug_assign')
                                                    @php
                                                        if ($project) {
                                                            $link = route('projects.bug.report', [
                                                                $notification->workspace_id,
                                                                $notification->project_id,
                                                            ]);
                                                            $text =
                                                                __('New bug assign') .
                                                                ' <b>' .
                                                                $data->title .
                                                                '</b> ' .
                                                                __('in project') .
                                                                ' <b>' .
                                                                $project->name .
                                                                '</b>';
                                                            $icon = 'fa fa-bug';
                                                            if ($data->priority == 'Low') {
                                                                $icon_color = 'bg-success';
                                                            } elseif ($data->priority == 'High') {
                                                                $icon_color = 'bg-danger';
                                                            }
                                                        }
                                                    @endphp
                                                @endif
                                                <a href="{{ $link }}"
                                                    class="list-group-item list-group-item-action mb-3">
                                                    <div class="d-flex align-items-center" data-toggle="tooltip"
                                                        data-placement="right" data-title="{{ $date }}">
                                                        <div class="notification_icon_size">
                                                            <span
                                                                class="avatar bg-primary text-white rounded-circle px-2 py-1">{{ $name }}</span>
                                                        </div>
                                                        <div class="flex-fill ml-3">
                                                            <div class="h6 text-sm mb-0">
                                                                {{ $notification->user->name }}
                                                                <small
                                                                    class="float-end text-muted">{{ $date }}</small>
                                                            </div>
                                                            <p class="text-sm lh-140 mb-0">
                                                                {!! $text !!}
                                                            </p>
                                                        </div>
                                                    </div>
                                                </a>
                                            @endforeach
                                        </div>
                                        <div class="all_notification">
                                        </div>
                                    </div>
                                </div>
                            @endauth
                        @endif
                    </li>
                @endif

                <li class="dropdown dash-h-item drp-language">
                    <a class="dash-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown"
                        href="#" role="button" aria-haspopup="false" aria-expanded="false">
                        <i class="ti ti-world nocolor"></i>
                        <span
                            class="drp-text hide-mob">{{ ucfirst(\App\Models\Utility::getlang_fullname($currantLang)) }}</span>
                        <i class="ti ti-chevron-down drp-arrow nocolor"></i>
                    </a>
                    <div class="dropdown-menu dash-h-dropdown dropdown-menu-end">
                        @if (\Auth::guard('client')->check())
                            @foreach ($languages as $languageCode => $languageFullName)
                                <a href="{{ route('change_lang_workspace1', [$currentWorkspace->id, $languageCode]) }}"
                                    class="dropdown-item {{ $currantLang == $languageCode ? 'text-danger' : '' }}">
                                    <span>{{ $languageFullName }}</span>
                                </a>
                            @endforeach
                        @endif
                        @if (\Auth::user()->type == 'admin')
                            @foreach ($languages as $languageCode => $languageFullName)
                                <a href="{{ route('change_lang_admin', $languageCode) }}"
                                    class="dropdown-item {{ $currantLang == $languageCode ? 'text-danger' : '' }}">
                                    <span>{{ $languageFullName }}</span>
                                </a>
                            @endforeach
                            <div class="dropdown-divider m-0"></div>
                            <a href="#" class="dropdown-item text-primary" data-ajax-popup="true"
                                data-size="md" data-title="{{ __('Create Language') }}" data-toggle="tooltip"
                                title="{{ __('Create Language') }}" data-url="{{ route('create_lang_workspace') }}">
                                <span class="dash-mtext">{{ __('Create Language') }}</span>
                            </a>
                            <div class="dropdown-divider m-0"></div>
                            <a href="{{ route('lang_workspace') }}" class="dropdown-item text-primary">
                                <span class="dash-mtext">{{ __('Manage Language') }}</span>
                            </a>
                        @elseif(isset($currentWorkspace) && $currentWorkspace && \Auth::guard('web')->check())
                            @foreach ($languages as $languageCode => $languageFullName)
                                <a href="{{ route('change_lang_workspace', [$currentWorkspace->id, $languageCode]) }}"
                                    class="dropdown-item {{ $currantLang == $languageCode ? 'text-danger' : '' }}">
                                    <span>{{ $languageFullName }}</span>
                                </a>
                            @endforeach
                        @endif
                        </a>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</header>
@if (\Auth::user()->type != 'admin' && $login_status == false)
    <script>
        document.getElementById('logout-form1').submit();
    </script>
@endif
