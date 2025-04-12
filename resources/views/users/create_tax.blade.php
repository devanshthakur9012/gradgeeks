<form method="post" action="{{ route('tax.store', [$currentWorkspace->slug]) }}" class="needs-validation" novalidate>
    @csrf
    <div class="modal-body">
        <div class="form-group">
            <label for="name" class="col-form-label">{{ __('Name') }}</label><x-required></x-required>
            <input type="text" class="form-control" id="name" name="name" required />
        </div>
        <div class="form-group">
            <label for="rate" class="col-form-label">{{ __('Rate') }}</label><x-required></x-required>
            <input type="number" class="form-control" id="rate" name="rate" min="0" step=".01"
                required />
        </div>
    </div>
    <div class=" modal-footer">
        <input type="button" value="{{ __('Cancel') }}" class="btn  btn-secondary" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
    </div>
</form>
