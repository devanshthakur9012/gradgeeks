<form method="post" action="{{ route('contract_type.store', [$currentWorkspace->slug]) }}" class="needs-validation"
    novalidate>
    @csrf
    <div class="modal-body">
        <div class="row">
            @if ($currentWorkspace->is_chagpt_enable())
                <div class="text-end col-12">
                    <a href="#" data-size="md" data-ajax-popup-over="true" class="btn btn-sm btn-primary"
                        data-url="{{ route('generate', ['contract type']) }}" data-bs-toggle="tooltip"
                        data-bs-placement="top" title="{{ __('Generate with AI') }}"
                        data-title="{{ __('Generate Contract Type Name') }}">
                        <i class="fas fa-robot px-1"></i>{{ __('Generate with AI') }}
                    </a>
                </div>
            @endif
            <div class="form-group col-12">
                {{ Form::label('name', __('Contract Type Name'), ['class' => 'col-form-label']) }}<x-required></x-required>
                {{ Form::text('name', '', ['class' => 'form-control', 'required' => 'required', 'placeholder' => __('Enter type name')]) }}
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn  btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
        <button type="submit" class="btn  btn-primary">{{ __('Create') }}</button>
    </div>
</form>
