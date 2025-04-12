@extends('layouts.admin')

@section('page-title')
    {{ __('Email Templates') }}
@endsection

@section('action-button')
    <a href="#" data-size="lg" data-ajax-popup-over="true" class="btn btn-sm btn-primary"
        data-url="{{ route('generate', ['email template', $emailTemplate->id]) }}" data-bs-toggle="tooltip"
        data-bs-placement="top" title="{{ __('Generate with AI') }}" data-title="{{ __('Generate Subject & Email Message') }}">
        <i class="fas fa-robot px-1"></i>{{ __('Generate with AI') }}</a>
@endsection

@section('links')
    @if (\Auth::guard('client')->check())
        <li class="breadcrumb-item"><a href="{{ route('client.home') }}">{{ __('Home') }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    @endif
    <li class="breadcrumb-item"> <a href="{{ route('email_template.index') }}">{{ __('Email Templates') }}</a></li>
    <li class="breadcrumb-item">{{ $emailTemplate->name }}</li>
@endsection

@push('scripts')
    <script>
        $('.summernote').summernote({
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'italic', 'underline', 'strikethrough']],
                ['list', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'unlink']],
            ],
            height: 250,
        });
    </script>
@endpush

@section('content')
    <div class="row row-gap-2 mb-4">
        <div class="col-12">
            <div class="row">
                <div class="col-md-4 col-12">
                    <div class="card mb-0 h-100">
                        <div class="card-body">
                            {{ Form::model($emailTemplate, ['route' => ['email_template.update', $emailTemplate->id], 'method' => 'PUT']) }}
                            <div class="row">
                                <div class="form-group col-md-12">
                                    {{ Form::label('name', __('Name'), ['class' => 'col-form-label text-dark']) }}
                                    {{ Form::text('name', null, ['class' => 'form-control font-style', 'disabled' => 'disabled']) }}
                                </div>
                                <div class="form-group col-md-12">
                                    {{ Form::label('from', __('From'), ['class' => 'col-form-label text-dark']) }}
                                    {{ Form::text('from', null, ['class' => 'form-control font-style', 'required' => 'required', 'placeholder' => __('Enter From Name')]) }}
                                </div>
                                {{ Form::hidden('lang', $currEmailTempLang->lang, ['class' => '']) }}
                                <div class="col-12 text-end">
                                    <input type="submit" value="{{ __('Save') }}"
                                        class="btn btn-print-invoice  btn-primary m-r-10">
                                </div>
                            </div>
                            {{ Form::close() }}
                        </div>
                    </div>
                </div>
                <div class="col-md-8 col-12">
                    <div class="card mb-0 h-100">
                        <div class="card-header">
                            <h5>{{ __('Variables') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-xs">
                                @if ($emailTemplate->name == 'New Client')
                                    <div class="row">
                                        <p class="col-4">{{ __('App Name') }} : <span
                                                class="pull-end text-primary">{app_name}</span></p>
                                        <p class="col-4">{{ __('User Name') }} : <span
                                                class="pull-right text-primary">{user_name}</span></p>
                                        <p class="col-4">{{ __('App Url') }} : <span
                                                class="pull-right text-primary">{app_url}</span></p>
                                        <p class="col-4">{{ __('Email') }} : <span
                                                class="pull-right text-primary">{email}</span></p>
                                        <p class="col-4">{{ __('Password') }} : <span
                                                class="pull-right text-primary">{password}</span></p>
                                    </div>
                                @elseif($emailTemplate->name == 'User Invited')
                                    <div class="row">
                                        <p class="col-4">{{ __('App Name') }} : <span
                                                class="pull-end text-primary">{app_name}</span></p>
                                        <p class="col-4">{{ __('User Name') }} : <span
                                                class="pull-right text-primary">{user_name}</span></p>
                                        <p class="col-4">{{ __('App Url') }} : <span
                                                class="pull-right text-primary">{app_url}</span></p>
                                        <p class="col-4">{{ __('Workspace Name') }} : <span
                                                class="pull-right text-primary">{workspace_name}</span></p>
                                        <p class="col-4">{{ __('Owner Name') }} : <span
                                                class="pull-right text-primary">{owner_name}</span></p>
                                    </div>
                                @elseif($emailTemplate->name == 'Project Assigned')
                                    <div class="row">
                                        <p class="col-4">{{ __('App Name') }} : <span
                                                class="pull-end text-primary">{app_name}</span></p>
                                        <p class="col-4">{{ __('User Name') }} : <span
                                                class="pull-right text-primary">{user_name}</span></p>
                                        <p class="col-4">{{ __('App Url') }} : <span
                                                class="pull-right text-primary">{app_url}</span></p>
                                        <p class="col-4">{{ __('Project Name') }} : <span
                                                class="pull-right text-primary">{project_name}</span></p>
                                        <p class="col-4">{{ __('Project Status') }} : <span
                                                class="pull-right text-primary">{project_status}</span></p>
                                        <p class="col-4">{{ __('Owner Name') }} : <span
                                                class="pull-right text-primary">{owner_name}</span></p>
                                    </div>
                                @elseif($emailTemplate->name == 'Contract Shared')
                                    <div class="row">
                                        <p class="col-4">{{ __('Client Name') }} : <span
                                                class="pull-end text-primary">{client_name}</span></p>
                                        <p class="col-4">{{ __('Contract Subject') }} : <span
                                                class="pull-right text-primary">{contract_subject}</span></p>
                                        <p class="col-4">{{ __('Project Name') }} : <span
                                                class="pull-right text-primary">{project_name}</span></p>
                                        <p class="col-4">{{ __('Contract Type') }} : <span
                                                class="pull-right text-primary">{contract_type}</span></p>
                                        <p class="col-4">{{ __('Contract value') }} : <span
                                                class="pull-right text-primary">{value}</span></p>
                                        <p class="col-4">{{ __('Start Date') }} : <span
                                                class="pull-right text-primary">{start_date}</span></p>
                                        <p class="col-4">{{ __('End Date') }} : <span
                                                class="pull-right text-primary">{end_date}</span></p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="row">
                <div class="col-sm-3 col-md-3 col-lg-3 col-xl-3">
                    <div class="card sticky-top language-sidebar mb-0">
                        <div class="list-group list-group-flush" id="useradd-sidenav">
                            @foreach ($languages as $key => $lang)
                                <a class="list-group-item list-group-item-action border-0 {{ $currEmailTempLang->lang == $key ? 'active' : '' }}"
                                    href="{{ route('manage.email.language', [$emailTemplate->id, $key]) }}">
                                    {{ Str::ucfirst($lang) }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-lg-9 col-md-9 col-sm-9">
                    <div class="card h-100 p-3">
                        {{ Form::model($currEmailTempLang, ['route' => ['store.email.language', $currEmailTempLang->parent_id], 'method' => 'POST']) }}
                        <div class="form-group col-12">
                            {{ Form::label('subject', __('Subject'), ['class' => 'col-form-label text-dark']) }}
                            {{ Form::text('subject', null, ['class' => 'form-control font-style', 'required' => 'required']) }}
                        </div>
                        <div class="form-group col-12">
                            {{ Form::label('content', __('Email Message'), ['class' => 'col-form-label text-dark']) }}
                            {{ Form::textarea('content', $currEmailTempLang->content, ['class' => 'summernote-simple', 'id' => 'content', 'required' => 'required']) }}
                        </div>

                        <div class="col-md-12 text-end mb-3">
                            {{ Form::hidden('lang', null) }}
                            <input type="submit" value="{{ __('Save') }}"
                                class="btn btn-print-invoice  btn-primary m-r-10">
                        </div>
                        {{ Form::close() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
