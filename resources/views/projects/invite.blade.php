<form method="post" action="{{ route('projects.invite.update', [$currentWorkspace->slug, $project->id]) }}"
    class="needs-validation" novalidate>
    @csrf
    <div class="modal-body">
        <div class="form-group col-md-12 mb-0">
            <label for="users_list" class="form-label">{{ __('Users') }}</label><x-required></x-required>
            <select class=" multi-select" required id="users_list" name="users_list[]" data-toggle="select2"
                multiple="multiple" data-placeholder="{{ __('Select Users ...') }}">
                @foreach ($currentWorkspace->users($currentWorkspace->created_by) as $user)
                    @if ($user->pivot->is_active)
                        @php
                            $user_p = App\Models\UserProject::where('user_id', '=', $user->id)
                                ->where('project_id', '=', $project->id)
                                ->first();
                        @endphp
                        @if (!$user_p)
                            <option value="{{ $user->email }}">{{ $user->name }} - {{ $user->email }}</option>
                        @endif
                    @endif
                @endforeach
            </select>
        </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{ __('Cancel') }}" class="btn  btn-secondary" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Invite') }}" class="btn  btn-primary">
    </div>
</form>
