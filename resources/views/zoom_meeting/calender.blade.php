@extends('layouts.admin')

@section('page-title')
    {{ __('Calendar') }}
@endsection

@section('links')
    @if (\Auth::guard('client')->check())
        <li class="breadcrumb-item"><a href="{{ route('client.home') }}">{{ __('Home') }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    @endif
    @if (\Auth::guard('client')->check())
        <li class="breadcrumb-item">
            <a href="{{ route('client.zoom-meeting.index', $currentWorkspace->slug) }}">{{ __('Zoom Meeting') }}</a>
        </li>
    @else
        <li class="breadcrumb-item">
            <a href="{{ route('zoom-meeting.index', $currentWorkspace->slug) }}">{{ __('Zoom Meeting') }}</a>
        </li>
    @endif
    <li class="breadcrumb-item"> {{ __('Calendar') }}</li>
@endsection

@section('action-button')
    @auth('web')
        @if (isset($currentWorkspace) && $currentWorkspace->creater->id == Auth::id())
            <a href="#" class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="lg"
                data-title="{{ __('Create Meeting') }}" data-toggle="tooltip" title="{{ __('Create') }}"
                data-url="{{ route('zoom-meeting.create', $currentWorkspace->slug) }}">
                <i class="ti ti-plus "></i>
            </a>
        @endif
    @endauth

    @auth('client')
        <a href="{{ route('client.zoom-meeting.index', $currentWorkspace->slug) }}" data-toggle="tooltip"
            title="{{ __('calendar') }}" class="btn btn-sm btn-primary" id=""> <i
                class="ti ti-arrow-back-up"></i> </a>
    @endauth
@endsection

@section('content')
    <div class="row">
        <!-- [ sample-page] start -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 style="width: 150px;">{{ __('Calendar') }}</h5>
                    @if ($currentWorkspace->is_googlecalendar_enabled == 'on')
                        <select class="form-control" name="calender_type" id="calender_type"
                            style="float: right;width: 180px;margin-top: -30px;" onchange="get_data()">
                            <option value="google_calendar">{{ __('Google Calendar') }}</option>
                            <option value="local_calendar" selected="true">{{ __('Local Calendar') }}</option>
                        </select>
                    @endif
                </div>
                <div class="card-body">
                    <div id='calendar' class='calendar'></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-4">Meetings</h4>
                    <ul class="event-cards list-group list-group-flush mt-3 w-100">
                        @php
                            $date = Carbon\Carbon::now()->format('m');
                            $date1 = Carbon\Carbon::now()->format('y');
                            $this_month_meeting = App\Models\ZoomMeeting::get();
                        @endphp
                        @foreach ($meetings as $meeting)
                            @php
                                $month = date('m', strtotime($meeting->start_date));
                                $year = date('y', strtotime($meeting->start_date));
                            @endphp
                            @if ($date == $month && $date1 == $year)
                                <li class="list-group-item card mb-3">
                                    <div class="row align-items-center justify-content-between">
                                        <div class="col-auto mb-3 mb-sm-0">
                                            <div class="d-flex align-items-center">
                                                <div class="theme-avtar bg-primary">
                                                    <i class="fa fa-tasks"></i>
                                                </div>
                                                <div class="ms-3">
                                                    <h6 class="m-0">{{ $meeting->title }}</h6>
                                                    <small class="text-muted">{{ $meeting->start_date }} </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@if ($currentWorkspace)
    @push('css-page')
    @endpush
    @push('scripts')
        <script src="{{ asset('assets/js/plugins/main.min.js') }}"></script>
        <script type="text/javascript">
            $(document).ready(function() {
                get_data();
            });

            function get_data() {
                var calender_type = $('#calender_type :selected').val();

                $.ajax({
                    url: $("#path_admin").val() + "/event/get_event_data",
                    method: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'calender_type': calender_type
                    },
                    success: function(data) {
                        (function() {
                            var etitle;
                            var etype;
                            var etypeclass;
                            var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
                                headerToolbar: {
                                    left: 'prev,next today',
                                    center: 'title',
                                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                                },
                                buttonText: {
                                    timeGridDay: "{{ __('Day') }}",
                                    timeGridWeek: "{{ __('Week') }}",
                                    dayGridMonth: "{{ __('Month') }}"
                                },
                                slotLabelFormat: {
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    hour12: false,
                                },

                                themeSystem: 'bootstrap',
                                slotDuration: '00:10:00',
                                navLinks: true,
                                droppable: true,
                                selectable: true,
                                selectMirror: true,
                                editable: true,
                                dayMaxEvents: true,
                                handleWindowResize: true,
                                height: 'auto',
                                timeFormat: 'H(:mm)',
                                events: data,
                            });
                            calendar.render();
                        })();
                    }
                });
            }
        </script>
    @endpush
@endif
