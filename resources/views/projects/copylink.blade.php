@extends('layouts.invoicepayheader')

@php
    $result = json_decode($project->copylinksetting);
    $logo_path = \App\Models\Utility::get_file('/');
@endphp

@section('title')
    {{ __('Copy Link') }}
@endsection

@section('page-title')
    {{ __('Projects Details') }}
@endsection

@section('action-button')
    <a href="#" class="btn-primary">
        <select name="language" id="language" class=" btn-primary btn "
            onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
            @foreach (App\Models\Utility::languages() as $langCode => $language)
                <option class="" @if ($lang == $langCode) selected @endif
                    value="{{ route('projects.link', [$currentWorkspace->slug, \Illuminate\Support\Facades\Crypt::encrypt($project->id), $langCode]) }}">
                    {{ Str::upper($language) }}
                </option>
            @endforeach
        </select>
    </a>
@endsection

<style>
    .application .container-application {
        display: flow-root !important;
    }

    #comments-data form,
    #sub-task-data form,
    #sub-task-data>div>a,
    #file-data form,
    .row_line_style {
        display: none !important;
    }
</style>

@php
    $logo = \App\Models\Utility::get_file('tasks/');
    $logo_path = \App\Models\Utility::get_file('/');
@endphp

@php
    if (Auth::user() != null) {
        $objUser = Auth::user();
    } else {
        $objUser = \App\Models\User::where('id', $project->created_by)->first();
    }
    $permissions = $objUser->getPermission($project->id);
    $client_keyword = $objUser->getGuard() == 'client' ? 'client.' : '';
    $logo = \App\Models\Utility::get_file('users-avatar/');
    $logo_project_files = \App\Models\Utility::get_file('project_files/');
@endphp

@section('content')
    <div class="row" data-spy="scroll" data-target="#useradd-sidenav">
        <div class="col-xl-3">
            <div class="card sticky-top" style="top:30px">
                <div class="list-group list-group-flush" id="useradd-sidenav">
                    @if (isset($result->basic_details) && $result->basic_details == 'on')
                        <a href="#tabs-1" class="list-group-item list-group-item-action border-0">{{ __('Basic details') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                    @endif
                    @if (isset($result->progress) && $result->progress == 'on')
                        <a href="#tabs-11" class="list-group-item list-group-item-action border-0">{{ __('Progress') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                    @endif
                    @if (isset($result->member) && $result->member == 'on')
                        <a href="#tabs-2" class="list-group-item list-group-item-action border-0 ">{{ __('Members') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                    @endif
                    @if (isset($result->progress) && $result->client == 'on')
                        <a href="#tabs-3" class="list-group-item list-group-item-action border-0">{{ __('Faculty') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                    @endif
                    @if (isset($result->milestone) && $result->milestone == 'on')
                        <a href="#tabs-4" class="list-group-item list-group-item-action border-0">{{ __('Milestones') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                    @endif
                    @if (isset($result->attachment) && $result->attachment == 'on')
                        <a href="#tabs-5" class="list-group-item list-group-item-action border-0">{{ __('Files') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                    @endif
                    @if (isset($result->task) && $result->task == 'on')
                        <a href="#tabs-6" class="list-group-item list-group-item-action border-0">{{ __('Task') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                    @endif
                    @if (isset($result->bug_report) && $result->bug_report == 'on')
                        <a href="#tabs-7" class="list-group-item list-group-item-action border-0">{{ __('Bug Report') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                    @endif
                    @if (isset($result->timesheet) && $result->timesheet == 'on')
                        <a href="#tabs-8" class="list-group-item list-group-item-action border-0">{{ __('Timesheet') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                    @endif
                    @if (isset($result->activity) && $result->activity == 'on')
                        <a href="#tabs-9" class="list-group-item list-group-item-action border-0">{{ __('Activity Log') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                    @endif
                    @if (isset($result->tracker_details) && $result->tracker_details == 'on')
                        <a href="#tabs-10"
                            class="list-group-item list-group-item-action border-0">{{ __('Tracker details') }}
                            <div class="float-end"><i class="ti ti-chevron-right"></i></div>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-9">
            @if (isset($result->basic_details) && $result->basic_details == 'on')
                <div id="tabs-1" class="">
                    <div class="card bg-primary">
                        <div class="card-body">
                            <div class="d-block d-sm-flex align-items-center justify-content-between">
                                <h4 class="text-white"> {{ $project->name }}</h4>
                                <div class="d-flex  align-items-center row1">
                                    <div class="px-3">
                                        <span class="text-white text-sm">{{ __('Start Date') }}:</span>
                                        <h5 class="text-white text-nowrap">
                                            {{ App\Models\Utility::dateFormat($project->start_date) }}</h5>
                                    </div>
                                    <div class="px-3">
                                        <span class="text-white text-sm">{{ __('Due Date') }}:</span>
                                        <h5 class="text-white">{{ App\Models\Utility::dateFormat($project->end_date) }}
                                        </h5>
                                    </div>
                                    <div class="px-3">
                                        <span class="text-white text-sm">{{ __('Total Members') }}:</span>
                                        <h5 class="text-white text-nowrap">
                                            {{ (int) $project->users->count() + (int) $project->clients->count() }}</h5>
                                    </div>
                                    <div class="px-3 py-3">
                                        @if ($project->status == 'Finished')
                                            <div class="badge  bg-success p-2 px-3 rounded"> {{ __('Finished') }}
                                            </div>
                                        @elseif($project->status == 'Ongoing')
                                            <div class="badge  bg-secondary p-2 px-3 rounded">{{ __('Ongoing') }}</div>
                                        @else
                                            <div class="badge bg-warning p-2 px-3 rounded">{{ __('OnHold') }}</div>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="theme-avtar bg-primary">
                                            <i class="fas fas fa-calendar-day"></i>
                                        </div>
                                        <div class="col text-end">
                                            <h6 class="text-muted mb-1">{{ __('Days left') }}</h6>
                                            <span class="h6 font-weight-bold mb-0 ">{{ $daysleft }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="theme-avtar bg-danger">
                                            <i class="fas fa-check-double"></i>
                                        </div>
                                        <div class="col text-end">
                                            <h6 class="text-muted mb-1">{{ __('Total Task') }}</h6>
                                            <span class="h6 font-weight-bold mb-0 ">{{ $project->countTask() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="theme-avtar bg-success">
                                            <i class="fas fa-comments"></i>
                                        </div>
                                        <div class="col text-end">
                                            <h6 class="text-muted mb-1">{{ __('Comment') }}</h6>
                                            <span
                                                class="h6 font-weight-bold mb-0 ">{{ $project->countTaskComments() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @if (isset($result->progress) && $result->progress == 'on')
                <div id="tabs-11" class="">
                    <div class="row">
                        <div class="card">
                            <div class="card-header" style="padding: 25px 35px !important;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="row">
                                        <h5 class="mb-0">{{ __('Progress') }}<span class="text-end"> (Last Week Tasks)
                                            </span></h5>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-start">
                                </div>
                            </div>
                            <div id="task-chart"></div>
                        </div>
                    </div>
                </div>
            @endif
            <div class="row">
                @if (isset($result->member) && $result->member == 'on')
                    <div id="tabs-2" class="col-md-6">
                        <div class="card ">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-0">{{ __('Team Members') }} ({{ count($project->users) }})</h5>
                                    </div>
                                    {{-- <div class="float-end">
                                        <p class="text-muted d-sm-flex align-items-center mb-0">
                                            @if ($currentWorkspace->permission == 'Owner')
                                                <a href="#" class="btn btn-sm btn-primary " data-ajax-popup="true"
                                                    data-title="{{ __('Invite') }}" data-toggle="popover"
                                                    title="{{ __('Invite') }}"
                                                    data-url="{{ route('projects.invite.popup', [$currentWorkspace->slug, $project->id]) }}">
                                                    <i class="ti ti-brand-telegram"></i>
                                                </a>
                                            @endif
                                        </p>
                                    </div> --}}
                                </div>
                            </div>
                            <div class="card-body">
                                @foreach ($project->users as $user)
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item px-0">
                                            <div class="row align-items-center justify-content-between">
                                                <div class="col-sm-auto mb-3 mb-sm-0">
                                                    <div class="d-flex align-items-center px-2">
                                                        <a href="#" class=" text-start">
                                                            <img class="fix_img"
                                                                @if ($user->avatar) src="{{ asset($logo . $user->avatar) }}"
                                                                @else avatar="{{ $user->name }}" @endif>
                                                        </a>
                                                        <div class="px-2">
                                                            <h5 class="m-0">{{ $user->name }}</h5>
                                                            <small class="text-muted">{{ $user->email }}
                                                                <span class="text-primary "> -
                                                                    {{ (int) count($project->user_done_tasks($user->id)) }}/{{ (int) count($project->user_tasks($user->id)) }}
                                                                </span>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- <div class="col-sm-auto text-sm-end d-flex align-items-center">
                                                    @auth('web')
                                                        @if ($currentWorkspace->permission == 'Owner' && $user->id != Auth::id())
                                                            <a href="#"
                                                                class="action-btn btn-primary mx-1  btn btn-sm d-inline-flex align-items-center"
                                                                data-ajax-popup="true" data-size="lg" data-toggle="popover"
                                                                title="{{ __('Permission') }}"
                                                                data-title="{{ __('Edit Permission') }}"
                                                                data-url="{{ route('projects.user.permission', [$currentWorkspace->slug, $project->id, $user->id]) }}"><i
                                                                    class="ti ti-lock"></i></a>

                                                            <a href="#"
                                                                class="action-btn btn-danger btn btn-sm d-inline-flex align-items-center bs-pass-para"
                                                                data-confirm="{{ __('Are You Sure?') }}" data-toggle="popover"
                                                                title="{{ __('Delete') }}"
                                                                data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                data-confirm-yes="delete-user-{{ $user->id }}"><i
                                                                    class="ti ti-trash ml-1"></i></a>
                                                            <form id="delete-user-{{ $user->id }}"
                                                                action="{{ route('projects.user.delete', [$currentWorkspace->slug, $project->id, $user->id]) }}"
                                                                method="POST" style="display: none;">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                        @endif
                                                    @endauth
                                                </div> --}}
                                            </div>
                                        </li>
                                    </ul>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
                @if (isset($result->client) && $result->client == 'on')
                    <div id="tabs-3" class="col-md-6">
                        <div class="card" style="min-height:350;">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-0">{{ __('Faculty') }} ({{ count($project->clients) }})</h5>
                                    </div>
                                    {{-- <div class="float-end">
                                        <p class="text-muted d-none d-sm-flex align-items-center mb-0">
                                            @if ($currentWorkspace->permission == 'Owner')
                                                <a href="#" class="btn btn-sm btn-primary" data-ajax-popup="true"
                                                    data-title="{{ __('Share to Client') }}" data-toggle="popover"
                                                    title="{{ __('Share to Client') }}"
                                                    data-url="{{ route('projects.share.popup', [$currentWorkspace->slug, $project->id]) }}"><i
                                                        class="ti ti-share"></i></a>
                                            @endif
                                        </p>
                                    </div> --}}
                                </div>
                            </div>
                            <div class="card-body">
                                @foreach ($project->clients as $client)
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item px-0">
                                            <div class="row align-items-center justify-content-between">
                                                <div class="col-sm-auto mb-3 mb-sm-0">
                                                    <div class="d-flex align-items-center px-2">
                                                        <a href="#" class=" text-start">
                                                            <img class="fix_img"
                                                                @if ($client->avatar) src="{{ asset($logo . $client->avatar) }}"
                                                                @else avatar="{{ $client->name }}" @endif>
                                                        </a>
                                                        <div class="px-2">
                                                            <h5 class="m-0">{{ $client->name }}</h5>
                                                            <small class="text-muted">{{ $client->email }}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- <div class="col-sm-auto text-sm-end d-flex align-items-center">
                                                    @auth('web')
                                                        @if ($currentWorkspace->permission == 'Owner')
                                                            <a href="#"
                                                                class="action-btn btn-primary mx-1  btn btn-sm d-inline-flex align-items-center"
                                                                data-toggle="popover" title="{{ __('Permission') }}"
                                                                data-ajax-popup="true" data-size="lg"
                                                                data-title="{{ __('Edit Permission') }}"
                                                                data-url="{{ route('projects.client.permission', [$currentWorkspace->slug, $project->id, $client->id]) }}"><i
                                                                    class="ti ti-lock"></i></a>
                                                            <a href="#"
                                                                class="action-btn btn-danger mx-1  btn btn-sm d-inline-flex align-items-center bs-pass-para"
                                                                data-confirm="{{ __('Are You Sure?') }}" data-toggle="popover"
                                                                title="{{ __('Delete') }}"
                                                                data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                data-confirm-yes="delete-client-{{ $client->id }}"><i
                                                                    class="ti ti-trash"></i></a>
                                                            <form id="delete-client-{{ $client->id }}"
                                                                action="{{ route('projects.client.delete', [$currentWorkspace->slug, $project->id, $client->id]) }}"
                                                                method="POST" style="display: none;">
                                                                @csrf
                                                                @method('DELETE')
                                                            </form>
                                                        @endif
                                                    @endauth
                                                </div> --}}
                                            </div>
                                        </li>
                                    </ul>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            @if (isset($result->milestone) && $result->milestone == 'on')
                <div id="tabs-4" class="">
                    @if ((isset($permissions) && in_array('show milestone', $permissions)) || $currentWorkspace->permission == 'Owner')
                        <div class="card" style="overflow-x: none;">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-0">{{ __('Milestones') }} ({{ count($project->milestones) }})
                                        </h5>
                                    </div>
                                    {{-- <div class="float-end">
                                        @if ((isset($permissions) && in_array('create milestone', $permissions)) || $currentWorkspace->permission == 'Owner')
                                            <a href="#" class="btn btn-sm btn-primary" data-ajax-popup="true"
                                                data-title="{{ __('Create Milestone') }}"
                                                data-url="{{ route($client_keyword . 'projects.milestone', [$currentWorkspace->slug, $project->id]) }}"
                                                data-toggle="popover" title="{{ __('Create') }}"><i
                                                    class="ti ti-plus"></i></a>
                                        @endif
                                    </div> --}}
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table id="" class="table table-bordered px-2">
                                        <thead>
                                            <tr>
                                                <th>{{ __('Name') }}</th>
                                                <th>{{ __('Status') }}</th>
                                                <th>{{ __('Start Date') }}</th>
                                                <th>{{ __('End Date') }}</th>
                                                <th>{{ __('Cost') }}</th>
                                                <th>{{ __('Progress') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($project->milestones as $key => $milestone)
                                                <tr>
                                                    <td>
                                                        <a href="#" class="d-block font-weight-500 mb-0"
                                                            data-ajax-popup="true"
                                                            data-title="{{ __('Milestone Details') }}"
                                                            data-url="{{ route($client_keyword . 'projects.milestone.show', [$currentWorkspace->slug, $milestone->id]) }}">
                                                            <h5 class="m-0"> {{ $milestone->title }} </h5>
                                                        </a>
                                                    </td>
                                                    <td>
                                                        @if ($milestone->status == 'complete')
                                                            <label
                                                                class="badge bg-success p-2 px-3 rounded">{{ __('Complete') }}</label>
                                                        @else
                                                            <label
                                                                class="badge bg-warning p-2 px-3 rounded">{{ __('Incomplete') }}</label>
                                                        @endif
                                                    </td>
                                                    <td>{{ $milestone->start_date }}</td>
                                                    <td>{{ $milestone->end_date }}</td>
                                                    <td>
                                                        {{ !empty($currentWorkspace->currency) ? $currentWorkspace->currency : '$' }}{{ $milestone->cost }}
                                                    </td>
                                                    <td>
                                                        <div class="progress_wrapper">
                                                            <div class="progress">
                                                                <div class="progress-bar" role="progressbar"
                                                                    style="width: {{ $milestone->progress }}%;"
                                                                    aria-valuenow="55" aria-valuemin="0"
                                                                    aria-valuemax="100">
                                                                </div>
                                                            </div>
                                                            <div class="progress_labels">
                                                                <div class="total_progress">
                                                                    <strong> {{ $milestone->progress }}%</strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    {{-- <td class="text-right">
                                                        <div class="col-auto">
                                                            @if ($currentWorkspace->permission == 'Owner')
                                                                <a href="#"
                                                                    class="action-btn btn-info mx-1  btn btn-sm d-inline-flex align-items-center"
                                                                    data-ajax-popup="true" data-size="lg"
                                                                    data-toggle="popover" title="{{ __('Edit') }}"
                                                                    data-title="{{ __('Edit Milestone') }}"
                                                                    data-url="{{ route('projects.milestone.edit', [$currentWorkspace->slug, $milestone->id]) }}"><i
                                                                        class="ti ti-edit"></i></a>
                                                                <a href="#"
                                                                    class="action-btn btn-danger mx-1  btn btn-sm d-inline-flex align-items-center bs-pass-para"
                                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                                    data-toggle="popover" title="{{ __('Delete') }}"
                                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                    data-confirm-yes="delete-form1-{{ $milestone->id }}"><i
                                                                        class="ti ti-trash"></i></a>
                                                                <form id="delete-form1-{{ $milestone->id }}"
                                                                    action="{{ route('projects.milestone.destroy', [$currentWorkspace->slug, $milestone->id]) }}"
                                                                    method="POST" style="display: none;">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                </form>
                                                            @elseif(isset($permissions))
                                                                @if (in_array('edit milestone', $permissions))
                                                                    <a href="#"
                                                                        class="action-btn btn-info mx-1  btn btn-sm d-inline-flex align-items-center bs-pass-para"
                                                                        data-ajax-popup="true" data-size="lg"
                                                                        data-title="{{ __('Edit Milestone') }}"
                                                                        data-toggle="popover"
                                                                        title="{{ __('Edit') }}"
                                                                        data-url="{{ route($client_keyword . 'projects.milestone.edit', [$currentWorkspace->slug, $milestone->id]) }}"><i
                                                                            class="ti ti-edit"></i></a>
                                                                @endif
                                                                @if (in_array('delete milestone', $permissions))
                                                                    <a href="#"
                                                                        class="action-btn btn-danger mx-1  btn btn-sm d-inline-flex align-items-center bs-pass-para"
                                                                        data-confirm="{{ __('Are You Sure?') }}"
                                                                        data-toggle="popover"
                                                                        title="{{ __('Delete') }}"
                                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                        data-confirm-yes="delete-form1-{{ $milestone->id }}"><i
                                                                            class="ti ti-trash"></i></a>
                                                                    <form id="delete-form1-{{ $milestone->id }}"
                                                                        action="{{ route($client_keyword . 'projects.milestone.destroy', [$currentWorkspace->slug, $milestone->id]) }}"
                                                                        method="POST" style="display: none;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                    </form>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </td> --}}
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    @endif
                </div>
            @endif
            @if (isset($result->attachment) && $result->attachment == 'on')
                <div id="tabs-5" class="">
                    @if (
                        (isset($permissions) && in_array('show uploading', $permissions)) ||
                            $currentWorkspace->permission == 'Owner' ||
                            $currentWorkspace->permission == 'Member')
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h5 class="mb-0"> {{ __('Files') }}</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <div class="author-box-name form-control-label mb-4">
                                </div>
                                <div class="col-md-12 dropzone browse-file" id="dropzonewidget">
                                    <div class="dz-message" data-dz-message>
                                        <span>
                                            {{ __('No files available') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif
            @if (isset($result->task) && $result->task == 'on')
                <div id="tabs-6" class="">
                    <div class="card" style="background-color:transparent !important">
                        <div class="card-header"
                            style="padding: 25px 35px !important; background-color:#ffffff !important">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="row">
                                    <h5 class="mb-0">{{ __('Task') }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <section class="section py-3">
                                @if ($project && $currentWorkspace)
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div style="height:70vh" class="row kanban-wrapper horizontal-scroll-cards"
                                                data-toggle="dragula"
                                                data-containers='{{ json_encode($statusClass_task) }}'>
                                                @foreach ($stages_task as $stage)
                                                    <div class="col" id="backlog">
                                                        <div class="card card-list">
                                                            <div class="card-header">
                                                                <div class="float-end">
                                                                    <button
                                                                        class="btn-submit btn btn-md btn-primary btn-icon px-1  py-0">
                                                                        <span
                                                                            class="badge badge-secondary rounded-pill count">{{ $stage->tasks->count() }}</span>
                                                                    </button>
                                                                </div>
                                                                <h4 class="mb-0">{{ $stage->name }}</h4>
                                                                {{-- <div class="col text-right">
                                                                    <span class="badge badge-secondary rounded-pill count">{{ $stage->tasks->count() }}</span>
                                                                </div>  --}}
                                                            </div>
                                                            <div id="{{ 'task-list-' . str_replace(' ', '_', $stage->id) }}"
                                                                data-status="{{ $stage->id }}"
                                                                class="card-body kanban-box">
                                                                @foreach ($stage->tasks as $task)
                                                                    <div class="card" id="{{ $task->id }}">
                                                                        <!--  <img class="img-fluid card-img-top" src="" alt=""> -->
                                                                        <div
                                                                            class="position-absolute top-0 start-0 pt-3 ps-3">
                                                                            @if ($task->priority == 'Low')
                                                                                <div
                                                                                    class="badge bg-success p-2 px-3 rounded">
                                                                                    {{ $task->priority }}</div>
                                                                            @elseif($task->priority == 'Medium')
                                                                                <div
                                                                                    class="badge bg-warning p-2 px-3 rounded">
                                                                                    {{ $task->priority }}</div>
                                                                            @elseif($task->priority == 'High')
                                                                                <div
                                                                                    class="badge bg-danger p-2 px-3 rounded">
                                                                                    {{ $task->priority }}</div>
                                                                            @endif
                                                                        </div>
                                                                        <div
                                                                            class="card-header border-0 pb-0 position-relative">

                                                                            <div style="padding: 30px 2px;">
                                                                                <h5>{{ $task->title }}</h5>
                                                                                {{-- <a href="#" data-size="lg"
                                                                                    data-url="{{ route($client_keyword . 'tasks.show', [$currentWorkspace->slug, $task->project_id, $task->id]) }}"
                                                                                    data-ajax-popup="true"
                                                                                    data-title="{{ __('Task Detail') }}"
                                                                                    class="h6 task-title">
                                                                                    <h5>{{ $task->title }}</h5>
                                                                                </a> --}}
                                                                            </div>
                                                                            {{-- <div class="card-header-right">
                                                                                <div class="btn-group card-option">
                                                                                    @if ($currentWorkspace->permission == 'Owner' || isset($permissions))
                                                                                        <button type="button"
                                                                                            class="btn dropdown-toggle"
                                                                                            data-bs-toggle="dropdown"
                                                                                            aria-haspopup="true"
                                                                                            aria-expanded="false">
                                                                                            <i
                                                                                                class="feather icon-more-vertical"></i>
                                                                                        </button>
                                                                                        <div
                                                                                            class="dropdown-menu dropdown-menu-end">
                                                                                            <a href="#"
                                                                                                class="dropdown-item"
                                                                                                data-ajax-popup="true"
                                                                                                data-size="lg"
                                                                                                data-title="{{ __('View Task') }}"
                                                                                                data-url="{{ route($client_keyword . 'tasks.show', [$currentWorkspace->slug, $task->project_id, $task->id]) }}">
                                                                                                <i class="ti ti-eye"></i>
                                                                                                {{ __('View') }}</a>
                                                                                            @if ($currentWorkspace->permission == 'Owner')
                                                                                                <a href="#"
                                                                                                    class="dropdown-item"
                                                                                                    data-ajax-popup="true"
                                                                                                    data-size="lg"
                                                                                                    data-title="{{ __('Edit Task') }}"
                                                                                                    data-url="{{ route('tasks.edit', [$currentWorkspace->slug, $task->project_id, $task->id]) }}">
                                                                                                    <i
                                                                                                        class="ti ti-edit"></i>
                                                                                                    {{ __('Edit') }}</a>
                                                                                                <a href="#"
                                                                                                    class="dropdown-item bs-pass-para"
                                                                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                                                    data-confirm-yes="delete-form-{{ $task->id }}">
                                                                                                    <i
                                                                                                        class="ti ti-trash"></i>
                                                                                                    {{ __('Delete') }}
                                                                                                </a>
                                                                                                <form
                                                                                                    id="delete-form-{{ $task->id }}"
                                                                                                    action="{{ route('tasks.destroy', [$currentWorkspace->slug, $task->project_id, $task->id]) }}"
                                                                                                    method="POST"
                                                                                                    style="display: none;">
                                                                                                    @csrf
                                                                                                    @method('DELETE')
                                                                                                </form>
                                                                                            @elseif(isset($permissions))
                                                                                                @if (in_array('edit task', $permissions))
                                                                                                    <a href="#"
                                                                                                        class="dropdown-item"
                                                                                                        data-ajax-popup="true"
                                                                                                        data-size="lg"
                                                                                                        data-title="{{ __('Edit Task') }}"
                                                                                                        data-url="{{ route($client_keyword . 'tasks.edit', [$currentWorkspace->slug, $task->project_id, $task->id]) }}">
                                                                                                        <i
                                                                                                            class="ti ti-edit"></i>
                                                                                                        {{ __('Edit') }}
                                                                                                    </a>
                                                                                                @endif
                                                                                                @if (in_array('delete task', $permissions))
                                                                                                    <a href="#"
                                                                                                        class="dropdown-item  delete-comment bs-pass-para"
                                                                                                        data-confirm="{{ __('Are You Sure?') }}"
                                                                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                                                        data-confirm-yes="delete-form-{{ $task->id }}">
                                                                                                        <i
                                                                                                            class="ti ti-trash"></i>
                                                                                                        {{ __('Delete') }}
                                                                                                    </a>
                                                                                                    <form
                                                                                                        id="delete-form-{{ $task->id }}"
                                                                                                        action="{{ route($client_keyword . 'tasks.destroy', [$currentWorkspace->slug, $task->project_id, $task->id]) }}"
                                                                                                        method="POST"
                                                                                                        style="display: none;">
                                                                                                        @csrf
                                                                                                        @method('DELETE')
                                                                                                    </form>
                                                                                                @endif
                                                                                            @endif

                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            </div> --}}
                                                                        </div>
                                                                        <div class="card-body pt-0">
                                                                            <div class="row">
                                                                                <div class="col">
                                                                                    <div class="action-item">
                                                                                        {{ \App\Models\Utility::dateFormat($task->start_date) }}
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col text-right">
                                                                                    <div class="action-item">
                                                                                        {{ \App\Models\Utility::dateFormat($task->due_date) }}
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div
                                                                                class="d-flex align-items-center justify-content-between">
                                                                                <ul class="list-inline mb-0">
                                                                                    <li
                                                                                        class="list-inline-item d-inline-flex align-items-center">
                                                                                        <i
                                                                                            class="f-16 text-primary ti ti-brand-telegram"></i>
                                                                                        {{ $task->taskCompleteSubTaskCount() }}/{{ $task->taskTotalSubTaskCount() }}
                                                                                    </li>
                                                                                </ul>
                                                                                <div class="user-group">
                                                                                    @if ($users = $task->users())
                                                                                        @foreach ($users as $key => $user)
                                                                                            @if ($key < 3)
                                                                                                <a href="#"
                                                                                                    class="img_group">
                                                                                                    <img alt="image"
                                                                                                        data-toggle="tooltip"
                                                                                                        data-original-title="{{ $user->name }}"
                                                                                                        @if ($user->avatar) src="{{ asset($logo . $user->avatar) }}" @else avatar="{{ $user->name }}" @endif>
                                                                                                </a>
                                                                                            @endif
                                                                                        @endforeach
                                                                                        @if (count($users) > 3)
                                                                                            <a href="#"
                                                                                                class="img_group">
                                                                                                <img alt="image"
                                                                                                    data-toggle="tooltip"
                                                                                                    data-original-title="{{ count($users) - 3 }} {{ __('more') }}"
                                                                                                    avatar="+ {{ count($users) - 3 }}">
                                                                                            </a>
                                                                                        @endif
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                                <span class="empty-container"
                                                                    data-placeholder="Empty"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <!-- [ sample-page ] end -->
                                        </div>
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
                                                                <a class="btn-return-home badge-blue"
                                                                    href="{{ route('home') }}">
                                                                    <i class="fas fa-reply"></i>
                                                                    {{ __('Return Home') }}
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </section>
                        </div>
                    </div>
                </div>
            @endif
            @if (isset($result->bug_report) && $result->bug_report == 'on')
                <div id="tabs-7" class="">
                    <div class="card" style="background-color:transparent !important">
                        <div class="card-header"
                            style="padding: 25px 35px !important; background-color:#ffffff !important">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="row">
                                    <h5 class="mb-0">{{ __('Bug Report') }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <section class="section py-5">
                                @if ($project && $currentWorkspace)
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <div style="height:70vh" class="row kanban-wrapper horizontal-scroll-cards"
                                                data-toggle="dragula"
                                                data-containers='{{ json_encode($statusClass_bug) }}'>
                                                @foreach ($stages_bug as $stage)
                                                    <div class="col" id="backlog">
                                                        <div class="card card-list">
                                                            <div class="card-header">
                                                                <div class="float-end">
                                                                    <button
                                                                        class="btn-submit btn btn-md btn-primary btn-icon px-1  py-0">
                                                                        <span
                                                                            class="badge badge-secondary rounded-pill count">{{ $stage->bugs->count() }}</span>
                                                                    </button>
                                                                </div>
                                                                <h4 class="mb-0">{{ $stage->name }}</h4>
                                                            </div>
                                                            <div id="{{ 'task-list-' . str_replace(' ', '_', $stage->id) }}"
                                                                data-status="{{ $stage->id }}"
                                                                class="card-body kanban-box">

                                                                @foreach ($stage->bugs as $bug)
                                                                    <div class="card" id="{{ $bug->id }}">
                                                                        <!--  <img class="img-fluid card-img-top" src="" alt=""> -->
                                                                        <div class="position-absolute top-0 start-0 pt-3 ps-3">
                                                                            @if ($bug->priority == 'Low')
                                                                                <div
                                                                                    class="badge bg-success p-2 px-3 rounded">
                                                                                    {{ $bug->priority }}</div>
                                                                            @elseif($bug->priority == 'Medium')
                                                                                <div
                                                                                    class="badge bg-warning p-2 px-3 rounded">
                                                                                    {{ $bug->priority }}</div>
                                                                            @elseif($bug->priority == 'High')
                                                                                <div
                                                                                    class="badge bg-danger p-2 px-3 rounded">
                                                                                    {{ $bug->priority }}
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                        <div
                                                                            class="card-header border-0 pb-0 position-relative">
                                                                            <div style="padding: 30px 2px;">
                                                                                <h5>{{ $bug->title }}</h5>
                                                                                {{-- <a href="#" data-size="lg"
                                                                                    data-url="{{ route($client_keyword . 'projects.bug.report.show', [$currentWorkspace->slug, $bug->project_id, $bug->id]) }}"
                                                                                    data-ajax-popup="true"
                                                                                    data-title="{{ __('Bug Detail') }}"
                                                                                    class="h6 task-title">
                                                                                    <h5>{{ $bug->title }}</h5>
                                                                                </a> --}}
                                                                            </div>
                                                                            {{-- <div class="card-header-right">
                                                                                <div class="btn-group card-option">
                                                                                    @if ($currentWorkspace->permission == 'Owner' || isset($permissions))
                                                                                        <button type="button"
                                                                                            class="btn dropdown-toggle"
                                                                                            data-bs-toggle="dropdown"
                                                                                            aria-haspopup="true"
                                                                                            aria-expanded="false">
                                                                                            <i class="feather icon-more-vertical"></i>
                                                                                        </button>
                                                                                        <div
                                                                                            class="dropdown-menu dropdown-menu-end">
                                                                                            <a href="#"
                                                                                                class="dropdown-item"
                                                                                                data-ajax-popup="true"
                                                                                                data-size="lg"
                                                                                                data-title="{{ __('View Bug') }}"
                                                                                                data-url="{{ route($client_keyword . 'projects.bug.report.show', [$currentWorkspace->slug, $bug->project_id, $bug->id]) }}"><i
                                                                                                    class="ti ti-eye"></i>
                                                                                                {{ __('View') }}</a>
                                                                                            @if ($currentWorkspace->permission == 'Owner')
                                                                                                <a href="#"
                                                                                                    class="dropdown-item"
                                                                                                    data-ajax-popup="true"
                                                                                                    data-size="lg"
                                                                                                    data-title="{{ __('Edit Bug') }}"
                                                                                                    data-url="{{ route('projects.bug.report.edit', [$currentWorkspace->slug, $bug->project_id, $bug->id]) }}"><i
                                                                                                        class="ti ti-edit"></i>
                                                                                                    {{ __('Edit') }}</a>
                                                                                                <a href="#"
                                                                                                    class="dropdown-item bs-pass-para"
                                                                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                                                    data-confirm-yes="delete-form-{{ $bug->id }}"><i
                                                                                                        class="ti ti-trash"></i>
                                                                                                    {{ __('Delete') }}
                                                                                                </a>
                                                                                                <form
                                                                                                    id="delete-form-{{ $bug->id }}"
                                                                                                    action="{{ route('projects.bug.report.destroy', [$currentWorkspace->slug, $bug->project_id, $bug->id]) }}"
                                                                                                    method="POST"
                                                                                                    style="display: none;">
                                                                                                    @csrf
                                                                                                    @method('DELETE')
                                                                                                </form>
                                                                                            @elseif(isset($permissions))
                                                                                                @if (in_array('edit bug report', $permissions))
                                                                                                    <a href="#"
                                                                                                        class="dropdown-item"
                                                                                                        data-ajax-popup="true"
                                                                                                        data-size="lg"
                                                                                                        data-title="{{ __('Edit Bug') }}"
                                                                                                        data-url="{{ route($client_keyword . 'projects.bug.report.edit', [$currentWorkspace->slug, $bug->project_id, $bug->id]) }}"><i
                                                                                                            class="ti ti-edit"></i>
                                                                                                        {{ __('Edit') }}
                                                                                                    </a>
                                                                                                @endif
                                                                                                @if (in_array('delete bug report', $permissions))
                                                                                                    <a href="#"
                                                                                                        class="dropdown-item bs-pass-para"
                                                                                                        data-confirm="{{ __('Are You Sure?') }}"
                                                                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                                                                        data-confirm-yes="delete-form-{{ $bug->id }}"><i
                                                                                                            class="ti ti-trash"></i>
                                                                                                        {{ __('Delete') }}
                                                                                                    </a>
                                                                                                    <form
                                                                                                        id="delete-form-{{ $bug->id }}"
                                                                                                        action="{{ route($client_keyword . 'projects.bug.report.destroy', [$currentWorkspace->slug, $bug->project_id, $bug->id]) }}"
                                                                                                        method="POST"
                                                                                                        style="display: none;">
                                                                                                        @csrf
                                                                                                        @method('DELETE')
                                                                                                    </form>
                                                                                                @endif
                                                                                            @endif

                                                                                        </div>
                                                                                    @endif
                                                                                </div>
                                                                            </div> --}}
                                                                        </div>
                                                                        <div class="card-body pt-0">
                                                                            <div
                                                                                class="d-flex align-items-center justify-content-between">
                                                                                <ul class="list-inline mb-0">
                                                                                    <li
                                                                                        class="list-inline-item d-inline-flex align-items-center">
                                                                                        <i
                                                                                            class="f-16 text-primary ti ti-message-2"></i>
                                                                                        {{ $bug->comments->count() }}
                                                                                        {{ __('Comments') }}
                                                                                    </li>
                                                                                </ul>
                                                                                <div class="user-group">
                                                                                    @if ($currentWorkspace->permission == 'Owner' || isset($permissions))
                                                                                        <a href="#"
                                                                                            class="img_group">
                                                                                            <img alt="image"
                                                                                                data-toggle="tooltip"
                                                                                                data-original-title="{{ $bug->user ? $bug->user->name : '' }}"
                                                                                                @if ($bug->user ? $bug->user->avatar : '') src="{{ $logo . $bug->user->avatar }}"
                                                                                                @else avatar="{{ $bug->user ? $bug->user->name : '' }}" @endif>
                                                                                        </a>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                                <span class="empty-container"
                                                                    data-placeholder="Empty"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <!-- [ sample-page ] end -->
                                        </div>
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
                                                                <a class="btn-return-home badge-blue"
                                                                    href="{{ route('home') }}">
                                                                    <i class="fas fa-reply"></i>
                                                                    {{ __('Return Home') }}
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </section>
                        </div>
                    </div>
                </div>
            @endif
            @if (isset($result->timesheet) && $result->timesheet == 'on')
                <div id="tabs-8" class="">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card notfound-timesheet1">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-0"> {{ __('Timesheet') }}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="py-5" id="timesheet-table-view" style="width:100%"></div>
                                </div>
                            </div>
                            <div class="card notfound-timesheet text-center">
                                <div class="card-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-0"> {{ __('Timesheet') }}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-3">
                                    <div class="page-error">
                                        <div class="page-inner">
                                            <div class="page-description">
                                                {{ __("We couldn't find any data") }}
                                            </div>
                                            <div class="page-search">
                                                <p class="text-muted mt-3">
                                                    {{ __("Sorry we can't find any timesheet records on this week.") }}
                                                    <br>
                                                    @if ($project->id != '-1' && $objUser->getGuard() != 'client')
                                                        {{ __('To add record go to ') }}
                                                        <b>{{ __('Add Task on Timesheet.') }}</b>
                                                    @else
                                                        {{ __('To add timesheet record go to ') }}
                                                        <a class="btn-home badge-blue"
                                                            href="{{ route($client_keyword . 'projects.index', $currentWorkspace->slug) }}">
                                                            <i class="fas fa-reply"></i> {{ __('Projects') }}
                                                        </a>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @if (isset($result->activity) && $result->activity == 'on')
                <div id="tabs-9" class="">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0">{{ __('Activity') }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div class="timeline timeline-one-side top-10-scroll" data-timeline-content="axis"
                                data-timeline-axis-style="dashed">
                                @if ((isset($permissions) && in_array('show activity', $permissions)) || $currentWorkspace->permission == 'Owner')
                                    @foreach ($project->activities as $activity)
                                        <div class="timeline-block px-2 pt-3">
                                            @if ($activity->log_type == 'Upload File')
                                                <span
                                                    class="timeline-step timeline-step-sm border border-primary text-white">
                                                    <i class="fas fa-file"></i></span>
                                            @elseif($activity->log_type == 'Create Milestone')
                                                <span class="timeline-step timeline-step-sm border border-info text-white">
                                                    <i class="fas fa-cubes"></i></span>
                                            @elseif($activity->log_type == 'Create Task')
                                                <span
                                                    class="timeline-step timeline-step-sm border border-success text-white">
                                                    <i class="fas fa-tasks"></i></span>
                                            @elseif($activity->log_type == 'Create Bug')
                                                <span
                                                    class="timeline-step timeline-step-sm border border-warning text-white">
                                                    <i class="fas fa-bug"></i></span>
                                            @elseif($activity->log_type == 'Move' || $activity->log_type == 'Move Bug')
                                                <span
                                                    class="timeline-step timeline-step-sm border round border-danger text-white">
                                                    <i class="fas fa-align-justify"></i></span>
                                            @elseif($activity->log_type == 'Create Invoice')
                                                <span
                                                    class="timeline-step timeline-step-sm border border-bg-dark text-white">
                                                    <i class="fas fa-file-invoice"></i></span>
                                            @elseif($activity->log_type == 'Invite User')
                                                <span
                                                    class="timeline-step timeline-step-sm border border-success text-white">
                                                    <i class="fas fa-plus"></i></span>
                                            @elseif($activity->log_type == 'Share with Client')
                                                <span class="timeline-step timeline-step-sm border border-info text-white">
                                                    <i class="fas fa-share"></i></span>
                                            @elseif($activity->log_type == 'Create Timesheet')
                                                <span
                                                    class="timeline-step timeline-step-sm border border-success text-white">
                                                    <i class="fas fa-clock-o"></i></span>
                                            @endif

                                            <div class="last_notification_text">
                                                {{-- <p class="m-0"> <span>{{ $activity->log_type }} </span> </p> <br>  --}}
                                                <p class="m-0"> <span>{{ $activity->logType($activity->log_type) }}
                                                    </span> </p> <br>
                                                <p> {!! $activity->getRemark() !!} </p>
                                                <div class="notification_time_main">
                                                    <p>{{ $activity->created_at->diffForHumans() }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @if (isset($result->tracker_details) && $result->tracker_details == 'on')
                <div id="tabs-10" class="">
                    {{-- <div class="row"> --}}
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-0">{{ __('Tracker details') }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-border-style ">
                            <div class="table-responsive">
                                <table class=" table" id="selection-datatable">
                                    <thead>
                                        <tr>
                                            <th> {{ __('Description') }}</th>
                                            <th> {{ __('Project') }}</th>
                                            <th> {{ __('Task') }}</th>
                                            <th> {{ __('Workspace') }}</th>
                                            <th> {{ __('Start Time') }}</th>
                                            <th> {{ __('End Time') }}</th>
                                            <th>{{ __('Total Time') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($treckers as $trecker)
                                            @php
                                                $total_name = App\Models\Utility::second_to_time($trecker->total_time);
                                            @endphp
                                            <tr>
                                                <td>{{ __($trecker->name) }}</td>
                                                <td>{{ __($trecker->project_name) }}</td>
                                                <td>{{ __($trecker->project_task) }}</td>
                                                <td>{{ __($trecker->project_workspace) }}</td>
                                                <td>{{ __(date('H:i:s', strtotime($trecker->start_time))) }}</td>
                                                <td>{{ __(date('H:i:s', strtotime($trecker->end_time))) }}</td>
                                                <td>{{ __($total_name) }}</td>
                                                <td>
                                                    <img alt="Image placeholder"
                                                        src="{{ asset('assets/images/gallery.png') }}"
                                                        class="avatar view-images rounded-circle avatar-sm"
                                                        data-toggle="tooltip" title="{{ __('View Screenshot images') }}"
                                                        style="height: 25px;width:24px;margin-right:10px;cursor: pointer;"
                                                        data-id="{{ $trecker->id }}"
                                                        id="track-images-{{ $trecker->id }}">
                                                    {{-- <a href="#"
                                                            class="action-btn btn-danger btn btn-sm d-inline-flex align-items-center bs-pass-para"
                                                            data-toggle="tooltip" title="{{ __('Delete') }}"
                                                            data-confirm="{{ __('Are You Sure?') }}"
                                                            data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                            data-confirm-yes="delete-form-{{ $trecker->id }}">
                                                            <i class="ti ti-trash"></i>
                                                        </a>
                                                        {!! Form::open([
                                                            'method' => 'DELETE',
                                                            'route' => ['tracker.destroy', $trecker->id],
                                                            'id' => 'delete-form-' . $trecker->id,
                                                        ]) !!}
                                                        {!! Form::close() !!} --}}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    {{-- </div> --}}
                </div>
            @endif
        </div>
    </div>
@endsection


@push('css-page')
    <link rel="stylesheet" href="{{ asset('assets/custom/css/dropzone.min.css') }}">
@endpush

@push('script')
    <script src="{{ asset('public/assets/custom/js/letter.avatar.js') }}"></script>

    <script>
        LetterAvatar.transform();
    </script>

    <script>
        $(document).ready(function() {
            var sectionIds = $('a.list-group-item-action');
            $(document).scroll(function() {
                sectionIds.each(function() {
                    var container = $(this).attr('href');
                    var containerOffset = $(container).offset().top - 200;
                    var containerHeight = $(container).outerHeight();
                    var containerBottom = containerOffset + containerHeight;
                    var scrollPosition = $(document).scrollTop();
                    if (scrollPosition < containerBottom && scrollPosition >= containerOffset) {
                        $(this).addClass('active');
                    } else {
                        $(this).removeClass('active');
                    }
                });
            });
        });
    </script>

    @if (isset($result->progress) && $result->progress == 'on')
        <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
        <script>
            (function() {
                var options = {
                    chart: {
                        type: 'area',
                        height: 60,
                        sparkline: {
                            enabled: true,
                        },
                    },
                    colors: {!! json_encode($chartData['color']) !!},
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 2,
                    },
                    series: [
                        @foreach ($chartData['stages'] as $id => $name)
                            {
                                name: "{{ __($name) }}",
                                data: {!! json_encode($chartData[$id]) !!},
                            },
                        @endforeach
                    ],
                    xaxis: {
                        type: "category",
                        categories: {!! json_encode($chartData['label']) !!},
                        title: {
                            text: '{{ __('Days') }}'
                        },
                        tooltip: {
                            enabled: false,
                        }
                    },
                    yaxis: {
                        show: true,
                        position: "left",
                        title: {
                            text: '{{ __('Tasks') }}'
                        },
                    },
                    grid: {
                        show: true,
                        borderColor: "#EBEBEB",
                        strokeDashArray: 0,
                        position: "back",
                        xaxis: {
                            show: true,
                            lines: {
                                show: true,
                            },
                        },
                        yaxis: {
                            show: false,
                            lines: {
                                show: false,
                            },
                        },
                        row: {
                            colors: undefined,
                            opacity: 0.5,
                        },
                        column: {
                            position: "back",
                            colors: undefined,
                            opacity: 0.5,
                        },
                        padding: {
                            top: 0,
                            right: 0,
                            bottom: 0,
                            left: 0,
                        },
                    },
                    tooltip: {
                        followCursor: false,
                        fixed: {
                            enabled: false
                        },
                        x: {
                            format: 'dd/MM/yy HH:mm'
                        },
                        marker: {
                            show: false
                        }
                    }
                }
                var chart = new ApexCharts(document.querySelector("#task-chart"), options);
                chart.render();
            })();
        </script>
    @endif

    <script>
        $(document).ready(function() {
            if ($(".top-10-scroll").length) {
                $(".top-10-scroll").css({
                    "max-height": 300
                }).niceScroll();
            }
        });
    </script>

    @if (isset($result->attachment) && $result->attachment == 'on')
        <script src="{{ asset('assets/custom/js/dropzone.min.js') }}"></script>
        <script>
            Dropzone.autoDiscover = false;
            myDropzone = new Dropzone("#dropzonewidget", {
                maxFiles: 20,
                // maxFilesize: 209715200,
                parallelUploads: 1,
                //acceptedFiles: ".jpeg,.jpg,.png,.gif,.svg,.pdf,.txt,.doc,.docx,.zip,.rar",
                url: "{{ route('projects.file.upload', [$currentWorkspace->slug, $project->id]) }}",
                success: function(file, response) {
                    if (response.is_success) {
                        dropzoneBtn(file, response);
                        show_toastr('{{ __('Success') }}', 'File Successfully Uploaded', 'success');
                    } else {
                        myDropzone.removeFile(file);
                        // show_toastr('error', 'File type must be match with Storage setting.');
                        show_toastr('{{ __('Error') }}',
                            'File type and size must be match with Storage setting.', 'error');
                    }
                },
                error: function(file, response) {
                    myDropzone.removeFile(file);
                    if (response.error) {
                        show_toastr('{{ __('Error') }}',
                            'File type and size must be match with Storage setting.', 'error');
                    } else {
                        show_toastr('{{ __('Error') }}',
                            'File type and size must be match with Storage setting.', 'error');
                    }
                }
            });

            myDropzone.on("sending", function(file, xhr, formData) {
                formData.append("_token", $('meta[name="csrf-token"]').attr('content'));
                formData.append("project_id", {{ $project->id }});
            });

            myDropzone.removeEventListeners();
            @if (isset($permisions) && in_array('show uploading', $permisions))
                $(".dz-hidden-input").prop("disabled", true);
            @endif

            function dropzoneBtn(file, response) {
                var html = document.createElement('span');
                var download = document.createElement('a');
                download.setAttribute('href', response.download);
                download.setAttribute('class', "action-btn btn-primary mx-1  btn btn-sm d-inline-flex align-items-center");
                download.setAttribute('data-toggle', "popover");
                download.setAttribute('download', "");
                download.setAttribute('title', "{{ __('Download') }}");
                // download.innerHTML = "<i class='fas fa-download mt-2'></i>";
                download.innerHTML = "<i class='ti ti-download'> </i>";
                html.appendChild(download);

                @if (isset($permisions) && in_array('show uploading', $permisions))
                @else
                    var del = document.createElement('a');
                    // del.setAttribute('href', response.delete);
                    // del.setAttribute('class', "action-btn btn-danger mx-1  btn btn-sm d-inline-flex align-items-center");
                    // del.setAttribute('data-toggle', "popover");
                    // del.setAttribute('title', "{{ __('Delete') }}");
                    // del.innerHTML = "<i class='ti ti-trash '></i>";

                    del.addEventListener("click", function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        if (confirm("Are you sure ?")) {
                            var btn = $(this);
                            $.ajax({
                                url: btn.attr('href'),
                                type: 'DELETE',
                                success: function(response) {
                                    if (response.is_success) {
                                        btn.closest('.dz-image-preview').remove();
                                        show_toastr('{{ __('Success') }}', 'File Successfully Deleted',
                                            'success');
                                    } else {
                                        show_toastr('{{ __('Error') }}', 'Something Wents Wrong.',
                                            'error');
                                    }
                                },
                                error: function(response) {
                                    response = response.responseJSON;
                                    if (response.is_success) {
                                        show_toastr('{{ __('Error') }}', 'Something Wents Wrong.',
                                            'error');
                                    } else {
                                        show_toastr('{{ __('Error') }}', 'Something Wents Wrong.',
                                            'error');
                                    }
                                }
                            })
                        }
                    });
                    html.appendChild(del);
                @endif

                file.previewTemplate.appendChild(html);
            }

            @php(   $files = $project->files)
            @foreach ($files as $file)
                @php($storage_file = asset($logo_project_files . $file->file_path))
                // Create the mock file:
                @if (file_exists($storage_file))
                    var mockFile = {
                        name: "{{ $file->file_name }}",
                        size: {{ filesize('storage/project_files/' . $file->file_path) }}
                    };

                    // Call the default addedfile event handler
                    myDropzone.emit("addedfile", mockFile);
                    // And optionally show the thumbnail of the file:
                    myDropzone.emit("thumbnail", mockFile, "{{ asset($logo_project_files . $file->file_path) }}");
                    myDropzone.emit("complete", mockFile);

                    dropzoneBtn(mockFile, {
                        download: "{{ route($client_keyword . 'projects.file.download', [$currentWorkspace->slug, $project->id, $file->id]) }}",
                        delete: "{{ route($client_keyword . 'projects.file.delete', [$currentWorkspace->slug, $project->id, $file->id]) }}"
                    });
                @endif
            @endforeach
        </script>
    @endif

    <script>
        function ajaxFilterTimesheetTableView() {

            var mainEle = $('#timesheet-table-view');
            var notfound = $('.notfound-timesheet');
            var notfound1 = $('.notfound-timesheet1');

            var week = parseInt($('#weeknumber').val());
            var project_id = '{{ $project->id }}';

            var data = {
                week: week,
                project_id: project_id,
            };

            $.ajax({
                url: '{{ route('filter.timesheet.table.view', '__slug') }}'.replace('__slug',
                    '{{ $currentWorkspace->slug }}'),
                data: data,
                success: function(data) {

                    $('.weekly-dates-div .weekly-dates').text(data.onewWeekDate);
                    $('.weekly-dates-div #selected_dates').val(data.selectedDate);
                    $('#project_tasks').find('option').not(':first').remove();

                    $.each(data.tasks, function(i, item) {
                        $('#project_tasks').append($("<option></option>")
                            .attr("value", i)
                            .text(item));
                    });

                    if (data.totalrecords == 0) {
                        mainEle.hide();
                        notfound.css('display', 'block');
                        notfound1.hide();
                    } else {
                        notfound.hide();
                        mainEle.show();
                    }

                    mainEle.html(data.html);
                }
            });
        }

        $(function() {
            ajaxFilterTimesheetTableView();
        });

        $(document).on('click', '.weekly-dates-div i', function() {
            var weeknumber = parseInt($('#weeknumber').val());
            if ($(this).hasClass('previous')) {
                weeknumber--;
                $('#weeknumber').val(weeknumber);
            } else if ($(this).hasClass('next')) {
                weeknumber++;
                $('#weeknumber').val(weeknumber);
            }
            ajaxFilterTimesheetTableView();
        });

        $(document).on('click', '[data-ajax-timesheet-popup="true"]', function(e) {
            e.preventDefault();

            var data = {};
            var url = $(this).data('url');
            var type = $(this).data('type');
            var date = $(this).data('date');
            var task_id = $(this).data('task-id');
            var user_id = $(this).data('user-id');
            var p_id = $(this).data('project-id');

            data.date = date;
            data.task_id = task_id;

            if (user_id != undefined) {
                data.user_id = user_id;
            }

            if (type == 'create') {
                var title = '{{ __('Create Timesheet') }}';
                data.p_id = '{{ $project->id }}';
                data.project_id = data.p_id != '-1' ? data.p_id : p_id;

            } else if (type == 'edit') {
                var title = '{{ __('Edit Timesheet') }}';
            }

            $("#commonModal .modal-title").html(title + ` <small>(` + moment(date).format("ddd, Do MMM YYYY") +
                `)</small>`);

            $.ajax({
                url: url,
                data: data,
                dataType: 'html',
                success: function(data) {
                    // $('#commonModal .body').html(data);
                    // $('#commonModal .modal-body').html(data);
                    // $("#commonModal").modal('show');
                    // commonLoader();
                    // loadConfirm();
                }
            });
        });

        $(document).on('click', '#project_tasks', function(e) {
            var mainEle = $('#timesheet-table-view');
            var notfound = $('.notfound-timesheet');

            var selectEle = $(this).children("option:selected");
            var task_id = selectEle.val();
            var selected_dates = $('#selected_dates').val();

            if (task_id != '') {

                $.ajax({
                    url: '{{ route('append.timesheet.task.html', '__slug') }}'.replace('__slug',
                        '{{ $currentWorkspace->slug }}'),
                    data: {
                        project_id: '{{ $project->id }}',
                        task_id: task_id,
                        selected_dates: selected_dates,
                    },
                    success: function(data) {

                        notfound.hide();
                        mainEle.show();

                        $('#timesheet-table-view tbody').append(data.html);
                        selectEle.remove();
                    }
                });
            }
        });

        $(document).on('change', '#time_hour, #time_minute', function() {

            var hour = $('#time_hour').children("option:selected").val();
            var minute = $('#time_minute').children("option:selected").val();
            var total = $('#totaltasktime').val().split(':');

            if (hour == '00' && minute == '00') {
                $(this).val('');
                return;
            }

            hour = hour != '' ? hour : 0;
            hour = parseInt(hour) + parseInt(total[0]);

            minute = minute != '' ? minute : 0;
            minute = parseInt(minute) + parseInt(total[1]);

            if (minute > 50) {
                minute = minute - 60;
                hour++;
            }

            hour = hour < 10 ? '0' + hour : hour;
            minute = minute < 10 ? '0' + minute : minute;

            $('.display-total-time span').text('{{ __('Total Time') }} : ' + hour + ' {{ __('Hours') }} ' +
                minute + ' {{ __('Minutes') }}');
        });
    </script>
@endpush
    