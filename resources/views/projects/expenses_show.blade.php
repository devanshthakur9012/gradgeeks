@extends('layouts.admin')

@php
    $permissions = Auth::user()->getPermission($project->id);
    $client_keyword = Auth::user()->getGuard() == 'client' ? 'client.' : '';
    $logo = \App\Models\Utility::get_file('users-avatar/');
    $attachment = \App\Models\Utility::get_file('expense/');
@endphp

@section('page-title')
    {{ __('Expenses') }}
@endsection

@section('links')
    @if (\Auth::guard('client')->check())
        <li class="breadcrumb-item"><a href="{{ route('client.home') }}">{{ __('Home') }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    @endif
    @if (\Auth::guard('client')->check())
        <li class="breadcrumb-item"><a
                href="{{ route('client.projects.index', $currentWorkspace->slug) }}">{{ __('Project') }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('projects.index', $currentWorkspace->slug) }}">{{ __('Project') }}</a>
        </li>
    @endif
    <li class="breadcrumb-item"><a
            href="{{ route($client_keyword . 'projects.show', [$currentWorkspace->slug, $project->id]) }}">{{ __('Project Details') }}</a>
    </li>
    <li class="breadcrumb-item">{{ __('Expenses') }}</li>
@endsection

@section('action-button')
    @if (
        (isset($permissions) && in_array('create expenses', $permissions)) ||
            ($currentWorkspace && $currentWorkspace->permission == 'Owner'))
        <a href="#" class="btn btn-sm btn-primary " data-ajax-popup="true" data-size="lg"
            data-title="{{ __('Create Expense') }}" data-toggle="tooltip" title="{{ __('Create') }}"
            data-url="{{ route($client_keyword . 'projects.expenses.report.create', [$currentWorkspace->slug, $project->id]) }}">
            <i class="ti ti-plus"></i>
        </a>
    @endif
    <a href="{{ route($client_keyword . 'projects.show', [$currentWorkspace->slug, $project->id]) }}"
        class="btn-submit btn btn-sm btn-primary" data-toggle="tooltip" title="{{ __('Back') }}">
        <i class="ti ti-arrow-back-up"></i>
    </a>
@endsection

@section('content')
    <div class="row row-gap-2 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-hover mb-0 animated" id="selection-datatable">
                            <thead>
                                <th>{{ __('Expense Name') }}</th>
                                <th>{{ __('Task Name') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Attchment') }}</th>
                                @if (
                                    (isset($permissions) && in_array('edit expenses', $permissions)) ||
                                        ($currentWorkspace && $currentWorkspace->permission == 'Owner') ||
                                        in_array('delete expenses', $permissions))
                                    <th>{{ __('Action') }}</th>
                                @endif
                            </thead>
                            <tbody>
                                @foreach ($expenses as $expense)
                                    <tr>
                                        <td>{{ $expense->title }}</td>
                                        <td>
                                            @foreach ($expense->taskExpenses as $task)
                                                {{ $task->title }}
                                            @endforeach
                                        </td>
                                        <td>{{ App\Models\Utility::dateFormat($expense->date) }}</td>
                                        <td>{{ $expense->amount }}</td>
                                        <td>
                                            <a href="{{ $attachment . $expense->image }}" class="btn-primary btn btn-sm"
                                                download data-toggle="tooltip" title="{{ __('Download') }}">
                                                <i class="ti ti-download"></i>
                                            </a>
                                        </td>
                                        <td class="text-right">
                                            <a href="#" class="btn-warning  btn btn-sm me-1"
                                                data-url="{{ route($client_keyword . 'projects.expense.report.view', [$currentWorkspace->slug, $expense->project_id, $expense->id]) }}"
                                                data-size="lg" data-toggle="tooltip" title="{{ __('Show') }}"
                                                data-ajax-popup="true" data-title="{{ __('View Expense') }}">
                                                <i class="ti ti-eye"></i>
                                            </a>
                                            @if (
                                                (isset($permissions) && in_array('edit expenses', $permissions)) ||
                                                    ($currentWorkspace && $currentWorkspace->permission == 'Owner') ||
                                                    in_array('delete expenses', $permissions))
                                                @if (
                                                    (isset($permissions) && in_array('edit expenses', $permissions)) ||
                                                        ($currentWorkspace && $currentWorkspace->permission == 'Owner'))
                                                    <a href="#" class="btn-info  btn btn-sm me-1"
                                                        data-url="{{ route($client_keyword . 'projects.expense.report.edit', [$currentWorkspace->slug, $expense->project_id, $expense->id]) }}"
                                                        data-size="lg" data-toggle="tooltip" title="{{ __('Edit') }}"
                                                        data-ajax-popup="true" data-title="{{ __('Update Expense') }}">
                                                        <i class="ti ti-pencil"></i>
                                                    </a>
                                                @endif

                                                @if (
                                                    (isset($permissions) && in_array('delete expenses', $permissions)) ||
                                                        ($currentWorkspace && $currentWorkspace->permission == 'Owner'))
                                                    <a href="#" class="btn-danger  btn btn-sm  bs-pass-para"
                                                        data-confirm="{{ __('Are You Sure?') }}"
                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="delete-form-{{ $expense->id }}"
                                                        data-toggle="tooltip" title="{{ __('Delete') }}">
                                                        <i class="ti ti-trash"></i>
                                                    </a>
                                                @endif
                                                {!! Form::open([
                                                    'method' => 'DELETE',
                                                    'route' => [
                                                        $client_keyword . 'projects.expense.report.destroy',
                                                        [$currentWorkspace->slug, $expense->project_id, $expense->id],
                                                    ],
                                                    'id' => 'delete-form-' . $expense->id,
                                                ]) !!}
                                                {!! Form::close() !!}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
