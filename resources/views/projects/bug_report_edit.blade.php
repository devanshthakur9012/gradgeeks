@if ($project && $currentWorkspace && $bug)
    <form method="post" class="needs-validation" novalidate
        action="@auth('web'){{ route('projects.bug.report.update', [$currentWorkspace->slug, $project->id, $bug->id]) }}@elseauth{{ route('client.projects.bug.report.update', [$currentWorkspace->slug, $project->id, $bug->id]) }}@endauth">
        @csrf
        <div class="modal-body">
            <div class="row">
                @if ($currentWorkspace->is_chagpt_enable())
                    <div class="text-end col-12">
                        <a href="#" data-size="lg" data-ajax-popup-over="true" class="btn btn-sm btn-primary"
                            data-url="{{ route('generate', ['bug']) }}" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="{{ __('Generate with AI') }}"
                            data-title="{{ __('Generate Bug Title & Description') }}">
                            <i class="fas fa-robot px-1"></i>{{ __('Generate with AI') }}
                        </a>
                    </div>
                @endif
                <div class="form-group col-md-8">
                    <label class="col-form-label">{{ __('Title') }}</label><x-required></x-required>
                    <input type="text" class="form-control" id="task-title" placeholder="{{ __('Enter Title') }}"
                        name="title" value="{{ $bug->title }}" required>
                </div>

                <div class="form-group col-md-4">
                    <label for="task-priority" class="col-form-label">{{ __('Priority') }}</label><x-required></x-required>
                    <select class="form-control" name="priority" id="task-priority" required>
                        <option value="Low" @if ($bug->priority == 'Low') selected @endif>{{ __('Low') }}
                        </option>
                        <option value="Medium" @if ($bug->priority == 'Medium') selected @endif>{{ __('Medium') }}
                        </option>
                        <option value="High" @if ($bug->priority == 'High') selected @endif>{{ __('High') }}
                        </option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="assign_to" class="col-form-label">{{ __('Assign To') }}</label><x-required></x-required>
                    <select class="form-control" id="assign_to" name="assign_to" required>
                        @foreach ($users as $u)
                            <option @if ($bug->assign_to == $u->id) selected @endif value="{{ $u->id }}">
                                {{ $u->name }} - {{ $u->email }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="status" class="col-form-label">{{ __('Status') }}</label><x-required></x-required>
                    <select class="form-control" id="status" name="status" required>
                        @foreach ($arrStatus as $id => $status)
                            <option @if ($bug->status == $id) selected @endif value="{{ $id }}">
                                {{ __($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-12 mb-0">
                    <label for="task-description" class="col-form-label">{{ __('Description') }}</label><x-required></x-required>
                    <textarea class="form-control" id="task-description" rows="3" name="description" required>{{ $bug->description }}</textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <input type="button" value="{{ __('Cancel') }}" class="btn  btn-secondary" data-bs-dismiss="modal">
            <input type="submit" value="{{ __('Update') }}" class="btn  btn-primary">
        </div>

    </form>
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
