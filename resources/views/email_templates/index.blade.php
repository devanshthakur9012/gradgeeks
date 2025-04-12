@extends('layouts.admin')

@section('page-title')
    {{ __('Email Templates') }}
@endsection

@section('links')
    @if (\Auth::guard('client')->check())
        <li class="breadcrumb-item"><a href="{{ route('client.home') }}">{{ __('Home') }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    @endif
    <li class="breadcrumb-item"> {{ __('Email Templates') }}</li>
@endsection


@section('content')
    <div class="row row-gap-2 mb-4">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="selection-datatable" class="table">
                            <thead>
                                <tr>
                                    <th scope="col" class="sort" data-sort="name"> {{ __('Name') }}</th>
                                    <th class="text-end">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($EmailTemplates as $EmailTemplate)
                                    <tr>
                                        <td>{{ $EmailTemplate->name }}</td>
                                        <td>
                                            @if (\Auth::user()->type == 'admin')
                                                <div class="text-end">
                                                    <div class="action-btn ms-2">
                                                        <a href="{{ route('email_template.show', [$EmailTemplate->id, \Auth::user()->lang]) }}"
                                                            class="mx-3 btn btn-sm align-items-center  bg-warning"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-original-title="{{ __('View') }}">
                                                            <i class="ti ti-eye text-white"></i>
                                                        </a>
                                                    </div>
                                                </div>
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
