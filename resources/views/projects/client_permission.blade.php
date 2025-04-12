<form method="post" class="needs-validation" novalidate
    action="{{ route('projects.client.permission.store', [$currentWorkspace->slug, $project->id, $client->id]) }}">
    @csrf
    @include('projects.project_permission')
    <div class=" modal-footer">
        <input type="button" value="{{ __('Cancel') }}" class="btn  btn-secondary" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Update') }}" class="btn btn-primary">
    </div>
</form>
