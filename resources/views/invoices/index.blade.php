@extends('layouts.admin')

@section('page-title')
    {{ __('Invoices') }}
@endsection

@section('links')
    @if (\Auth::guard('client')->check())
        <li class="breadcrumb-item"><a href="{{ route('client.home') }}">{{ __('Home') }}</a></li>
    @else
        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('Home') }}</a></li>
    @endif
    <li class="breadcrumb-item"> {{ __('Invoice') }}</li>
@endsection

@push('css-page')
    <style type="text/css">
        .on_hover:hover {
            color: #fff;
        }

        .status_badge {
            min-width: 95px;
        }
    </style>
@endpush

@section('action-button')
    @auth('web')
        @if ($currentWorkspace->creater->id == Auth::user()->id)
            <a href="{{ route('invoice.export') }}" class="btn btn-sm btn-primary " data-toggle="tooltip"
                title="{{ __('Export') }}" data-bs-toggle="tooltip" data-bs-placement="top">
                <i class="ti ti-file-x"></i>
            </a>
            <a href="#" class="btn btn-sm btn-primary" data-ajax-popup="true" data-size="lg"
                data-title="{{ __('Create Invoice') }}" data-toggle="tooltip" data-bs-toggle="tooltip"
                title="{{ __('Create') }}" data-url="{{ route('invoices.create', $currentWorkspace->slug) }}">
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
                                <th>{{ __('Invoice') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Issue Date') }}</th>
                                <th>{{ __('Due Date') }}</th>
                                <th>{{ __('Amount') }}</th>
                                <th>{{ __('Status') }}</th>
                                @auth('web')
                                    <th>{{ __('Action') }}</th>
                                @endauth
                            </thead>
                            <tbody>
                                @foreach ($invoices as $key => $invoice)
                                    <tr>
                                        <td class="Id sorting_1">
                                            <a href="@auth('web'){{ route('invoices.show', [$currentWorkspace->slug, $invoice->id]) }}@elseauth{{ route('client.invoices.show', [$currentWorkspace->slug, $invoice->id]) }}@endauth"
                                                class="btn btn-outline-primary">
                                                {{ App\Models\Utility::invoiceNumberFormat($invoice->invoice_id) }}
                                            </a>
                                        </td>
                                        <td>{{ $invoice->project->name }}</td>
                                        <td>{{ App\Models\Utility::dateFormat($invoice->issue_date) }}</td>
                                        <td>{{ App\Models\Utility::dateFormat($invoice->due_date) }}</td>
                                        <td>{{ $currentWorkspace->priceFormat($invoice->getTotal()) }}</td>
                                        <td>
                                            @if ($invoice->status == 'sent')
                                                <span class="status_badge badge bg-info p-2 px-3">{{ __('Sent') }}</span>
                                            @elseif($invoice->status == 'paid')
                                                <span class="status_badge badge bg-success p-2 px-3">{{ __('Paid') }}</span>
                                            @elseif($invoice->status == 'partialy paid')
                                                <span class="status_badge badge bg-warning p-2 px-3">{{ __('Partialy Paid') }}</span>
                                            @elseif($invoice->status == 'canceled')
                                                <span class="status_badge badge bg-danger p-2 px-3">{{ __('Canceled') }}</span>
                                            @endif
                                        </td>
                                        @auth('web')
                                            <td class="text-right">
                                                <a href="@auth('web'){{ route('invoices.show', [$currentWorkspace->slug, $invoice->id]) }}@elseauth{{ route('client.invoices.show', [$currentWorkspace->slug, $invoice->id]) }}@endauth"
                                                    class="btn-warning me-1 btn btn-sm" data-toggle="tooltip"
                                                    title="{{ __('Show') }}">
                                                    <i class="ti ti-eye"></i>
                                                </a>
                                                <a href="#" class="btn-info me-1 btn btn-sm"
                                                    data-url="{{ route('invoices.edit', [$currentWorkspace->slug, $invoice->id]) }}"
                                                    data-size="lg" data-toggle="tooltip" title="{{ __('Edit') }}"
                                                    data-ajax-popup="true" data-title="{{ __('Update Invoice') }}">
                                                    <i class="ti ti-pencil"></i>
                                                </a>
                                                <a href="#" class="btn-danger  btn btn-sm bs-pass-para"
                                                    data-confirm="{{ __('Are You Sure?') }}"
                                                    data-text="{{ __('This action can not be undone. Do you want to continue?') }}"
                                                    data-confirm-yes="delete-form-{{ $invoice->id }}" data-toggle="tooltip"
                                                    title="{{ __('Delete') }}">
                                                    <i class="ti ti-trash"></i>
                                                </a>
                                                {!! Form::open([
                                                    'method' => 'DELETE',
                                                    'route' => ['invoices.destroy', [$currentWorkspace->slug, $invoice->id]],
                                                    'id' => 'delete-form-' . $invoice->id,
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
