@extends('admin.layouts.main')

@section('title')
    {{ $title }}
@endsection

@section('pre-title')
    {{ $title }}
@endsection

@section('page-title')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ url('admin/dashboard') }}">{{ __('page.HOME') }}/</a>
                @yield('pre-title')
            </div>
            <h2 class="page-title">
                @yield('title')
            </h2>
        </div>
    </div>
@endsection

@section('content')
    <section class="section">
        @can('list-transaction')
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-bordered text-nowrap " id="transaction-list"
                                data-url="{{ route('admin.transactions.index') }}">
                                <thead>
                                    <tr>
                                        <th class="wd-15p border-bottom-0">{{ __('global.ID') }}</th>
                                        <th class="wd-15p border-bottom-0">{{ __('global.USER') }}</th>
                                        <th class="wd-20p border-bottom-0">{{ __('global.TRANSACTION_ID') }}</th>
                                        <th class="wd-15p border-bottom-0">{{ __('global.PAYMENT_GATEWAY') }}</th>
                                        <th class="wd-15p border-bottom-0">{{ __('global.DATE') }}</th>
                                        <th class="wd-15p border-bottom-0">{{ __('global.ACTIONS') }}</th>
                                    </tr>
                                </thead>
                                @forelse($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->id }}</td>
                                        <td>
                                            @if ($transaction->user)
                                                {{ $transaction->user->name }}
                                            @else
                                                {{ __('global.NO_USER') }}
                                            @endif
                                        </td>
                                        <td>{{ $transaction->transaction_id }}</td>
                                        <td>
                                            <span class="badge bg-{{ $transaction->payment_gateway === 'stripe' }}">
                                                {{ ucfirst($transaction->payment_gateway) }}
                                            </span>
                                        </td>
                                        <td>{{ $transaction->created_at->format('d M, Y') }}</td>
                                        <td>
                                            @can('show-transaction')
                                                <a href="{{ route('admin.transactions.show', $transaction->id) }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="currentColor"
                                                        class="icon icon-tabler icons-tabler-filled icon-tabler-eye">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path
                                                            d="M12 4c4.29 0 7.863 2.429 10.665 7.154l.22 .379l.045 .1l.03 .083l.014 .055l.014 .082l.011 .1v.11l-.014 .111a.992 .992 0 0 1 -.026 .11l-.039 .108l-.036 .075l-.016 .03c-2.764 4.836 -6.3 7.38 -10.555 7.499l-.313 .004c-4.396 0 -8.037 -2.549 -10.868 -7.504a1 1 0 0 1 0 -.992c2.831 -4.955 6.472 -7.504 10.868 -7.504zm0 5a3 3 0 1 0 0 6a3 3 0 0 0 0 -6z" />
                                                    </svg>
                                                </a>
                                            @else
                                                <span class='badge bg-primary text-white m-1'>No permission for View Details.</span>
                                            @endcan
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">{{ __('global.NO_TRANSACTION_FOUND') }}</td>
                                    </tr>
                                @endforelse
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="col-12 text-center py-5">
                <h1 class="display-1 fw-bold text-danger">403</h1>
                <h1 class="fw-bold mb-0 text-danger">Access Denied</h1>
                <div class="d-flex justify-content-center mb-0">
                    <div class="col-6 col-md-8 col-lg-4">
                        <img src="{{ asset('assets/images/access_Denied/no permission.png') }}" alt="Access Denied">
                    </div>
                </div>

                <div class="d-inline-block">
                    <h3 class="text-danger mb-0">You do not have permission to view the list of Transaction.
                    </h3>
                </div>
            </div>
        @endcan
    </section>
@endsection
