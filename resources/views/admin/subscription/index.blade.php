@extends('admin.layouts.main')

@section('title')
    {{ __('page.SUBSCRIPTIONS') }}
@endsection

@section('pre-title')
    {{ __('page.SUBSCRIPTIONS') }}
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
        @can('list-subscription')
            <div class="col-12 mt-0">
                <div class="card">
                    <div class="card-body">
                        @if ($subscriptions->isEmpty())
                            <p class="text-center">{{ __('message.NO_SUBSCRIPTION') }}</p>
                        @else
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-bordered text-nowrap border-bottom">
                                        <thead>
                                            <tr>
                                                <th class="wd-15p border-bottom-0">{{ __('global.ID') }}</th>
                                                <th class="wd-15p border-bottom-0">{{ __('global.USER') }}</th>
                                                <th class="wd-15p border-bottom-0">{{ __('global.PLAN') }}</th>
                                                <th class="wd-15p border-bottom-0">{{ __('global.DURATION') }}</th>
                                                <th class="wd-15p border-bottom-0">{{ __('global.START DATE') }}</th>
                                                <th class="wd-15p border-bottom-0">{{ __('global.END DATE') }}</th>
                                                <th class="wd-15p border-bottom-0">{{ __('global.STATUS') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($subscriptions as $subscription)
                                                <tr>
                                                    <td>{{ $subscription->id }}</td>
                                                    <td>{{ $subscription->user->name }}</td>
                                                        <td>{{ $subscription->transactions->plan_details['plan']['plan_name'] ?? 'N/A' }}</td>
                                                        <td>{{ $subscription->transactions->plan_details['tenures'][0]['duration'] ?? 'N/A' }} {{ __('months') }}</td>
                                                    <td>{{ $subscription->start_date->format('d M, Y') }}</td>
                                                    <td>{{ $subscription->end_date->format('d M, Y') }}</td>
                                                    <td>
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
                                                    </td>

                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endcan
    </section>
@endsection
