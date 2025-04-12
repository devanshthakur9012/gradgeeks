@php
    $expenseAttachment = \App\Models\Utility::get_file('expense/');
    $taskName = \App\Models\Task::find($expenseDetails->task_id);
@endphp
@if ($project && $currentWorkspace)
    {{-- <form class="">
        @csrf
        <div class="modal-body">
            <div class="row">
                <div class="form-group col-md-12">
                    <label class="col-form-label">{{ __('Project Name') }}</label>
                    <input type="text" class="form-control" id="project_name" placeholder="{{ __('Project Name') }}"
                        name="project_name" value="{{ $project->name }}" disabled>
                </div>

                <div class="form-group col-md-6">
                    <label class="col-form-label">{{ __('Expense Name') }}</label>
                    <input type="text" class="form-control" id="expense_name" placeholder="{{ __('Enter Title') }} "
                        value="{{ $expenseDetails->title }}" name="expense_name" required>
                </div>

                <div class="form-group col-md-6">
                    <label for="assign_to" class="col-form-label">{{ __('Tasks') }}</label>
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
                        <label class="form-label">{{ __('Select Date') }}</label>
                        <div class="input-group date">
                            <input class="form-control datepicker2" type="text" id="date" name="date"
                                value="{{ $expenseDetails->date }}" autocomplete="off" required>
                            <span class="input-group-text">
                                <i class="feather icon-calendar"></i>
                            </span>
                        </div>
                    </div>

                    <div class="form-group col-md-6">
                        <label class="col-form-label">{{ __('Amount') }}</label>
                        <input type="text" class="form-control" id="amount" placeholder="{{ __('Enter Amount') }}"
                            value="{{ $expenseDetails->amount }}" name="amount" required>
                    </div>
                </div>





                <div class="form-group col-md-12 mb-0">
                    <label for="task-description" class="col-form-label">{{ __('Description') }}</label>
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
    </form> --}}
    <div class="row p-3">
        <div class="col-12">
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label" for="bank_details" class="form-label">{{ __('Project Name : ') }}</label><br>
                </div>
                <div class="col-md-6">
                    {{ $project->name }}
                </div>
            </div>
            <hr class="my-3">
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label" for="bank_details"
                        class="form-label">{{ __('Expense Name : ') }}</label><br>
                </div>
                <div class="col-md-6">
                    {{ $expenseDetails->title }}
                </div>
            </div>
            <hr class="my-3">
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label" for="bank_details" class="form-label">{{ __('Task Name : ') }}</label><br>
                </div>
                <div class="col-md-6">

                    {{ $taskName->title }}
                </div>
            </div>
            <hr class="my-3">
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label" for="bank_details" class="form-label">{{ __('Date : ') }}</label><br>
                </div>
                <div class="col-md-6">
                    {{ $expenseDetails->date }}
                </div>
            </div>
            <hr class="my-3">
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label" for="bank_details" class="form-label">{{ __('Amount : ') }}</label><br>
                </div>
                <div class="col-md-6">
                    {{ $expenseDetails->amount }}
                </div>
            </div>
            <hr class="my-3">
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label" for="bank_details"
                        class="form-label">{{ __('Description : ') }}</label><br>
                </div>
                <div class="col-md-6">
                    {{ $expenseDetails->description }}
                </div>
            </div>
            <hr class="my-3">
            <div class="row">
                <div class="col-md-6">
                    <label class="form-label" for="bank_details"
                        class="form-label">{{ __('Attachment : ') }}</label><br>
                </div>
                <div class="col-md-6">
                    <img class="img-fluid" src="{{ asset($expenseAttachment . $expenseDetails->image) }}"
                        alt="" height="100" width="100">
                </div>
            </div>
        </div>
        <hr class="my-3">
    </div>


    {{-- <script>
        (function() {
            const d_week = new Datepicker(document.querySelector('.datepicker2'), {
                buttonClass: 'btn',
                todayBtn: true,
                clearBtn: true,
                format: 'yyyy-mm-dd',
            });
        })();
    </script>

    <script>
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
