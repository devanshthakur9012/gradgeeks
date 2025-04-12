@extends('layouts.admin')

@section('page-title')
    {{ __('User Logs') }}
@endsection

@section('links')
    @if (\Auth::guard('client')->check())
        <li class="breadcrumb-item"><a href="{{ route('client.home') }}">{{ __('Home') }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    @endif
    <li class="breadcrumb-item"><a href="{{ route('users.index', $currentWorkspace->slug) }}">{{ __('users') }}</a> </li>

    <li class="breadcrumb-item"> {{ __('User Logs') }}</li>
@endsection

@php
    $logo = \App\Models\Utility::get_file('avatars/');
    $logo_tasks = \App\Models\Utility::get_file('tasks/');
@endphp

@section('action-button')
    <a href="#" class="btn btn-sm btn-primary filter" data-toggle="tooltip" title="{{ __('Filter') }}">
        <i class="ti ti-filter"></i>
    </a>
@endsection

@section('content')
    <div class="row row-gap-2 mb-4">
        <div id="show_filter" style="display:none">
            <form method="POST" action="{{ route('users_logs.filter', $currentWorkspace->slug) }}" class="row align-items-center">
                @csrf
                <div class="form-group col-md-2 col-sm-2 m-0">
                    <div class="input-group date ">
                        <input class="form-control datepicker5" type="text" id="start_date" name="start_date"
                            value="" autocomplete="off" placeholder="{{ __('Start Date') }}">
                        <span class="input-group-text">
                            <i class="feather icon-calendar"></i>
                        </span>
                    </div>
                </div>
                <div class="form-group col-md-2 col-sm-2 m-0">
                    <div class="input-group date ">
                        <input class="form-control datepicker4" type="text" id="end_date" name="end_date" value=""
                            autocomplete="off" placeholder="{{ __('End Date') }}">
                        <span class="input-group-text">
                            <i class="feather icon-calendar"></i>
                        </span>
                    </div>
                </div>
                <div class=" col-md-2 col-sm-2 m-0">
                    <select class=" form-select" name="user" id="user">
                        <option value="">{{ __('Select Users') }}</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id . ',user' }}">{{ $user->name }}</option>
                        @endforeach
                        @foreach ($clients as $client)
                            <option value="{{ $client->id . ',client' }}">{{ $client->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-sm-2 m-0">
                    <button type="submit" class=" btn btn-primary btn-sm me-1" data-bs-toggle="tooltip" title=""
                        data-bs-original-title="Apply"><i class="ti ti-search"></i></button>
                    <a href="{{ route('users_logs.index', $currentWorkspace->slug) }}" class="btn btn-sm btn-danger"
                        data-bs-toggle="tooltip" title="" data-bs-original-title="Reset">
                        <i class="ti ti-refresh"></i>
                    </a>
                </div>
            </form>
        </div>
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-hover mb-0 animated" id="selection-datatable">
                            <thead>
                                <th>{{ __('User Name') }}</th>
                                {{-- <th>{{__('Email')}}</th> --}}
                                <th>{{ __('Role') }}</th>
                                <th>{{ __('Last Login') }}</th>
                                <th>{{ __('IP') }}</th>
                                <th>{{ __('Country') }}</th>
                                <th>{{ __('Device type') }}</th>
                                <th>{{ __('OS Name') }}</th>
                                @auth('web')
                                    <th>{{ __('Action') }}</th>
                                @endauth
                            </thead>
                            <tbody>
                                @php
                                    $current_month = Carbon\Carbon::now()->format('m');
                                    $current_year = Carbon\Carbon::now()->format('y');
                                @endphp
                                @foreach ($user_logs as $key => $user_log)
                                    @php
                                        $month = date('m', strtotime($user_log->date));
                                        $year = date('y', strtotime($user_log->date));
                                    @endphp
                                    @if ($current_month == $month && $current_year == $year)
                                        <tr>
                                            <td class="Id sorting_1">{{ $user_log->name }}</td>
                                            {{-- <td>{{ $user_log->email }}</td> --}}
                                            <td><span class="badge p-2 px-3 bg-success">
                                                {{ $user_log->role }}</span></td>
                                            <td>{{ $user_log->date }}</td>
                                            <td>{{ $user_log->ip }}</td>
                                            <td>{{ $user_log->country }}</td>
                                            <td>{{ $user_log->device_type }}</td>
                                            <td>{{ $user_log->os_name }}</td>
                                            @auth('web')
                                                <td class="text-right">
                                                    <a href="#"
                                                        class="btn-warning  btn btn-sm"
                                                        data-ajax-popup="true" data-size="lg"
                                                        data-title="{{ __('User Logs') }}"
                                                        data-url="{{ route('users_logs.show', [$currentWorkspace->slug, $user_log->id]) }}"
                                                        data-toggle="tooltip" title="{{ __('Show') }}">
                                                        <i class="ti ti-eye"></i>
                                                    </a>
                                                    <a href="#"
                                                        class="btn-danger  btn btn-sm bs-pass-para"
                                                        data-confirm="{{ __('Are You Sure?') }}"
                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="delete-form-{{ $user_log->id }}"
                                                        data-toggle="tooltip" title="{{ __('Delete') }}">
                                                        <i class="ti ti-trash"></i>
                                                    </a>
                                                    {!! Form::open([
                                                        'method' => 'DELETE',
                                                        'route' => ['users_logs.destroy', [$currentWorkspace->slug, $user_log->id]],
                                                        'id' => 'delete-form-' . $user_log->id,
                                                    ]) !!}
                                                    {!! Form::close() !!}
                                                </td>
                                            @endauth
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        (function() {
            const d_week = new Datepicker(document.querySelector('.datepicker4'), {
                buttonClass: 'btn',
                todayBtn: true,
                clearBtn: true,
                format: 'yyyy-mm-dd',
            });
        })();
    </script>
    <script>
        (function() {
            const d_week = new Datepicker(document.querySelector('.datepicker5'), {
                buttonClass: 'btn',
                todayBtn: true,
                clearBtn: true,
                format: 'yyyy-mm-dd',
            });
        })();
    </script>
    <script type="text/javascript">
        $(document).on("click", ".filter", function() {

            var x = document.getElementById("show_filter");
            if (x.style.display === "none") {
                x.style.display = "block";
            } else {
                x.style.display = "none";
            }
        });
    </script>
@endpush
