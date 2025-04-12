<form method="post" action="{{ route('projects.store', $currentWorkspace->slug) }}" class="needs-validation" novalidate>
    @csrf
    <div class="modal-body">
        <div class="row">
            @if ($currentWorkspace->is_chagpt_enable())
                <div class="text-end col-12">
                    <a href="#" data-size="lg" data-ajax-popup-over="true" class="btn btn-sm btn-primary"
                        data-url="{{ route('generate', ['project']) }}" data-bs-toggle="tooltip" data-bs-placement="top"
                        title="{{ __('Generate with AI') }}"
                        data-title="{{ __('Generate Project Name & Description') }}">
                        <i class="fas fa-robot px-1"></i>{{ __('Generate with AI') }}
                    </a>
                </div>
            @endif
            <div class="form-group col-md-12">
                <label for="projectname" class="col-form-label">{{ __('Name') }}</label><x-required></x-required>
                <input class="form-control" type="text" id="projectname" name="name" required=""
                    placeholder="{{ __('Project Name') }}">
            </div>
            <div class="form-group col-md-12">
                <label for="description" class="col-form-label">{{ __('Description') }}</label><x-required></x-required>
                <textarea rows="3" class="form-control" id="description" name="description" required=""
                    placeholder="{{ __('Add Description') }}"></textarea>
            </div>
            <div class="col-md-12">
                <label for="users_list" class="col-form-label">{{ __('Users') }}</label>
                <select class=" multi-select" id="users_list" name="users_list[]" data-toggle="select2"
                    multiple="multiple" data-placeholder="{{ __('Select Users ...') }}">
                    @foreach ($currentWorkspace->users($currentWorkspace->created_by) as $user)
                        <option value="{{ $user->email }}">{{ $user->name }} - {{ $user->email }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{ __('Cancel') }}" class="btn  btn-secondary" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
    </div>
</form>
