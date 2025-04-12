@extends('layouts.admin')

<style type="text/css">
    .modal-body {
        background: #ffffff !important;
        padding: 25px !important;
    }

    @media (max-width: 576px) {
        .header_breadcrumb {
            width: 100% !important;
        }
    }
</style>

@section('page-title')
    {{ __('Calendar') }}
@endsection

@section('links')
    @if (\Auth::guard('client')->check())
        <li class="breadcrumb-item"><a href="{{ route('client.home') }}">{{ __('Home') }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    @endif
    <li class="breadcrumb-item"> {{ __('Calendar') }}</li>
@endsection

@php
    $logo = \App\Models\Utility::get_file('avatars/');
    $logo_tasks = \App\Models\Utility::get_file('tasks/');
@endphp

@section('action-button')
    <div class="col-xl-12 col-lg-4 col-md-4 col-sm-6 col-8">
        <div class=" form-group col-auto m-0">
            <select class="  form-select" id="project_id" onchange="get_data()">
                <option value="">{{ __('All Projects') }}</option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}" @if ($project_id == $project->id) selected @endif>
                        {{ $project->name }} </option>
                @endforeach
            </select>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <!-- [ sample-page] start -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 style="width: 150px;">{{ __('Calendar') }}</h5>
                    <div id="google_cal">
                        @if ($currentWorkspace->is_googlecalendar_enabled == 'on')
                            <select class="form-control " name="calender_type" id="calender_type"
                                style="float: right;width: 180px;margin-top: -30px;" onchange="get_data()">
                                <option value="google_calendar">{{ __('Google Calendar') }}</option>
                                <option value="local_calendar" selected="true">{{ __('Local Calendar') }}</option>
                            </select>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div id='calendar' class='calendar'></div>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h4 class="mb-4">{{ __('Tasks') }}</h4>
                    <ul class="event-cards list-group list-group-flush mt-3 w-100">
                        @php
                            $date = Carbon\Carbon::now()->format('m');
                            $date1 = Carbon\Carbon::now()->format('y');
                            $this_month_task = App\Models\project::where('workspace', $currentWorkspace->id)->get();
                        @endphp
                        @foreach ($this_month_task as $task)
                            @php
                                $task_get = App\Models\task::where('project_id', $task->id)->get();
                            @endphp
                            @foreach ($task_get as $t)
                                @php
                                    $month = date('m', strtotime($t->start_date));
                                    $year = date('y', strtotime($t->start_date));
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
                                                        <h6 class="m-0">{{ $t->title }}</h6>
                                                        <small class="text-muted">
                                                            {{ $t->start_date }} to {{ $t->due_date }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endif
                            @endforeach
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
@if ($currentWorkspace)
    @push('scripts')
        <script>
            $(document).ready(function() {
                get_data();
            });

            function get_data() {
                var element = document.getElementById("calender_type");
                var calender_type = $('#calender_type :selected').val();

                if (calender_type == undefined) {
                    calender_type = 'local_calendar'
                }

                var project_id = $('#project_id :selected').val();
                var is_googlecalendar_enabled = "{{ $currentWorkspace->is_googlecalendar_enabled }}";

                $('#calendar').removeClass('google_calendar');
                $('#calendar').removeClass('local_calendar');
                $('#calendar').addClass(calender_type);

                if (project_id) {
                    document.getElementById("google_cal").style.display = "none";
                } else {
                    document.getElementById("google_cal").style.display = "block";
                }

                $.ajax({
                    @if (\Auth::guard('client')->check())
                        url: $("#path_admin").val() + "/calendar",
                    @else
                        url: $("#path_admin").val() + "/calendarr",
                    @endif
                    method: "POST",
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'calender_type': calender_type,
                        'project_id': project_id,
                        'is_googlecalendar_enabled': is_googlecalendar_enabled,
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

            $(document).on('click', '#form-comment button', function(e) {
                var comment = $("#form-comment textarea[name='comment']");
                if ($.trim(comment.val()) != '') {
                    $.ajax({
                        url: $("#form-comment").data('action'),
                        data: {
                            comment: comment.val()
                        },
                        type: 'POST',
                        success: function(data) {
                            data = JSON.parse(data);

                            if (data.user_type == 'Client') {
                                var avatar = "avatar='" + data.client.name + "'";
                                var html = "<li class='media border-bottom mb-3'>" +
                                    "                    <img class='mr-3 avatar-sm rounded-circle img-thumbnail' width='60' " +
                                    avatar + " alt='" + data.client.name + "'>" +
                                    "                    <div class='media-body mb-2'>" +
                                    "                        <h5 class='mt-0 mb-1 form-control-label'>" +
                                    data.client.name + "</h5>" +
                                    "                        " + data.comment +
                                    "                    </div>" +
                                    "                </li>";
                            } else {
                                var avatar = (data.user.avatar) ? "src='{{ $logo }}/" + data.user
                                    .avatar + "'" : "avatar='" + data.user.name + "'";
                                var html = "<li class='media border-bottom mb-3'>" +
                                    "                    <img class='mr-3 avatar-sm rounded-circle img-thumbnail' width='60' " +
                                    avatar + " alt='" + data.user.name + "'>" +
                                    "                    <div class='media-body mb-2'>" +
                                    "                           <div class='float-left'>" +
                                    "                               <h5 class='mt-0 mb-1 form-control-label'>" +
                                    data.user.name + "</h5>" +
                                    "                               " + data.comment +
                                    "                           </div>" +
                                    "                           <div class='float-right'>" +
                                    "                               <a href='#' class='delete-icon' data-url='" +
                                    data.deleteUrl + "'>" +
                                    "                                   <i class='fas fa-trash'></i>" +
                                    "                               </a>" +
                                    "                           </div>" +
                                    "                    </div>" +
                                    "                </li>";
                            }

                            $("#comments").prepend(html);
                            $("#form-comment textarea[name='comment']").val('');
                            show_toastr('{{ __('Success') }}', '{{ __('Comment Added Successfully!') }}',
                                'success');
                        },
                        error: function(data) {
                            show_toastr('{{ __('Error') }}', '{{ __('Some Thing Is Wrong!') }}',
                                'error');
                        }
                    });
                } else {
                    comment.focus();
                    show_toastr('{{ __('Error') }}', '{{ __('Please write comment!') }}', 'error');
                }
            });

            $(document).on("click", ".delete-comment", function() {
                if (confirm('{{ __('Are you sure ?') }}')) {
                    var btn = $(this);
                    $.ajax({
                        url: $(this).attr('data-url'),
                        type: 'DELETE',
                        dataType: 'JSON',
                        success: function(data) {
                            show_toastr('{{ __('Success') }}',
                                '{{ __('Comment Deleted Successfully!') }}', 'success');
                            btn.closest('.media').remove();
                        },
                        error: function(data) {
                            data = data.responseJSON;
                            if (data.message) {
                                show_toastr('{{ __('Error') }}', data.message, 'error');
                            } else {
                                show_toastr('{{ __('Error') }}', '{{ __('Some Thing Is Wrong!') }}',
                                    'error');
                            }
                        }
                    });
                }
            });

            $(document).on('click', '#form-subtask .create-subtask', function(e) {
                e.preventDefault();

                var name = $('#form-subtask input[name="name"]');
                var due_date = $('#form-subtask input[name="due_date"]');

                if ($.trim(name.val()) != '' && due_date.val() != '') {
                    $.ajax({
                        url: $("#form-subtask").data('action'),
                        type: 'POST',
                        data: {
                            name: name.val(),
                            due_date: due_date.val()
                        },
                        dataType: 'json',
                        success: function(data) {

                            show_toastr('{{ __('Success') }}', '{{ __('Sub Task Added Successfully!') }}',
                                'success');

                            var html = '<li class="list-group-item py-3">' +
                                '    <div class="form-check form-switch d-inline-block">' +
                                '        <input type="checkbox" class="form-check-input" name="option" id="option' +
                                data.id + '" value="' + data.id + '" data-url="' + data.updateUrl + '">' +
                                '        <label class="custom-control-label form-control-label" for="option' +
                                data.id + '">' + data.name + '</label>' +
                                '    </div>' +
                                '    <div class="text-end">' +
                                '        <a href="#" class="btn-danger  btn btn-sm delete-comment delete-icon delete-subtask" data-url="' +
                                data.deleteUrl + '">' +
                                '            <i class="ti ti-trash"></i>' +
                                '        </a>' +
                                '    </div>' +
                                '</li>';
                            $("#subtasks").prepend(html);
                            name.val('');
                            due_date.val('');
                            $("#form-subtask").collapse('toggle');
                        },
                        error: function(data) {
                            data = data.responseJSON;
                            if (data.message) {
                                show_toastr('{{ __('Error') }}', data.message, 'error');
                                $('#file-error').text(data.errors.file[0]).show();
                            } else {
                                show_toastr('{{ __('Error') }}', '{{ __('Some Thing Is Wrong!') }}',
                                    'error');
                            }
                        }
                    });
                } else {
                    name.focus();
                    show_toastr('{{ __('Error') }}', '{{ __('Some Thing Is Wrong!') }}', 'error');
                }
            });

            $(document).on("change", "#subtasks input[type=checkbox]", function() {
                $.ajax({
                    url: $(this).attr('data-url'),
                    type: 'POST',
                    dataType: 'JSON',
                    success: function(data) {
                        show_toastr('{{ __('Success') }}', '{{ __('Subtask Updated Successfully!') }}',
                            'success');
                    },
                    error: function(data) {
                        data = data.responseJSON;
                        if (data.message) {
                            show_toastr('{{ __('Error') }}', data.message, 'error');
                        } else {
                            show_toastr('{{ __('Error') }}', '{{ __('Some Thing Is Wrong!') }}',
                                'error');
                        }
                    }
                });
            });

            $(document).on("click", ".delete-subtask", function() {
                if (confirm('{{ __('Are you sure ?') }}')) {
                    var btn = $(this);
                    $.ajax({
                        url: $(this).attr('data-url'),
                        type: 'DELETE',
                        dataType: 'JSON',
                        success: function(data) {
                            show_toastr('{{ __('Success') }}',
                                '{{ __('Subtask Deleted Successfully!') }}', 'success');
                            btn.closest('.list-group-item').remove();
                        },
                        error: function(data) {
                            data = data.responseJSON;
                            if (data.message) {
                                show_toastr('{{ __('Error') }}', data.message, 'error');
                            } else {
                                show_toastr('{{ __('Error') }}', '{{ __('Some Thing Is Wrong!') }}',
                                    'error');
                            }
                        }
                    });
                }
            });

            $(document).on('submit', '#form-file', function(e) {

                e.preventDefault();

                $.ajax({
                    url: $("#form-file").data('url'),
                    type: 'POST',
                    data: new FormData(this),
                    dataType: 'JSON',
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(data) {
                        show_toastr('{{ __('Success') }}', '{{ __('File Upload Successfully!') }}',
                            'success');
                        // console.log(data);
                        var delLink = '';

                        if (data.deleteUrl.length > 0) {
                            delLink =
                                "<a href='#' class=' btn-danger  btn btn-sm delete-comment delete-icon delete-comment-file'  data-url='" +
                                data.deleteUrl + "'>" +
                                "                                        <i class='ti ti-trash'></i>" +
                                "                                    </a>";
                        }

                        var html = "<div class='card mb-1 shadow-none border'>" +

                            "                        <div class='card-body p-3'>" +

                            "                            <div class='row align-items-center'>" +

                            "                                <div class='col-auto'>" +

                            "                                    <div class='avatar-sm'>" +

                            "                                        <span class='avatar-title rounded text-uppercase'>" +
                            "<img class='preview_img_size' " + "src='{{ $logo_tasks }}/" + data.file +
                            "'>" +
                            "                                        </span>" +
                            "                                    </div>" +
                            "                                </div>" +
                            "                                <div class='col pl-0'>" +
                            "                                    <a href='#' class='text-muted form-control-label'>" +
                            data.name + "</a>" +
                            "                                    <p class='mb-0'>" + data.file_size +
                            "</p>" +
                            "                                </div>" +
                            "                                <div class='col-auto'>" +
                            "                                    <a download href='{{ $logo_tasks }}/" +
                            data.file +
                            "' class='edit-icon btn-primary  btn btn-sm'>" +
                            "                                        <i class='ti ti-download'></i>" +
                            "                                    </a>" +


                            "                                   <a  href='{{ $logo_tasks }}/" + data
                            .file +
                            "' class='edit-icon btn-secondary  btn btn-sm mx-1'>" +
                            "                                        <i class='ti ti-crosshair text-white'></i>" +
                            "                                    </a>" +
                            delLink +
                            "                                </div>" +
                            "                            </div>" +
                            "                        </div>" +
                            "                    </div>";
                        $("#comments-file").prepend(html);
                    },
                    error: function(data) {
                        data = data.responseJSON;
                        if (data.message) {
                            show_toastr('{{ __('Error') }}', data.message, 'error');
                            $('#file-error').text(data.errors.file[0]).show();
                        } else {
                            show_toastr('{{ __('Error') }}', '{{ __('Some Thing Is Wrong!') }}',
                                'error');
                        }
                    }
                });
            });

            $(document).on("click", ".delete-comment-file", function() {
                if (confirm('{{ __('Are you sure ?') }}')) {
                    var btn = $(this);
                    $.ajax({
                        url: $(this).attr('data-url'),
                        type: 'DELETE',
                        dataType: 'JSON',
                        success: function(data) {
                            show_toastr('{{ __('Success') }}', '{{ __('File Deleted Successfully!') }}',
                                'success');
                            btn.closest('.border').remove();
                        },
                        error: function(data) {
                            data = data.responseJSON;
                            if (data.message) {
                                show_toastr('{{ __('Error') }}', data.message, 'error');
                            } else {
                                show_toastr('{{ __('Error') }}', '{{ __('Some Thing Is Wrong!') }}',
                                    'error');
                            }
                        }
                    });
                }
            });
        </script>
    @endpush
@endif
