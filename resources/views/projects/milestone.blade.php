@if ($project && $currentWorkspace)
    <form method="post" class="needs-validation" novalidate
        action="@auth('web'){{ route('projects.milestone.store', [$currentWorkspace->slug, $project->id]) }}@elseauth{{ route('client.projects.milestone.store', [$currentWorkspace->slug, $project->id]) }}@endauth">
        @csrf
        <div class="modal-body">
            <div class="row">
                @if ($currentWorkspace->is_chagpt_enable())
                    <div class="text-end col-12">
                        <a href="#" data-size="lg" data-ajax-popup-over="true" class="btn btn-sm btn-primary"
                            data-url="{{ route('generate', ['milestone']) }}" data-bs-toggle="tooltip"
                            data-bs-placement="top" title="{{ __('Generate with AI') }}"
                            data-title="{{ __('Generate Milestone Title & Summary') }}">
                            <i class="fas fa-robot px-1"></i>{{ __('Generate with AI') }}
                        </a>
                    </div>
                @endif
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="milestone-title"
                            class="col-form-label">{{ __('Milestone Title') }}</label><x-required></x-required>
                        <input type="text" class="form-control form-control-light" id="milestone-title"
                            placeholder="{{ __('Enter Title') }}" name="title" required>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-group">
                        <label for="milestone-status"
                            class="col-form-label">{{ __('Status') }}</label><x-required></x-required>
                        <select class="form-control" name="status" id="milestone-status" required>
                            <option value="incomplete">{{ __('Incomplete') }}</option>
                            <option value="complete">{{ __('Complete') }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="milestone-title"
                    class="col-form-label">{{ __('Milestone Cost') }}</label><x-required></x-required>
                <div class="form-icon-user">
                    <span
                        class="currency-icon bg-primary">{{ !empty($currentWorkspace->currency) ? $currentWorkspace->currency : '$' }}</span>
                    <input type="number" class="form-control currency_input form-control-light" id="milestone-title"
                        placeholder="{{ __('Enter Cost') }}" min="0" name="cost" value="0" required>
                </div>
            </div>
            <div class="form-group">
                <label for="task-summary" class="col-form-label">{{ __('Summary') }}</label><x-required></x-required>
                <textarea class="form-control form-control-light" id="task-summary" rows="3" name="summary" required></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <input type="button" value="{{ __('Cancel') }}" class="btn  btn-secondary" data-bs-dismiss="modal">
            <input type="submit" value="{{ __('Create') }}" class="btn  btn-primary">
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
