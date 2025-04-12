{{ Form::open(['url' => route('project.timesheet.store', ['slug' => $currentWorkspace->slug, 'project_id' => $parseArray['project_id']]), 'id' => 'project_form', 'class' => 'needs-validation', 'novalidate']) }}
<div class="modal-body">
    <input type="hidden" name="project_id" value="{{ $parseArray['project_id'] }}">
    <input type="hidden" name="task_id" value="{{ $parseArray['task_id'] }}">
    <input type="hidden" name="date" value="{{ $parseArray['date'] }}">
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
        <input type="text" class="form-control" value="{{ $parseArray['project_name'] }}" disabled="disabled"
            required>
    </div>

    <div class="form-group">
        <label class="col-form-label">{{ __('Task') }}</label><x-required></x-required>
        <input type="text" class="form-control" value="{{ $parseArray['task_name'] }}" disabled="disabled" required>
    </div>

    <div class="row">
        <div class="col-md-12">
            <label for="time" class="col-form-label">{{ __('Time') }}</label><x-required></x-required>
        </div>
        <div class="col-md-6">
            <select class="form-control" name="time_hour" id="time_hour" required>
                <option value="">{{ __('Hours') }}</option>
                <?php for ($i = 0; $i < 23; $i++) { $i = $i < 10 ? '0' . $i : $i; ?>
                <option value="{{ $i }}">{{ $i }}</option>
                <?php } ?>
            </select>
        </div>

        <div class="col-md-6">
            <select class="form-control" name="time_minute" id="time_minute" required>
                <option value="">{{ __('Minutes') }}</option>
                <?php for ($i = 0; $i < 51; $i += 10) { $i = $i < 10 ? '0' . $i : $i; ?>
                <option value="{{ $i }}">{{ $i }}</option>
                <?php } ?>
            </select>
        </div>
    </div>

    <div class="form-group py-4">
        <label for="description">{{ __('Description') }}</label>
        <textarea class="form-control" id="description" rows="3" name="description"></textarea>
    </div>

    <div class="display-total-time">
        <i class="fas fa-clock"></i>
        <span>{{ __('Total Time') }} :
            {{ $parseArray['totaltaskhour'] . ' ' . __('Hours') . ' ' . $parseArray['totaltaskminute'] . ' ' . __('Minutes') }}
        </span>
    </div>

</div>
<div class="modal-footer">
    <input type="button" value="{{ __('Cancel') }}" class="btn  btn-secondary" data-bs-dismiss="modal">
    <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
</div>

{{ Form::close() }}
