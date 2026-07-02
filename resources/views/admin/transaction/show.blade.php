@extends('admin.layouts.main')

@section('title')
    {{ __('Transaction Details') }}
@endsection

@section('pre-title')
    {{ __('Transaction Details') }}
@endsection

@section('page-title')
    <div class="row g-2 align-items-center">
        <div class="col">
            <div class="page-pretitle">
                <a href="{{ url('admin/dashboard') }}">{{ __('HOME') }}/</a>
                <a href="{{ route('admin.transactions.index') }}">{{ __('Transactions') }}/</a>
                @yield('pre-title')
            </div>
            <h2 class="page-title">
                @yield('title')
            </h2>
        </div>
        <div class="col-auto ms-auto">
            <div class="btn-list">
                <a href="{{ route('admin.transactions.index') }}" class="btn btn-primary d-none d-sm-inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="24"
                        height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                        <path d="M5 12l14 0"></path>
                        <path d="M5 12l6 6"></path>
                        <path d="M5 12l6 -6"></path>
                    </svg>
                    {{ __('Back to Transactions') }}
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
    @can('show-transaction')
        <div class="row row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Transaction #') }}{{ $transaction->id }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="datagrid">
                            <div class="datagrid-item">
                                <div class="datagrid-title">{{ __('Transaction ID') }}</div>
                                <div class="datagrid-content">{{ $transaction->id }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">{{ __('External Transaction ID') }}</div>
                                <div class="datagrid-content">{{ $transaction->transaction_id }}</div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">{{ __('Payment Gateway') }}</div>
                                <div class="datagrid-content">
                                    <span class="badge bg-{{ $transaction->payment_gateway === 'stripe' }}">
                                        {{ ucfirst($transaction->payment_gateway) }}
                                    </span>
                                </div>
                            </div>
                            <div class="datagrid-item">
                                <div class="datagrid-title">{{ __('Date') }}</div>
                                <div class="datagrid-content">{{ $transaction->created_at->format('d M, Y H:i:s') }}</div>
                            </div>

                            <div class="datagrid-item">
                                <div class="datagrid-title">{{ __('User') }}</div>
                                <div class="datagrid-content">
                                    @if ($transaction->user)
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-sm me-2"
                                                style="background-image: url({{ auth()->user()->profile ?? asset('assets/images/faces/2.jpg') }})"></span>
                                            {{ $transaction->user->name }}
                                        </div>
                                    @else
                                        {{ __('User details not available....') }}
                                    @endif
                                </div>
                            </div>

                            <div class="datagrid-item">
                                <div class="datagrid-title">{{ __('Email') }}</div>
                                <div class="datagrid-content">
                                    @if ($transaction->user)
                                        {{ $transaction->user->email }}
                                    @else
                                        {{ __('N/A') }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 mt-3">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ __('Subscription Details') }}</h3>
                    </div>
                    <div class="card-body">
                        @if ($userSubscriptions && count($userSubscriptions) > 0)
                            @foreach ($userSubscriptions as $subscription)
                                <div class="datagrid mb-4">
                                    <div class="datagrid-item">
                                        <div class="datagrid-title">{{ __('Plan') }}</div>
                                        <div class="datagrid-content">
                                            {{ $subscription->plan ? $subscription->plan->name : __('Plan details not available') }}
                                        </div>
                                    </div>
                                    <div class="datagrid-item">
                                        <div class="datagrid-title">{{ __('Duration') }}</div>
                                        <div class="datagrid-content">
                                            {{ $subscription->duration }} {{ __('Month') }}
                                        </div>
                                    </div>
                                    <div class="datagrid-item">
                                        <div class="datagrid-title">{{ __('Status') }}</div>
                                        <div class="datagrid-content">
                                            <span
                                                class="badge text-bg-{{ $subscription->status == 'active'
                                                    ? 'success'
                                                    : ($subscription->status == 'pending'
                                                        ? 'primary'
                                                        : ($subscription->status == 'upcoming'
                                                            ? 'info'
                                                            : 'danger')) }}">
                                                {{ ucfirst($subscription->status) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="datagrid-item">
                                        <div class="datagrid-title">{{ __('Start Date') }}</div>
                                        <div class="datagrid-content">
                                            {{ $subscription->start_date ? $subscription->start_date->format('d M, Y') : __('N/A') }}
                                        </div>
                                    </div>
                                    <div class="datagrid-item">
                                        <div class="datagrid-title">{{ __('End Date') }}</div>
                                        <div class="datagrid-content">
                                            {{ $subscription->end_date ? $subscription->end_date->format('d M, Y') : __('N/A') }}
                                        </div>
                                    </div>
                                    <div class="datagrid-item">
                                        <div class="datagrid-title">{{ __('Article Count') }}</div>
                                        <div class="datagrid-content">
                                            {{ $subscription->article_count ?? 0 }}
                                        </div>
                                    </div>
                                    <div class="datagrid-item">
                                        <div class="datagrid-title">{{ __('Story Count') }}</div>
                                        <div class="datagrid-content">
                                            {{ $subscription->story_count ?? 0 }}
                                        </div>
                                    </div>
                                    <div class="datagrid-item">
                                        <div class="datagrid-title">{{ __('Story Count') }}</div>
                                        <div class="datagrid-content">
                                            {{ $subscription->e_paper_count ?? 0 }}
                                        </div>
                                    </div>
                                </div>
                                @if (!$loop->last)
                                    <hr>
                                @endif
                            @endforeach
                        @else
                            <div>{{ __('No subscriptions found for this transaction.') }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endcan
@endsection
