<form method="post" action="{{ route('contract_type.update', [$currentWorkspace->slug, $contractsType->id]) }}"
    class="needs-validation" novalidate>
    @csrf
    @method('PUT')
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
            <div class="form-group col-md-12">
                <label for="name"
                    class="col-form-label">{{ __('Contract Type Name') }}</label><x-required></x-required>
                <input class="form-control" type="text" name="name" value="{{ $contractsType->name }}" required
                    placeholder="{{ __('Enter type name') }}">
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn  btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
        <button type="submit" class="btn  btn-primary">{{ __('Update') }}</button>
    </div>
</form>
