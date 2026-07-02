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
                <a href="{{ route('pricing-plans.index') }}">{{ __('message.MEMBERSHIP_PLANS') }}</a> /
                @yield('pre-title')
            </div>
            <h2 class="page-title mt-2">
                @yield('title')
            </h2>
        </div>
        <!-- Page title actions -->
        <div class="col-auto ms-auto d-print-none">
        </div>
    </div>
@endsection


@section('content')
    {{-- CREATE MEMBERSHIP PLANS FORM --}}
    <form action="{{ route('pricing-plans.store') }}" class="form-horizontal" id="addPricingPlanForm" method="POST"
        data-parsley-validate>
        @csrf
        <div class="card mb-3">
            <div class="card-header ">
                <h3 class="mb-0 ">{{ __('message.ADD_PRICING_PLAN') }}</h3>
            </div>
            <div class="card-body">
                <!-- Basic Plan Information -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="mb-0">{{ __('message.BASIC_PLAN_INFO') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Plan Name -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">{{ __('message.PLAN_NAME') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name">
                                    <span class="parsley-required error-text name_error"></span>
                                </div>
                            </div>

                            <!-- Plan Slug -->
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="slug" class="form-label">{{ __('message.PLAN_SLUG') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="slug" name="slug">
                                    <small class="text-muted">{{ __('message.MUST_BE_UNIQUE') }}</small>
                                    <span class="parsley-required error-text slug_error"></span>
                                </div>
                            </div>

                        </div>

                        <div class="form-group mb-3">
                            <label for="description" class="form-label">{{ __('message.PLAN_DESC') }}</label>
                            <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                        </div>

                        <div class="form-group mb-3">
                            <label for="status" class="form-label d-block">{{ __('message.PLAN_STATUS') }}</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="status" name="status" value="1"
                                    checked>
                                <label class="form-check-label" for="status">{{ __('message.PLAN_ACTIVE') }}</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Plan Features -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="mb-0">{{ __('message.PLAN_FEATURES') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="number_of_articles"
                                        class="form-label">{{ __('message.NO_OF_ARTICLES') }}</label>
                                    <input type="number" class="form-control" id="number_of_articles"
                                        name="number_of_articles" min="0" value="0">
                                    <span class="parsley-required  error-text number_of_articles_error"></span>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="number_of_stories"
                                        class="form-label">{{ __('message.NO_OF_STORIES') }}</label>
                                    <input type="number" class="form-control" id="number_of_stories"
                                        name="number_of_stories" min="0" value="0">
                                    <span class="parsley-required  error-text number_of_stories_error"></span>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="number_of_e_papers_and_magazines"
                                        class="form-label">{{ __('message.NO_OF_E-PAPER_AND_MAGAZINES') }}</label>
                                    <input type="number" class="form-control" id="number_of_e_papers_and_magazinese"
                                        name="number_of_e_papers_and_magazines" min="0" value="0">
                                    <span class="parsley-required  error-text number_of_e_papers_and_magazines_error"></span>

                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_ads_free" name="is_ads_free"
                                    value="1">
                                <label class="form-check-label" for="is_ads_free"> {{ __('message.ADS_FREE') }}</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Plan Tenure Information -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="mb-0">{{ __('message.TENURE_INFO') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row tenure-row mb-3">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="tenure_name" class="form-label">{{ __('message.TENURE_NAME') }}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="tenure_name[]"
                                        placeholder="e.g. Months">
                                    <span class="parsley-required error-text tenure_name_error"></span>

                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="duration" class="form-label">{{ __('message.TENURE_DURATION') }} <span
                                            class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="duration[]" min="1">
                                    <span class="parsley-required error-text duration_error"></span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="price" class="form-label">{{ __('message.TENURE_PRICE') }} <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" name="price[]" min="0"
                                            step="0.01">
                                    </div>
                                    <span class="parsley-required error-text price_error"></span>
                                </div>
                            </div>


                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="product_id" class="form-label">{{ __('message.PRODUCT_ID') }} </label>
                                    <input type="text" class="form-control" name="product_id[]"
                                        placeholder="e.g. premium_one_month">
                                    <small class="text-danger fw-bolder">
                                        Product ID is required for only Apple Pay integration. Leave this blank if Apple Pay
                                        is disabled or your using another gateway.
                                    </small>
                                </div>
                            </div>

                            <div class="col-md-1">
                                <div class="form-group">
                                    <label class="form-label mt-3 p-1"></label>
                                    <div class="d-flex align-items-end h-100">
                                        <button type="button" class="btn btn-danger remove-tenure-btn display-none">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="tenure-container"></div>

                        <button type="button" class="btn btn-outline-primary w-100 mt-3" id="add-tenure-btn">
                            <i class="fas fa-plus me-1"></i> {{ __('message.ADD_ANOTHER_TENURE') }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">{{ __('message.SAVE_PLAN') }}</button>
            </div>
        </div>
    </form>


@section('script')
    <script type="text/javascript"
        src="{{ asset('/assets/js/custom/payment_gatway/membership_plan.js') }}?v=<?= time() ?>"></script>
@endsection
@endsection
