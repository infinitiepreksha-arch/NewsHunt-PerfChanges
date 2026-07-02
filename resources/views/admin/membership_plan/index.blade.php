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
        @can('create-plan')
            <div class="col-auto ms-auto d-print-none gap-1">
                <div class="col-auto ms-auto d-print-none gap-1">
                    <a class="btn btn-primary" href="{{ route('pricing-plans.create') }}"> {{ $title }}
                    </a>
                </div>
            </div>
        @endcan
    </div>
@endsection

@section('content')
    <section class="section">
        @can('list-plan')
            {{-- @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif --}}



            <div class="col-12 mt-0">
                <div class="card">
                    <div class="card-body">
                        <div class="col-12 mt-0">
                            <div class="alert alert-primary alert-dismissible" role="alert">
                                {{ __('page.MEMBERSHIP_PLANS_DISPLAY_NOTE') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        </div>
                        @if ($plans->isEmpty())
                            <p class="text-center">{{ __('No pricing plans found.') }}</p>
                        @else
                            <div class="row">
                                @foreach ($plans as $plan)
                                    <div class="col-lg-3 col-md-6 mb-4">
                                        <div class="card h-100 d-flex flex-column position-relative rounded">
                                            <div class="card-body d-flex flex-column p-4">
                                                <h1 class="fw-bold mb-3">{{ $plan->name }}</h1>

                                                @if ($plan->planTenures->isNotEmpty())
                                                    @php
                                                        $lowestTenure = $plan->planTenures->sortBy('price')->first();
                                                    @endphp
                                                    <h2 class="display-8 fw-bold mb-3">
                                                        <span class="fs-10">{{ $payment->currency ?? '$' }}</span>
                                                        {{ number_format($lowestTenure->price, 2) }}
                                                        <span class="fs-6 text-muted">/ {{ $lowestTenure->duration }}
                                                            {{ __('month') }}{{ $lowestTenure->duration > 1 ? 's' : '' }}</span>
                                                    </h2>
                                                @endif

                                                <p class="text-muted mb-4">{{ $plan->description }}</p>

                                                <ul class="list-unstyled mb-4">
                                                    @if ($plan->features_plan)
                                                        @if ($plan->features_plan->is_ads_free)
                                                            <li class="mb-2">
                                                                <i
                                                                    class="bi bi-check-circle-fill text-primary me-2"></i>{{ __('Ads Free Experience') }}
                                                            </li>
                                                        @endif

                                                        @if ($plan->features_plan->number_of_articles > 0)
                                                            <li class="mb-2">
                                                                <i
                                                                    class="bi bi-check-circle-fill text-primary me-2"></i>{{ $plan->features_plan->number_of_articles }}
                                                                {{ __('Articles') }}
                                                            </li>
                                                        @endif

                                                        @if ($plan->features_plan->number_of_stories > 0)
                                                            <li class="mb-2">
                                                                <i
                                                                    class="bi bi-check-circle-fill text-primary me-2"></i>{{ $plan->features_plan->number_of_stories }}
                                                                {{ __('Stories') }}
                                                            </li>
                                                        @endif

                                                        @if ($plan->features_plan->number_of_e_papers_and_magazines > 0)
                                                            <li class="mb-2">
                                                                <i
                                                                    class="bi bi-check-circle-fill text-primary me-2"></i>{{ $plan->features_plan->number_of_e_papers_and_magazines }}
                                                                {{ __('E-Paper and Magazines') }}
                                                            </li>
                                                        @endif
                                                    @endif
                                                </ul>

                                                @if ($plan->planTenures->isNotEmpty())
                                                    <div class="mb-4">
                                                        <label class="form-label">{{ __('Select Tenure') }}</label>
                                                        <select class="form-select tenure-selector"
                                                            data-plan="{{ $plan->id }}">
                                                            @foreach ($plan->planTenures as $tenure)
                                                                <option value="{{ $tenure->id }}"
                                                                    data-price="{{ $tenure->price }}"
                                                                    data-duration="{{ $tenure->duration }}">
                                                                    {{ $tenure->name ?? $tenure->duration . ' ' . __('month') . ($tenure->duration > 1 ? 's' : '') }}
                                                                    -
                                                                    {{ $payment->currency ?? '$' }}{{ number_format($tenure->price, 2) }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                @endif

                                                <div class="mt-auto">
                                                    <form action="{{ route('payment.form') }}" method="GET"
                                                        class="payment-form-{{ $plan->id }}">
                                                        <input type="hidden" name="plan" value="{{ $plan->id }}">
                                                        <input type="hidden" name="name" value="{{ $plan->name }}">
                                                        @if ($plan->planTenures->isNotEmpty())
                                                            <input type="hidden" name="tenure_id"
                                                                value="{{ $plan->planTenures->first()->id }}">
                                                            <input type="hidden" name="amount"
                                                                value="{{ $plan->planTenures->first()->price }}">
                                                            <input type="hidden" name="duration"
                                                                value="{{ $plan->planTenures->first()->duration }}">
                                                        @endif
                                                    </form>
                                                </div>
                                            </div>

                                            <div class="position-absolute top-0 end-0 p-2">
                                                @if (auth()->user()->can('update-plan') || auth()->user()->can('delete-plan'))
                                                    <div class="dropdown">
                                                        <button class="btn btn-icon btn-link p-1" type="button"
                                                            data-bs-toggle="dropdown" aria-expanded="false">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                height="24" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                                stroke-linejoin="round" class="icon">
                                                                <path d="M5 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                                                <path d="M12 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                                                <path d="M19 12m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
                                                            </svg>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            @can('update-plan')
                                                                 <li>
                                                                    <a class="dropdown-item d-flex justify-content-between align-items-center {{ $plan->is_active ? 'plan-edit-active' : '' }}"
                                                                        href="{{ $plan->is_active ? 'javascript:void(0)' : route('pricing-plans.edit', $plan->id) }}">
                                                                        {{ __('page.UPDATE_PLAN') }}
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                            height="24" viewBox="0 0 24 24" fill="none"
                                                                            stroke="currentColor" stroke-width="2"
                                                                            stroke-linecap="round" stroke-linejoin="round"
                                                                            class="icon icon-tabler icons-tabler-outline icon-tabler-edit">
                                                                            <path stroke="none" d="M0 0h24v24H0z"
                                                                                fill="none" />
                                                                            <path
                                                                                d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                                            <path
                                                                                d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                                            <path d="M16 5l3 3" />
                                                                        </svg>
                                                                    </a>
                                                                </li>
                                                            @endcan
                                                            @can('delete-plan')
                                                                <li>
                                                                    <form id="delete-plan-form-{{ $plan->id }}"
                                                                        action="{{ route('pricing-plans.destroy', $plan->id) }}"
                                                                        method="POST">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                         <button type="button"
                                                                            class="text-danger dropdown-item d-flex justify-content-between align-items-center plan-delete-btn"
                                                                            data-id="{{ $plan->id }}"
                                                                            data-is-active="{{ $plan->is_active ? 'true' : 'false' }}">
                                                                            {{ __('Delete') }}
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                viewBox="0 0 24 24" class="icon">
                                                                                <path
                                                                                    d="M20 6a1 1 0 01.117 1.993L20 8h-.081l-.919 11a3 3 0 01-2.824 2.995L16 22H8c-1.598 0-2.904-1.249-2.992-2.75L5 19.083 4.08 8H4a1 1 0 01-.117-1.993L4 6h16zm-6-4a2 2 0 012 2 1 1 0 01-1.993.117L14 4h-4l-.007.117A1 1 0 018 4a2 2 0 011.85-1.995L10 2h4z"
                                                                                    fill="currentColor" />
                                                                                <path d="M0 0h24v24H0z" fill="none" />
                                                                            </svg>
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            @endcan
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
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
                    <h3 class="text-danger mb-0">You do not have permission to view the list of Pricing Plans.
                    </h3>
                </div>
            </div>
        @endcan
    </section>
@endsection
@section('script')
    <script type="text/javascript"
        src="{{ asset('/assets/js/custom/payment_gatway/membership_plan.js') }}?v=<?= time() ?>"></script>
@endsection
