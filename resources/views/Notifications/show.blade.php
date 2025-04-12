@extends('layouts.admin')

@section('page-title')
    {{ __(' Notification Templates') }}
@endsection

@section('action-button')
    @if ($currentWorkspace->is_chagpt_enable())
        <a href="#" data-size="lg" data-ajax-popup-over="true" class="btn btn-sm btn-primary"
            data-url="{{ route('generate', ['notification template', $notification_template->id]) }}" data-bs-toggle="tooltip"
            data-bs-placement="top" title="{{ __('Generate with AI') }}"
            data-title="{{ __('Generate Notification Message') }}">
            <i class="fas fa-robot px-1"></i>{{ __('Generate with AI') }}
        </a>
    @endif
@endsection

@section('links')
    @if (\Auth::guard('client')->check())
        <li class="breadcrumb-item"><a href="{{ route('client.home') }}">{{ __('Home') }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    @endif
    <li class="breadcrumb-item"> <a
            href="{{ route('notification-templates.index', $currentWorkspace->slug) }}">{{ __('Notification Templates') }}</a>
    </li>
    <li class="breadcrumb-item">{{ $notification_template->name }}</li>
@endsection

@push('css-page')
    <link rel="stylesheet" href="{{ asset('assets/custom/libs/summernote/summernote-bs4.css') }}">
@endpush

@push('scripts')
    <script src="{{ asset('assets/custom/libs/summernote/summernote-bs4.js') }}"></script>
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
            <div class="card mb-0 h-100">
                <div class="card-header">
                    <h5>{{ __('Variables') }}</h5>
                </div>
                <div class="card-body">
                    <div class="row text-xs">
                        @php
                            $variables = json_decode($curr_noti_tempLang->variables);
                        @endphp
                        @if (!empty($variables) > 0)
                            @foreach ($variables as $key => $var)
                                <div class="col-6 pb-1">
                                    <p class="mb-1">{{ __($key) }} : <span
                                            class="pull-right text-primary">{{ '{' . $var . '}' }}</span></p>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12">
            <div class="row">
                <div class="col-sm-3 col-md-3 col-lg-3 col-xl-3">
                    <div class="card sticky-top language-sidebar mb-0">
                        <div class="list-group list-group-flush" id="useradd-sidenav">
                            @foreach ($languages as $langCode => $lang)
                                <a href="{{ route('notification-templates.show', [$currentWorkspace->slug, $notification_template->id, $langCode]) }}"
                                    class="list-group-item list-group-item-action border-0 {{ $curr_noti_tempLang->lang == $langCode ? 'active' : '' }}">{{ ucfirst(\App\Models\Utility::getlang_fullname($langCode)) }}</a>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="col-lg-9 col-md-9 col-sm-9">
                    <div class="card h-100 p-3">
                        {{ Form::model($curr_noti_tempLang, ['route' => ['notification-templates.update', [$currentWorkspace->slug, $curr_noti_tempLang->parent_id]], 'method' => 'PUT', 'class' => 'needs-validation', 'novalidate']) }}
                        <div class="form-group col-12">
                            {{ Form::label('name', __('Name'), ['class' => 'col-form-label text-dark']) }}
                            {{ Form::text('name', $notification_template->name, ['class' => 'form-control font-style', 'required' => 'required', 'disabled' => 'disabled']) }}
                        </div>
                        <div class="form-group col-12">
                            {{ Form::label('content', __('Email Message'), ['class' => 'col-form-label text-dark']) }}
                            {{ Form::textarea('content', $curr_noti_tempLang->content, ['class' => 'summernote-simple', 'id' => 'content', 'required' => 'required']) }}
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
        {{-- <div class="card">
                <div class="card-body ">
                    <h5 class= "font-weight-bold pb-3">{{ __('Placeholders') }}</h5>
                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card">
                            <div class="card-header card-body">
                                <div class="row text-xs">
                                    <h6 class="font-weight-bold mb-4">{{ __('Variables') }}</h6>
                                    @php
                                        $variables = json_decode($curr_noti_tempLang->variables);
                                    @endphp
                                    @if (!empty($variables) > 0)
                                        @foreach ($variables as $key => $var)
                                            <div class="col-6 pb-1">
                                                <p class="mb-1">{{ __($key) }} : <span
                                                        class="pull-right text-primary">{{ '{' . $var . '}' }}</span></p>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>

                            </div>
                        </div>
                    </div>
                    {{ Form::model($curr_noti_tempLang, ['route' => ['notification-templates.update', [$currentWorkspace->slug, $curr_noti_tempLang->parent_id]], 'method' => 'PUT', 'class' => 'needs-validation', 'novalidate']) }}
                    <div class="row">
                        <div class="form-group col-12">
                            {{ Form::label('content', __('Notification Message'), ['class' => 'form-label text-dark']) }}
                            {{ Form::textarea('content', $curr_noti_tempLang->content, ['class' => 'form-control summernote', 'id' => 'summernote', 'required' => 'required', 'rows' => '04', 'placeholder' => 'EX. Hello, {company_name}']) }}
                            <small>{{ __('A variable is to be used in such a way.') }} <span
                                    class="text-primary">{{ __('Ex. Hello, {company_name}') }}</span></small>
                        </div>
                    </div>
                    <hr>
                    <div class="col-md-12 text-end">
                        {{ Form::hidden('lang', null) }}
                        <input type="submit" value="{{ __('Save Changes') }}"
                            class="btn btn-print-invoice  btn-primary m-r-10">
                    </div>
                    {{ Form::close() }}
                </div>
            </div> --}}
    </div>
@endsection
