<form class="needs-validation" novalidate method="post"
    action="{{ route('projects.share', [$currentWorkspace->slug, $project->id]) }}">
    @csrf
    <div class="modal-body">
        <div class=" col-md-12 mb-0">
            <label for="users_list" class="col-form-label">{{ __('Clients') }}</label><x-required></x-required>
            <select class="multi-select" id="clients" data-toggle="select2" required name="clients[]" multiple="multiple"
                data-placeholder="{{ __('Select Clients ...') }}">
                @foreach ($currentWorkspace->clients as $client)
                    @if ($client->pivot->is_active)
                        @php
                            $user_p = App\Models\ClientProject::where('client_id', '=', $client->id)
                                ->where('project_id', '=', $project->id)
                                ->first();
                        @endphp
                        @if (!$user_p)
                            <option value="{{ $client->id }}">{{ $client->name }} - {{ $client->email }}</option>
                        @endif
                    @endif
                @endforeach
            </select>
        </div>
    </div>
    <div class="modal-footer">
        <input type="button" value="{{ __('Cancel') }}" class="btn  btn-secondary" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Share') }}" class="btn  btn-primary">
    </div>
</form>
