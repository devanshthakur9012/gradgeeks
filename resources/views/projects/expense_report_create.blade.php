<form method="post" enctype="multipart/form-data" class="needs-validation" novalidate
    action="@auth('web'){{ route('projects.expense.report.store', [$currentWorkspace->slug, $project->id]) }}@elseauth{{ route('client.projects.expense.report.store', [$currentWorkspace->slug, $project->id]) }}@endauth">
    @csrf
    <div class="modal-body">
        <div class="row">
            <div class="form-group col-md-6">
                <label class="col-form-label">{{ __('Project Name') }}</label><x-required></x-required>
                <input type="text" class="form-control" id="project_name" placeholder="{{ __('Project Name') }}"
                    name="project_name" value="{{ $project->name }}" required disabled>
            </div>

            <div class="form-group col-md-6">
                <label class="col-form-label">{{ __('Expense Name') }}</label><x-required></x-required>
                <input type="text" class="form-control" id="expense_name" placeholder="{{ __('Enter Title') }}"
                    name="expense_name" required>
            </div>

            <div class="form-group col-md-6">
                <label for="tasks" class="col-form-label">{{ __('Tasks') }}</label><x-required></x-required>
                <select class="form-control" id="tasks" name="tasks" required>
                    @foreach ($allTask as $task)
                        <option value="{{ $task->id }}">{{ $task->title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-6">
                <label class="col-form-label">{{ __('Amount') }}</label><x-required></x-required>
                <input type="text" class="form-control" id="amount" placeholder="{{ __('Enter Amount') }}"
                    name="amount" required>
            </div>


            <div class="form-group col-md-6">
                <label class="form-label">{{ __('Select Date') }}</label><x-required></x-required>
                <div class="input-group date ">
                    <input class="form-control datepicker2" type="text" id="date" name="date"
                        value="{{ $project->start_date }}" autocomplete="off" required>
                    <span class="input-group-text">
                        <i class="feather icon-calendar"></i>
                    </span>
                </div>
            </div>

            <div class="form-group col-md-6">
                <label class="form-label" for="attachment">Attachment</label><x-required></x-required>
                <input type="file" name="attachment" class="form-control" required>
            </div>

            <div class="form-group col-md-12 mb-0">
                <label for="task-description"
                    class="col-form-label">{{ __('Description') }}</label><x-required></x-required>
                <textarea rows="3" class="form-control" id="description" name="description" required></textarea>
            </div>
        </div>
    </div>
    <div class=" modal-footer">
        <input type="button" value="{{ __('Cancel') }}" class="btn  btn-secondary" data-bs-dismiss="modal">
        <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
    </div>

</form>


<script>
    (function() {
        const d_week = new Datepicker(document.querySelector('.datepicker2'), {
            buttonClass: 'btn',
            todayBtn: true,
            clearBtn: true,
            format: 'yyyy-mm-dd',
        });
    })();
</script>

{{-- <script>
    (function() {
        const d_week = new Datepicker(document.querySelector('.datepicker3'), {
            buttonClass: 'btn',
            todayBtn: true,
            clearBtn: true,
            format: 'yyyy-mm-dd',
        });
    })();
</script> --}}
