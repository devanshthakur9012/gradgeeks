{{ Form::model($timesheet, ['route' => ['project.timesheet.update', ['slug' => $currentWorkspace->slug, 'timesheet_id' => $timesheet->id, 'project_id' => $project_id]], 'method' => 'POST', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <input type="hidden" name="project_id" value="{{ $parseArray['project_id'] }}">
    <input type="hidden" name="task_id" value="{{ $parseArray['task_id'] }}">
    <input type="hidden" name="date" value="{{ $timesheet->date }}">
    <input type="hidden" id="totaltasktime"
        value="{{ $parseArray['totaltaskhour'] . ':' . $parseArray['totaltaskminute'] }}">

    @if ($currentWorkspace->is_chagpt_enable())
        <div class="text-end col-12">
            <a href="#" data-size="lg" data-ajax-popup-over="true" class="btn btn-sm btn-primary"
                data-url="{{ route('generate', ['timesheet']) }}" data-bs-toggle="tooltip" data-bs-placement="top"
                title="{{ __('Generate with AI') }}" data-title="{{ __('Generate Timesheet Description') }}">
                <i class="fas fa-robot px-1"></i>{{ __('Generate with AI') }}
            </a>
        </div>
    @endif

    <div class="form-group">
        <label class="col-form-label">{{ __('Project') }}</label><x-required></x-required>
        <input type="text" class="form-control" value="{{ $parseArray['project_name'] }}" disabled="disabled">
    </div>

    <div class="form-group">
        <label class="col-form-label">{{ __('Task') }}</label><x-required></x-required>
        <input type="text" class="form-control" value="{{ $parseArray['task_name'] }}" disabled="disabled">
    </div>

    <div class="row">
        <div class="col-md-12">
            <label for="time" class="col-form-label">{{ __('Time') }}</label><x-required></x-required>
        </div>
        <div class="col-md-6">
            <select class="form-control" name="time_hour" id="time_hour" required="">
                <option value="">{{ __('Hours') }}</option>
                <?php for ($i = 0; $i < 23; $i++) { $i = $i < 10 ? '0' . $i : $i; ?>
                <option value="{{ $i }}" {{ $parseArray['time_hour'] == $i ? 'selected="selected"' : '' }}>
                    {{ $i }}</option>
                <?php } ?>
            </select>
        </div>

        <div class="col-md-6">
            <select class="form-control" name="time_minute" id="time_minute" required>
                <option value="">{{ __('Minutes') }}</option>
                <?php for ($i = 0; $i < 51; $i += 10) { $i = $i < 10 ? '0' . $i : $i; ?>
                <option value="{{ $i }}"
                    {{ $parseArray['time_minute'] == $i ? 'selected="selected"' : '' }}>{{ $i }}</option>
                <?php } ?>
            </select>
        </div>

        <div class="col-md-12 mt-1">
            <label for="description" class="col-form-label">{{ __('Description') }}</label>
            <textarea class="form-control " id="description" rows="3" name="description">{{ $timesheet->description }}</textarea>
        </div>
        <div class="col-md-12">
            <div class="display-total-time">
                <i class="fas fa-clock"></i>
                <span>{{ __('Total Time') }} :
                    {{ $parseArray['totaltaskhour'] . ' ' . __('Hours') . ' ' . $parseArray['totaltaskminute'] . ' ' . __('Minutes') }}</span>
            </div>
        </div>
        @php($id = str_replace('.', '', uniqid('', true)))
    </div>
</div>

<div class="modal-footer">
    <div class="row col-12 m-0">
        <div class="text-start col-6">
            <a href="#"
                onclick="(confirm('{{ __('Are you sure ?') }}')?document.getElementById('delete-form-{{ $id }}').submit(): '');"
                class="btn-danger  btn" data-toggle="tooltip" title="{{ __('Delete') }}">
                <i class="ti ti-trash"></i>
            </a>
        </div>
        <div class="text-end  col-6">
            <input type="button" value="{{ __('Cancel') }}" class="btn  btn-secondary me-1" data-bs-dismiss="modal">
            <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
        </div>
    </div>
</div>
{{ Form::close() }}

<form id="delete-form-{{ $id }}" class=""
    action="{{ route('timesheet.destroy', [$currentWorkspace->slug, $timesheet->id]) }}" method="POST"
    style="display: none;">
    @csrf
    @method('DELETE')
</form>
