<form method="post" action="{{ route('invoice.item.store', [$currentWorkspace->slug, $invoice->id]) }}"
    class="needs-validation" novalidate>
    @csrf
    <div class="modal-body">
        <div class="col-md-12">
            <label for="task" class="col-form-label">{{ __('Tasks') }}</label><x-required></x-required>
            <select class="form-control" name="task" id="task" required>
                <option value="">{{ __('Select Task') }}</option>
                @foreach ($invoice->project->tasks() as $task)
                    <option value="{{ $task->id }}">{{ $task->title }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-12">
            <label for="price" class="col-form-label">{{ __('Price') }}</label><x-required></x-required>
            <div class="form-icon-user">
                <span
                    class="currency-icon bg-primary">{{ !empty($currentWorkspace->currency) ? $currentWorkspace->currency : '$' }}</span>
                <input class="form-control currency_input" type="number" min="0" value="0" id="price"
                    name="price" required>
            </div>
        </div>
    </div>
    <div class=" modal-footer">
        <input type="button" value="{{ __('Cancel') }}" class="btn  btn-secondary" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Add') }}" class="btn  btn-primary">
    </div>
</form>
