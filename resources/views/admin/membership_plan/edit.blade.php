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
    {{-- EDIT MEMBERSHIP PLANS FORM --}}
    <form id="editPricingPlanForm_{{ $plan->id }}" class="editPricingPlanFormValidation" method="POST"
        action="{{ route('pricing-plans.update', $plan->id) }}" data-parsley-validate>
        @csrf
        @method('PUT')
        <input type="hidden" name="id" value="{{ $plan->id }}">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="mb-0">{{ __('message.UPDATE_PRICING_PLAN') }}</h5>
            </div>
            <div class="card-body">
                <!-- Basic Plan Information -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">{{ __('message.BASIC_PLAN_INFO') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="edit_name_{{ $plan->id }}"
                                        class="form-label">{{ __('message.PLAN_NAME') }}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_name_{{ $plan->id }}"
                                        name="name" value="{{ $plan->name }}">
                                </div>
                                <span class="parsley-required error-text name_error mt-0"></span>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="edit_slug_{{ $plan->id }}"
                                        class="form-label">{{ __('message.PLAN_SLUG') }}<span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="edit_slug_{{ $plan->id }}"
                                        name="slug" value="{{ $plan->slug }}">
                                    <small class="text-muted">{{ __('message.MUST_BE_UNIQUE') }}</small>

                                </div>
                                <span class="parsley-required error-text slug_error"></span>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="edit_description_{{ $plan->id }}"
                                class="form-label">{{ __('message.PLAN_DESC') }}</label>
                            <textarea class="form-control" id="edit_description_{{ $plan->id }}" name="description" rows="3">{{ $plan->description }}</textarea>
                        </div>

                        <div class="form-group mb-3">
                            <label for="edit_status_{{ $plan->id }}"
                                class="form-label d-block">{{ __('message.STATUS') }}</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="edit_status_{{ $plan->id }}"
                                    name="status" value="1" {{ $plan->status ? 'checked' : '' }}>
                                <label class="form-check-label"
                                    for="edit_status_{{ $plan->id }}">{{ __('message.ACTIVE') }}</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Plan Features -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">{{ __('message.PLAN_FEATURES') }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="edit_number_of_articles_{{ $plan->id }}"
                                        class="form-label">{{ __('message.NO_OF_ARTICLES') }}</label>
                                    <input type="number" class="form-control"
                                        id="edit_number_of_articles_{{ $plan->id }}" name="number_of_articles"
                                        min="0" value="{{ $plan->features_plan->number_of_articles ?? 0 }}">
                                    <span class="parsley-required  error-text number_of_articles_error"></span>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="edit_number_of_stories_{{ $plan->id }}"
                                        class="form-label">{{ __('message.NO_OF_STORIES') }}</label>
                                    <input type="number" class="form-control"
                                        id="edit_number_of_stories_{{ $plan->id }}" name="number_of_stories"
                                        min="0" value="{{ $plan->features_plan->number_of_stories ?? 0 }}">
                                    <span class="parsley-required  error-text number_of_stories_error"></span>

                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="edit_number_of_e_papers_and_magazines_{{ $plan->id }}"
                                        class="form-label">{{ __('message.NO_OF_E-PAPER_AND_MAGAZINES') }}</label>
                                    <input type="number" class="form-control"
                                        id="edit_number_of_e_papers_and_magazines{{ $plan->id }}"
                                        name="number_of_e_papers_and_magazines" min="0"
                                        value="{{ $plan->features_plan->number_of_e_papers_and_magazines ?? 0 }}">
                                    <span
                                        class="parsley-required  error-text number_of_e_papers_and_magazines_error"></span>

                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox"
                                    id="edit_is_ads_free_{{ $plan->id }}" name="is_ads_free" value="1"
                                    {{ $plan->features_plan && $plan->features_plan->is_ads_free ? 'checked' : '' }}>
                                <label class="form-check-label"
                                    for="edit_is_ads_free_{{ $plan->id }}">{{ __('message.ADS_FREE') }}</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Plan Tenure Information -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">{{ __('message.TENURE_INFO') }}</h6>
                    </div>
                    <div class="card-body">
                        @if ($plan->planTenures && $plan->planTenures->count() > 0)
                            @foreach ($plan->planTenures as $index => $tenure)
                                <div class="row tenure-row mb-3">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="tenure_name_{{ $plan->id }}_{{ $index }}"
                                                class="form-label">{{ __('message.TENURE_NAME') }}</label>
                                            <input type="text" class="form-control" name="tenure_name[]"
                                                id="tenure_name_{{ $plan->id }}_{{ $index }}"
                                                placeholder="e.g. Monthly, Annual" value="{{ $tenure->name }}">
                                            <input type="hidden" name="tenure_id[]" value="{{ $tenure->id }}">
                                            <span class="parsley-required error-text tenure_name_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="duration_{{ $plan->id }}_{{ $index }}"
                                                class="form-label">{{ __('message.TENURE_DURATION') }}<span
                                                    class="text-danger">*</span></label>
                                            <input type="number" class="form-control" name="duration[]"
                                                id="duration_{{ $plan->id }}_{{ $index }}" min="1"
                                                value="{{ $tenure->duration }}">
                                        </div>
                                        <span class="parsley-required error-text duration_error"></span>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="price_{{ $plan->id }}_{{ $index }}"
                                                class="form-label">{{ __('message.TENURE_PRICE') }} <span
                                                    class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text">$</span>
                                                <input type="number" class="form-control" name="price[]"
                                                    id="price_{{ $plan->id }}_{{ $index }}" min="0"
                                                    step="0.01" value="{{ $tenure->price }}">
                                            </div>
                                            <span class="parsley-required error-text price_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 mt-2">
                                        <div class="form-group">
                                            <label for="tenure_name_{{ $plan->id }}_{{ $index }}"
                                                class="form-label">{{ __('message.PRODUCT_ID') }}</label>
                                            <input type="text" class="form-control" name="product_id[]"
                                                id="product_id{{ $plan->id }}_{{ $index }}"
                                                placeholder="e.g. premium_one_month" value="{{ $tenure->product_id }}">
                                            <small class="text-danger fw-bolder">
                                                Product ID is required for only Apple Pay integration. Leave this blank
                                                if
                                                Apple Pay
                                                is disabled or your using another gateway.
                                            </small>
                                            <span class="parsley-required error-text product_id_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-1">
                                        <div class="form-group">
                                            <label class="form-label mt-3 p-1"></label>

                                            <div class="d-flex align-items-end h-100">
                                                <button type="button"
                                                    class="btn btn-danger remove-tenure-btn display-none"
                                                    {{ $index == 0 && $plan->planTenures->count() == 1 ? 'style="display: none;"' : '' }}>
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                            @endforeach
                        @else
                            <div class="row tenure-row mb-3">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="tenure_name_{{ $plan->id }}_0"
                                            class="form-label">{{ __('message.TENURE_NAME') }}<span
                                            class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="tenure_name[]"
                                            id="tenure_name_{{ $plan->id }}_0" placeholder="e.g. Monthly, Annual">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="duration_{{ $plan->id }}_0"
                                            class="form-label">{{ __('message.TENURE_DURATION') }}<span
                                                class="text-danger">*</span></label>
                                        <input type="number" class="form-control" name="duration[]"
                                            id="duration_{{ $plan->id }}_0" min="1">
                                        <span class="parsley-required error-text duration_error"></span>

                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="price_{{ $plan->id }}_0"
                                            class="form-label">{{ __('message.TENURE_PRICE') }} <span
                                                class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control" name="price[]"
                                                id="price_{{ $plan->id }}_0" min="0" step="0.01">
                                            <span class="parsley-required error-text price_error"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 mt-2">
                                    <div class="form-group">
                                        <label for="tenure_name_{{ $plan->id }}_{{ $index }}"
                                            class="form-label">{{ __('message.PRODUCT_ID') }}</label>
                                        <input type="text" class="form-control" name="product_id[]"
                                            id="product_id{{ $plan->id }}_{{ $index }}"
                                            placeholder="e.g. premium_one_month" value="{{ $tenure->product_id }}">
                                        <small class="text-danger fw-bolder">
                                            Product ID is required for only Apple Pay integration. Leave this blank if
                                            Apple Pay
                                            is disabled or your using another gateway.
                                        </small>
                                        <span class="parsley-required error-text product_id_error"></span>
                                    </div>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button"
                                        class="btn btn-sm btn-danger remove-tenure-btn mb-2 display-none">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        @endif

                        <div id="tenure-container-{{ $plan->id }}"></div>

                        <button type="button" class="btn btn-outline-secondary w-100 mt-3"
                            id="add-tenure-btn-{{ $plan->id }}">
                            <i class="fas fa-plus me-1"></i> {{ __('message.ADD_ANOTHER_TENURE') }}
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">{{ __('message.UPDATE_PLAN') }}</button>
            </div>
        </div>
    </form>

@section('script')
    <script type="text/javascript" src="{{ asset('assets/js/custom/payment_gatway/membership_plan.js') }}?v=<?= time() ?>">
    </script>
@endsection
@endsection
