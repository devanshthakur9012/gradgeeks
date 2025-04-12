@extends('layouts.admin')

@section('page-title')
    {{ __('Contract Type') }}
@endsection

@section('links')
    @if (\Auth::guard('client')->check())
        <li class="breadcrumb-item"><a href="{{ route('client.home') }}">{{ __('Home') }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    @endif
    <li class="breadcrumb-item"> {{ __('Contract Type') }}</li>
@endsection

@section('action-button')
    @auth('web')
        @if ($currentWorkspace->creater->id == Auth::user()->id)
            <a href="#" class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="md"
                data-title="{{ __('Create Contract Type') }}" data-toggle="tooltip" title="{{ __('Create') }}"
                data-url="{{ route('contract_type.create', !empty($currentWorkspace) ? $currentWorkspace->slug : 0) }}">
                <i class="ti ti-plus"></i>
            </a>
        @endif
    @endauth
@endsection

@section('content')
    <div class="row row-gap-2 mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-hover mb-0 animated" id="selection-datatable">
                            <thead>
                                <th data-sortable="" style="width: 82.97%;">{{ __('Contract Type') }}</th>
                                @auth('web')
                                    <th width="250px" data-sortable="" style="width: 17.03%;">{{ __('Action') }}</th>
                                @endauth
                            </thead>
                            <tbody>
                                @foreach ($contractTypes as $contractType)
                                    <tr>
                                        <td>
                                            {{ $contractType->name }}
                                        </td>
                                        @auth('web')
                                            <td class="text-right">
                                                <a href="#" class="btn-info  btn btn-sm me-1"
                                                    data-url="{{ route('contract_type.edit', [!empty($currentWorkspace) ? $currentWorkspace->slug : 0, $contractType->id]) }}"
                                                    data-size="md" data-toggle="tooltip" title="{{ __('Edit') }}"
                                                    data-ajax-popup="true" data-title="{{ __('Update Contract Type') }}">
                                                    <i class="ti ti-pencil"></i>
                                                </a>
                                                <a href="#" class="btn-danger  btn btn-sm bs-pass-para"
                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                    data-confirm-yes="delete-form-{{ $contractType->id }}"
                                                    data-toggle="tooltip" title="{{ __('Delete') }}">
                                                    <i class="ti ti-trash"></i>
                                                </a>
                                                {!! Form::open([
                                                    'method' => 'DELETE',
                                                    'route' => ['contract_type.destroy', [!empty($currentWorkspace) ? $currentWorkspace->slug : 0, $contractType->id]],
                                                    'id' => 'delete-form-' . $contractType->id,
                                                ]) !!}
                                                {!! Form::close() !!}
                                            </td>
                                        @endauth
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
