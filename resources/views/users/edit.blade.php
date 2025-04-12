<form method="post" action="{{ route('users.update', [$currentWorkspace->slug, $user->id]) }}" class="needs-validation"
    novalidate>
    @csrf
    @method('post')
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <label for="name" class="col-form-label">{{ __('Name') }}</label><x-required></x-required>
                <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}"
                    required />
            </div>
            <div class="col-md-12">
                <label for="email" class="col-form-label">{{ __('Email') }}</label><x-required></x-required>
                <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}"
                    required />
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{ __('Cancel') }}" class="btn  btn-secondary" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
    </div>

</form>
