<div class="card-body table-border-style">
    <div class="table-responsive ">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th class="text-muted">{{ __('Task') }}</th>
                    @foreach ($days['datePeriod'] as $key => $perioddate)
                        <th class="text-dark m-0 ">{{ $perioddate->format('D d M') }}</th>
                    @endforeach
                    <th class="th-sm">
                        <span class="pr-1">{{ __('Total') }}</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                @if (isset($allProjects) && $allProjects == true)
                    @foreach ($timesheetArray as $key => $timesheet)
                        <tr class="">
                            <td colspan="9"><span data-tooltip="Project"
                                    class="project-name pad_row custom-tooltip">{{ $timesheet['project_name'] }}</span>
                            </td>
                        </tr>
                        @foreach ($timesheet['taskArray'] as $key => $taskTimesheet)
                            <tr>
                                <td colspan="9">
                                    <div data-tooltip="Task" class="task-name  ml-3 pad_row custom-tooltip">
                                        {{ $taskTimesheet['task_name'] }}
                                    </div>
                                </td>
                            </tr>
                            @foreach ($taskTimesheet['dateArray'] as $dateTimeArray)
                                <tr>
                                    <td>
                                        <div data-tooltip="User" class="task blue ml-5 custom-tooltip">
                                            {{ $dateTimeArray['user_name'] }}
                                        </div>
                                    </td>
                                    @foreach ($dateTimeArray['week'] as $dateSubArray)
                                        <td>
                                            @auth('client')
                                                <div class="form-control border-dark wid-120">
                                                    {{ $dateSubArray['time'] != '00:00' ? $dateSubArray['time'] : '00:00' }}
                                                </div>
                                                @elseauth
                                                @if (
                                                    (Auth::user()->currant_workspace == $currentWorkspace->id && $currentWorkspace->created_by == Auth::user()->id) ||
                                                        Auth::user()->id == $dateTimeArray['user_id']
                                                )
                                                    <div role="button" class="form-control border-dark wid-120"
                                                        title="{{ $dateSubArray['type'] == 'edit' ? __('Click to Edit/Delete Timesheet') : __('Click to Add Timesheet') }}"
                                                        data-ajax-timesheet-popup="true"
                                                        data-type="{{ $dateSubArray['type'] }}"
                                                        data-user-id="{{ $dateTimeArray['user_id'] }}"
                                                        data-project-id="{{ $timesheet['project_id'] }}"
                                                        data-task-id="{{ $taskTimesheet['task_id'] }}"
                                                        data-date="{{ $dateSubArray['date'] }}"
                                                        data-url="{{ $dateSubArray['url'] }}">
                                                        {{ $dateSubArray['time'] != '00:00' ? $dateSubArray['time'] : '00:00' }}
                                                    </div>
                                                @else
                                                    <div class="form-control border-dark wid-120">
                                                        {{ $dateSubArray['time'] != '00:00' ? $dateSubArray['time'] : '00:00' }}
                                                    </div>
                                                @endif
                                            @endauth
                                        </td>
                                    @endforeach
                                    <td>
                                        <div class="total form-control bg-transparent border-dark wid-120">
                                            {{ $dateTimeArray['totaltime'] }}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    @endforeach
                @else
                    @foreach ($timesheetArray as $key => $timesheet)
                        <tr>
                            <td>
                                <div class="task-name ml-3">
                                    {{ $timesheet['task_name'] }}
                                </div>
                            </td>
                            @foreach ($timesheet['dateArray'] as $day => $datetime)
                                <td>
                                    @auth('client')
                                        <div class="form-control border-dark">
                                            {{ $datetime['time'] != '00:00' ? $datetime['time'] : '00:00' }}
                                        </div>
                                        @elseauth
                                        @if (Auth::user()->currant_workspace == $currentWorkspace->id && $currentWorkspace->created_by == Auth::user()->id)
                                            <div role="button" class="form-control border-dark wid-120"
                                                title="{{ $datetime['type'] == 'edit' ? __('Click to Edit/Delete Timesheet') : __('Click to Add Timesheet') }}"
                                                data-ajax-timesheet-popup="true" data-type="{{ $datetime['type'] }}"
                                                data-task-id="{{ $timesheet['task_id'] }}"
                                                data-date="{{ $datetime['date'] }}" data-url="{{ $datetime['url'] }}">
                                                {{ isset($datetime['time']) ? $datetime['time'] : '00:00' }}
                                            </div>
                                        @else
                                            <div role="button" class="form-control border-dark wid-120"
                                                title="{{ $datetime['type'] == 'edit' ? __('Click to Edit/Delete Timesheet') : __('Click to Add Timesheet') }}"
                                                data-ajax-timesheet-popup="true" data-type="{{ $datetime['type'] }}"
                                                data-task-id="{{ $timesheet['task_id'] }}"
                                                data-date="{{ $datetime['date'] }}" data-url="{{ $datetime['url'] }}">
                                                {{ $datetime['time'] != '00:00' ? $datetime['time'] : '00:00' }}
                                            </div>
                                        @endif
                                    @endauth
                                    @if (!Auth::guard('client')->check() && !Auth::check())
                                        <div class="form-control border-dark">
                                            {{ $datetime['time'] != '00:00' ? $datetime['time'] : '00:00' }}
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                            <td>
                                <div class="total form-control bg-transparent border-dark wid-120">
                                    {{ $timesheet['totaltime'] }}
                                </div>
                            </td>
                        </tr>
                    @endforeach
                @endif

            </tbody>
            <tfoot>
                <tr class="footer-total bg-primary">
                    <td>{{ __('Total') }}</td>
                    @foreach ($totalDateTimes as $key => $totaldatetime)
                        <td>
                            <div class=" form-control border-dark wid-120">
                                {{ $totaldatetime != '00:00' ? $totaldatetime : '00:00' }}
                            </div>
                        </td>
                    @endforeach
                    <td>
                        <div class="form-control border-dark wid-120">
                            {{ $calculatedtotaltaskdatetime }}
                        </div>
                    </td>
                </tr>
            </tfoot>


        </table>
    </div>
</div>
<div class="text-center d-flex align-items-center justify-content-center  mt-4 mb-5">
    <h5 class="f-w-900 me-2 mb-0">{{ __('Time Logged:') }}</h5>
    <span class="p-2  f-w-900 rounded  bg-primary d-inline-block border border-dark">
        {{ $calculatedtotaltaskdatetime }}
    </span>
</div>

<style type="text/css">
    .table thead {
        line-height: 30px !important;
    }
</style>
