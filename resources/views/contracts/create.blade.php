<form method="post" action="{{ route('contracts.store', [$currentWorkspace->slug]) }}" class="needs-validation"
    novalidate>
    @csrf
    <div class="modal-body">
        <div class="row">
            @if ($currentWorkspace->is_chagpt_enable())
                <div class="text-end col-12">
                    <a href="#" data-size="lg" data-ajax-popup-over="true" class="btn btn-sm btn-primary"
                        data-url="{{ route('generate', ['contract']) }}" data-bs-toggle="tooltip"
                        data-bs-placement="top" title="{{ __('Generate with AI') }}"
                        data-title="{{ __('Generate Contract Subject & Description') }}">
                        <i class="fas fa-robot px-1"></i>{{ __('Generate with AI') }}
                    </a>
                </div>
            @endif
            <div class="col-md-6 form-group">
                {{ Form::label('client_id', __('Client Name'), ['class' => 'col-form-label']) }}<x-required></x-required>
                {{ Form::select('client_id', $client, null, ['class' => 'form-control client_id', 'id' => 'client_id', 'data-toggle' => 'select', 'required' => 'required']) }}
            </div>
            <div class="col-md-6 form-group">
                {{ Form::label('project', __('Project'), ['class' => 'col-form-label']) }}<x-required></x-required>
                <div class="project-div">
                    {{ Form::select('project_id', $projects, null, ['class' => 'form-control', 'id' => 'project', 'placeholder' => __('Select Projects'), 'name' => 'project', 'required' => 'required']) }}
                </div>
            </div>
            <div class="col-md-6 form-group">
                {{ Form::label('subject', __('Subject'), ['class' => 'col-form-label']) }}<x-required></x-required>
                {{ Form::text('subject', '', ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter subject of contract')]) }}
            </div>
            <div class="col-md-6 form-group">
                {{ Form::label('value', __('Value'), ['class' => 'col-form-label']) }}<x-required></x-required>
                {{ Form::number('value', '', ['class' => 'form-control', 'required' => 'required', 'min' => '1', 'placeholder' => __('Enter value of contract')]) }}
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('start_date', __('Start Date'), ['class' => 'col-form-label']) }}<x-required></x-required>
                {{ Form::date('start_date', null, ['class' => 'form-control', 'required' => 'required']) }}
            </div>
            <div class="form-group col-md-6">
                {{ Form::label('end_date', __('Due Date'), ['class' => 'col-form-label']) }}<x-required></x-required>
                {{ Form::date('end_date', null, ['class' => 'form-control', 'required' => 'required']) }}
            </div>
            <div class="form-group col-md-12">
                {{ Form::label('type', __('Type'), ['class' => 'col-form-label']) }}<x-required></x-required>
                {!! Form::select('type', $contractType, null, ['class' => 'form-select', 'required' => 'required']) !!}
                @if (count($contractType) <= 0)
                    <div class="text-muted text-xs">
                        {{ __('Please create new contract type') }}
                        <a
                            href="{{ route('contract_type.index', !empty($currentWorkspace) ? $currentWorkspace->slug : 0) }}">{{ __('here') }}</a>.
                    </div>
                @endif
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    {{ Form::label('description', __('Description'), ['class' => 'col-form-label']) }}
                    {{ Form::textarea('description', '', ['class' => 'form-control', 'placeholder' => __('Enter description about contract...')]) }}
                </div>
            </div>
            {{-- <div class="col-md-12 form-group">
                <label class="col-form-label">{{ __('Status') }}</label>
                <div class="d-flex radio-check">
                    <div class="custom-control custom-radio custom-control-inline m-1">
                        <input class="form-check-input" type="radio" id="on" value="on" name="status">
                        <label class="form-check-labe" for="pre">{{ __('Start') }}</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline m-1">
                        <input class="form-check-input" type="radio" id="off" value="off" name="status">
                        <label class="form-check-labe" for="post">{{ __('Close') }}</label>
                    </div>
                </div>
            </div> --}}
        </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{ __('Cancel') }}" class="btn btn-secondary" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Create') }}" class="btn btn-primary">
    </div>
</form>
