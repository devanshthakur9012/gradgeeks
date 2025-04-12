@extends('layouts.admin')

@php
    $client_keyword = Auth::user()->getGuard() == 'client' ? 'client.' : '';
@endphp

@section('page-title')
    {{ __('Contracts') }}
@endsection

@section('links')
    @if (\Auth::guard('client')->check())
        <li class="breadcrumb-item"><a href="{{ route('client.home') }}">{{ __('Home') }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    @endif
    <li class="breadcrumb-item"> {{ __('Contracts') }}</li>
@endsection

@section('action-button')
    @auth('web')
        @if ($currentWorkspace->creater->id == Auth::user()->id)
            <a href="#" class="btn btn-sm  btn-primary" data-ajax-popup="true" data-size="lg"
                data-title="{{ __('Create Contract ') }}" data-toggle="tooltip" title="{{ __('Create') }}"
                data-url="{{ route('contracts.create', $currentWorkspace->slug) }}">
                <i class="ti ti-plus"></i>
            </a>
        @endif
    @endauth
@endsection

@section('content')
    <div class="row row-gap-2 mb-4">
        <div class="col-xl-3 col-sm-6">
            <div class="card comp-card">
                <div class="card-body" style="min-height: 143px;">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-b-20">{{ __('Total Contracts') }}</h6>
                            <h3 class="text-primary">{{ $cnt_contract['total'] }}</h3>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-handshake bg-success text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card comp-card">
                <div class="card-body" style="min-height: 143px;">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-b-20">{{ __('This Month Total Contracts') }}</h6>
                            <h3 class="text-info">{{ $cnt_contract['this_month'] }}</h3>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-handshake bg-info text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card comp-card">
                <div class="card-body" style="min-height: 143px;">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-b-20">{{ __('This Week Total Contracts') }}</h6>
                            <h3 class="text-warning">{{ $cnt_contract['this_week'] }}</h3>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-handshake bg-warning text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card comp-card">
                <div class="card-body" style="min-height: 143px;">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="m-b-20">{{ __('Last 30 Days Total Contracts') }}</h6>
                            <h3 class="text-danger">{{ $cnt_contract['last_30days'] }}</h3>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-handshake bg-danger text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-hover mb-0 animated" id="selection-datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('Contracts') }}</th>
                                    @if (Auth::user()->getGuard() != 'client')
                                        <th>{{ __('Client') }}</th>
                                    @endif
                                    <th>{{ __('Project') }}</th>
                                    <th>{{ __('Subject') }}</th>
                                    <th>{{ __('Value') }}</th>
                                    <th>{{ __('Type') }}</th>
                                    <th>{{ __('Start Date') }}</th>
                                    <th>{{ __('End Date') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($contracts as $contract)
                                    <tr>
                                        <td class="Id">
                                            <a href="@auth('web'){{ route('contracts.show', [$currentWorkspace->slug, $contract->id]) }}@elseauth{{ route('client.contracts.show', [$currentWorkspace->slug, $contract->id]) }}@endauth"
                                                class="btn btn-outline-primary">{{ App\Models\Utility::contractNumberFormat($contract->id) }}
                                            </a>
                                        </td>
                                        @if (Auth::user()->getGuard() != 'client')
                                            <td>{{ !empty($contract->clients) ? $contract->clients->name : '' }}</td>
                                        @endif
                                        <td>{{ !empty($contract->projects) ? $contract->projects->name : '' }}</td>
                                        <td>{{ $contract->subject }}</td>
                                        <td>{{ $currentWorkspace->priceFormat($contract->value) }}</td>
                                        <td>{{ $contract->contract_type->name }}</td>
                                        <td>{{ App\Models\Utility::dateFormat($contract->start_date) }}</td>
                                        <td>{{ App\Models\Utility::dateFormat($contract->end_date) }}</td>
                                        <td>
                                            @if ($contract->status == 'pending')
                                                <span class="badge bg-warning p-2 px-3">{{ __('Pending') }}</span>
                                            @elseif($contract->status == 'accept')
                                                <span class="badge bg-success p-2 px-3">{{ __('Accept') }}</span>
                                            @else
                                                <span class="badge bg-danger p-2 px-3">{{ __('Decline') }}</span>
                                            @endif
                                        </td>
                                        <td class="Action">
                                            <span>
                                                @if ($currentWorkspace->permission == 'Owner' || Auth::user()->getGuard() == 'client')
                                                    <a href="@auth('web'){{ route('contracts.show', [$currentWorkspace->slug, $contract->id]) }}@elseauth{{ route('client.contracts.show', [$currentWorkspace->slug, $contract->id]) }}@endauth"
                                                        class="btn btn-sm bg-warning me-1" title=""
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        data-bs-original-title="Show" aria-label="Detail">
                                                        <span class="text-white"><i class="ti ti-eye"></i></span>
                                                    </a>
                                                @endif
                                                @auth('web')
                                                    @if ($contract->status == 'accept')
                                                        <a href="#" data-size="lg" class="btn btn-sm bg-primary me-1"
                                                            data-url="{{ route('contracts.copy', [$currentWorkspace->slug, $contract->id]) }}"data-ajax-popup="true"
                                                            data-title="{{ __('Duplicate Contract') }}"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="{{ __('Duplicate') }}">
                                                            <i class="ti ti-copy text-white"></i>
                                                        </a>
                                                    @endif
                                                    <a href="#" data-size="lg" class="btn btn-sm bg-info me-1"
                                                        data-url="{{ route('contracts.edit', [$currentWorkspace->slug, $contract->id]) }}"
                                                        data-ajax-popup="true" data-title="{{ __('Update Contract') }}"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="{{ __('Edit') }}">
                                                        <i class="ti ti-pencil text-white"></i>
                                                    </a>
                                                    <a href="#" class="btn btn-sm bg-danger me-1 bs-pass-para"
                                                        data-confirm="{{ __('Are You Sure?') }}"
                                                        data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                        data-confirm-yes="delete-form-{{ $contract->id }}"
                                                        title="{{ __('Delete') }}" data-bs-toggle="tooltip"
                                                        data-bs-placement="top">
                                                        <span class="text-white"><i class="ti ti-trash"></i></span>
                                                    </a>
                                                    {!! Form::open([
                                                        'method' => 'DELETE',
                                                        'route' => ['contracts.destroy', [$currentWorkspace->slug, $contract->id]],
                                                        'id' => 'delete-form-' . $contract->id,
                                                    ]) !!}
                                                    {!! Form::close() !!}
                                                @endauth
                                            </span>
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
@push('scripts')
    <script type="text/javascript">
        $(document).on('change', '.client_id', function() {
            getUsers($(this).val());
        });

        function getUsers(id) {

            $("#project-div").html('');
            $('#project-div').append('<select class="form-control" id="project" name="project" ></select>');
            $.get("{{ url('get-projects') }}/" + id, function(data, status) {
                var list = '';
                $('#project').empty();
                if (data.length > 0) {
                    list += "<option value=''> {{ __('Select Projects') }} </option>";
                } else {
                    list += "<option value=''> {{ __('No Projects') }} </option>";
                }
                $.each(data, function(i, item) {
                    list += "<option value='" + item.id + "'>" + item.name + "</option>"
                });
                var select = '<select class="form-control" id="project" name="project" >' + list + '</select>';
                $('.project-div').html(select);
                select2();
            });
        }
    </script>
@endpush
