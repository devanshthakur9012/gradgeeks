@php
    $expenseAttachment = \App\Models\Utility::get_file('expense/');
@endphp
@if ($project && $currentWorkspace)
    <form method="post" enctype="multipart/form-data" class="needs-validation" novalidate
        action="@auth('web'){{ route('projects.expense.report.update', [$currentWorkspace->slug, $project->id, $expenseDetails->id]) }}@elseauth{{ route('client.projects.expense.report.update', [$currentWorkspace->slug, $project->id, $expenseDetails->id]) }}@endauth">
        @csrf
        <div class="modal-body">
            <div class="row">
                <div class="form-group col-md-12">
                    <label class="col-form-label">{{ __('Project Name') }}</label><x-required></x-required>
                    <input type="text" class="form-control" id="project_name" placeholder="{{ __('Project Name') }}"
                        name="project_name" value="{{ $project->name }}" disabled>
                </div>

                <div class="form-group col-md-6">
                    <label class="col-form-label">{{ __('Expense Name') }}</label><x-required></x-required>
                    <input type="text" class="form-control" id="expense_name" placeholder="{{ __('Enter Title') }} "
                        value="{{ $expenseDetails->title }}" name="expense_name" required>
                </div>

                <div class="form-group col-md-6">
                    <label for="assign_to" class="col-form-label">{{ __('Tasks') }}</label><x-required></x-required>
                    <select class="form-control" id="tasks" name="tasks" required>
                        <option selected disabled>Select Tasks</option>
                        @foreach ($allTask as $task)
                            <option value="{{ $task->id }}"
                                {{ $expenseDetails->task_id == $task->id ? 'selected' : '' }}>{{ $task->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row d-flex align-items-center justify-content-center">
                    <div class="form-group col-md-6">
                        <label class="form-label">{{ __('Select Date') }}</label><x-required></x-required>
                        <div class="input-group date">
                            <input class="form-control datepicker2" type="text" id="date" name="date"
                                value="{{ $expenseDetails->date }}" autocomplete="off" required>
                            <span class="input-group-text">
                                <i class="feather icon-calendar"></i>
                            </span>
                        </div>
                    </div>

                    <div class="form-group col-md-6">
                        <label class="col-form-label">{{ __('Amount') }}</label><x-required></x-required>
                        <input type="text" class="form-control" id="amount"
                            placeholder="{{ __('Enter Amount') }}" value="{{ $expenseDetails->amount }}"
                            name="amount" required>
                    </div>
                </div>

                <div class="form-group col-md-12">
                    <label for="task-description"
                        class="col-form-label">{{ __('Description') }}</label><x-required></x-required>
                    <textarea rows="3" class="form-control" id="description" name="description" required> {{ $expenseDetails->description }}</textarea>
                </div>

                <div class="form-group col-md-12">
                    <label class="form-label" for="attachment">Attachment</label>
                    <input type="file" name="attachment" class="form-control">
                </div>

                <div class="mt-2">
                    <img class="img-fluid" src="{{ asset($expenseAttachment . $expenseDetails->image) }}"
                        alt="" height="100" width="100">
                </div>
            </div>
        </div>
        <div class=" modal-footer">
            <input type="button" value="{{ __('Cancel') }}" class="btn  btn-secondary" data-bs-dismiss="modal">
            <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
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
@else
    <div class="container mt-5">
        <div class="card">
            <div class="card-body p-4">
                <div class="page-error">
                    <div class="page-inner">
                        <h1>404</h1>
                        <div class="page-description">
                            {{ __('Page Not Found') }}
                        </div>
                        <div class="page-search">
                            <p class="text-muted mt-3">
                                {{ __("It's looking like you may have taken a wrong turn. Don't worry... it happens to the best of us. Here's a little tip that might help you get back on track.") }}
                            </p>
                            <div class="mt-3">
                                <a class="btn-return-home badge-blue" href="{{ route('home') }}"><i
                                        class="fas fa-reply"></i> {{ __('Return Home') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
