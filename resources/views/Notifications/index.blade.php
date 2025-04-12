@extends('layouts.admin')

@section('page-title')
    {{ __(' Notification Templates') }}
@endsection

@section('links')
    @if (\Auth::guard('client')->check())
        <li class="breadcrumb-item"><a href="{{ route('client.home') }}">{{ __('Home') }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    @endif
    <li class="breadcrumb-item">{{ __('Notification Templates') }}</li>
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
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body ">
                    <div class="table-responsive">
                        <table id="selection-datatable" class="table">
                            <thead>
                                <tr>
                                    <th scope="col" class="sort" data-sort="name"> {{ __('Name') }}</th>
                                    <th class="text-end">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($notification_templates as $notification_template)
                                    <tr>
                                        <td>{{ $notification_template->name }}</td>
                                        <td>
                                            <div class="text-end">
                                                <div class="action-btn ms-2">
                                                    <a href="{{ route('notification-templates.show', [$currentWorkspace->slug,$notification_template->id, \Auth::user()->lang]) }}"
                                                        class="mx-3 btn btn-sm align-items-center  bg-warning"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-original-title="{{ __('View') }}">
                                                        <i class="ti ti-eye text-white"></i>
                                                    </a>
                                                </div>
                                            </div>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- <h5 class= "font-weight-bold pb-3">{{ __('Placeholders') }}</h5>
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
                    {{ Form::close() }} --}}
                </div>
            </div>
        </div>
    </div>
@endsection
