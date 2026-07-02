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
    <div id="custom-ads-permissions" data-view-details="{{ auth()->user()->can('view-details-CustomAds') ? '1' : '0' }}"
        data-change-status="{{ auth()->user()->can('change-status-CustomAds') ? '1' : '0' }}">
        @can('list-CustomAds')
            <section class="section">
                <div class="card ">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 overflow-x-scroll">
                                <table class="table table-bordered text-nowrap border-bottom" id="Custom_ads_list"
                                    data-url="{{ route('custom-ads-request.index') }}">
                                    <thead>
                                        <tr>
                                            <th class="wd-5p border-bottom-0">{{ __('global.ID') }}</th>
                                            <th class="wd-10p border-bottom-0">{{ __('global.USER') }}</th>
                                            <th class="wd-15p border-bottom-0">{{ __('global.TITLE') }}</th>
                                            <th class="wd-10p border-bottom-0">{{ __('global.AD_TYPE') }}</th>
                                            <th class="wd-10p border-bottom-0">{{ __('global.VERTICAL_IMAGE') }}</th>
                                            <th class="wd-10p border-bottom-0">{{ __('global.HORIZONTAL_IMAGE') }}</th>
                                            <th class="wd-15p border-bottom-0">{{ __('global.AD_PUBLISH_STATUS') }}</th>
                                            <th class="wd-15p border-bottom-0">{{ __('global.PAYMENT_STATUS') }}</th>
                                            <th class="wd-15p border-bottom-0">{{ __('global.PRICING') }}</th>
                                            <th class="wd-10p border-bottom-0">{{ __('global.CREATED_AT') }}</th>
                                            <th class="wd-10p border-bottom-0">{{ __('global.ACTION') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- DataTables will populate this section -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Enhanced Modal -->
            <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="previewModalLabel">{{ __('global.PREVIEW') }}</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row g-4">

                                <!-- Ad Title and Basic Info -->
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0 ">
                                                <i class="fas fa-ad me-2"></i>{{ __('page.AD_INFORMATION') }}
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row mb-3">
                                                <div class="col-md-8">
                                                    <h4 class="mb-2" id="modal-title">{{ __('page.sample_ad_title') }}</h4>
                                                    <p class="mb-2" id="modal-description">
                                                        {{ __('page.ad_description_placeholder') }}</p>
                                                    <div class="d-flex gap-2 mb-2">
                                                        <span class="badge bg-warning text-white" id="modal-ad_type"></span>
                                                        <span class="badge bg-success text-white"
                                                            id="modal-ad_publish_status"></span>
                                                        <span class="badge bg-warning text-white"
                                                            id="modal-payment_status"></span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 text-center">
                                                    <div class="card bg-success text-white">
                                                        <div class="card-body">
                                                            <h3 class="card-title mb-1" id="modal-total_price">$0.00</h3>
                                                            <p class="card-text mb-0">{{ __('page.TOTAL_PRICE') }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row g-3">
                                                <div class="col-md-3">
                                                    <div class="border-start border-primary border-3 ps-3">
                                                        <label
                                                            class="form-label text-muted fw-bold small">{{ __('global.ID') }}</label>
                                                        <p class="mb-0 fw-semibold" id="modal-id">-</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="border-start border-primary border-3 ps-3">
                                                        <label
                                                            class="form-label text-muted fw-bold small">{{ __('global.USER_ID') }}</label>
                                                        <p class="mb-0 fw-semibold" id="modal-user_id">-</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="border-start border-primary border-3 ps-3">
                                                        <label
                                                            class="form-label text-muted fw-bold small">{{ __('global.SLUG') }}</label>
                                                        <p class="mb-0 fw-semibold" id="modal-slug">-</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="border-start border-primary border-3 ps-3">
                                                        <label
                                                            class="form-label text-muted fw-bold small">{{ __('global.URL') }}</label>
                                                        <p class="mb-0 fw-semibold" id="modal-url">-</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Image Section -->
                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            <h6 class="card-title mb-0 ">
                                                <i class="fas fa-image me-2"></i>{{ __('global.VERTICAL_IMAGE') }}
                                            </h6>
                                        </div>
                                        <div class="card-body text-center">
                                            <div class="rounded p-5">
                                                <div id="modal_vertical_image">
                                                    <i class="fas fa-image fa-3x text-muted mb-2"></i>
                                                    <p class="text-muted">{{ __('page.no_image_available') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            <h6 class="card-title mb-0 ">
                                                <i class="fas fa-image me-2"></i>{{ __('global.HORIZONTAL_IMAGE') }}
                                            </h6>
                                        </div>
                                        <div class="card-body text-center">
                                            <div class="rounded p-5">
                                                <div id="modal_horizontal_image">
                                                    <i class="fas fa-image fa-3x text-muted mb-2"></i>
                                                    <p class="text-muted">{{ __('page.no_image_available') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pricing Details -->
                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            <h6 class="card-title mb-0 ">
                                                <i class="fas fa-dollar-sign me-2"></i>{{ __('page.PRICING_DETAILS') }}
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-6">
                                                    <div class="text-center p-3  rounded">
                                                        <h5 class="mb-1" id="modal-daily_price">$0.00</h5>
                                                        <small>{{ __('global.DAILY_PRICE') }}</small>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="text-center p-3 rounded">
                                                        <h5 class="mb-1" id="modal-total_days">0</h5>
                                                        <small>{{ __('global.TOTAL_DAYS') }}</small>
                                                    </div>
                                                </div>

                                                <div class="col-12">
                                                    <div class="border-start border-gray border-3 ps-3">
                                                        <label
                                                            class="form-label fw-bold small">{{ __('global.PRICE_SUMMARY') }}</label>
                                                        <p class="mb-0" id="modal-price_summary">-</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Ad Placement and Analytics -->
                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            <h6 class="card-title mb-0 ">
                                                <i class="fas fa-map-marker-alt me-2"></i>{{ __('page.AD_PLACEMENT') }}
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <div class="border-start border-warning border-3 ps-3">
                                                        <label
                                                            class="form-label border p-2 rounded fw-bold small ">{{ __('global.WEB_ADS_PLACEMENT') }}</label>
                                                        <p class="mb-0" id="modal-web_ads_placement">-</p>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="border-start border-warning border-3 ps-3">
                                                        <label
                                                            class="form-label border fw-bold small  p-2 rounded ">{{ __('global.APP_ADS_PLACEMENT') }}</label>
                                                        <p class="mb-0" id="modal-app_ads_placement">-</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!-- Analytics and Payment Info -->
                                <div class="col-md-6">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            <h6 class="card-title mb-0 ">
                                                <i class="fas fa-chart-bar me-2"></i>{{ __('page.ANALYTICS_PAYMENT') }}
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <div class="border-start border-success border-3 ps-3">
                                                        <label
                                                            class="form-label  fw-bold small">{{ __('global.PAYMENT_GATEWAY') }}</label>
                                                        <p class="mb-0 fw-semibold" id="modal-payment_gateway">-</p>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="border-start border-success border-3 ps-3">
                                                        <label
                                                            class="form-label  fw-bold small">{{ __('global.TRANSACTION_ID') }}</label>
                                                        <p class="mb-0 fw-semibold" id="modal-transaction_id">-</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contact Information -->
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="card-title mb-0 ">
                                                <i class="fas fa-user me-2"></i>{{ __('page.CONTACT_INFORMATION') }}
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <div class="border-start border-info border-3 ps-3">
                                                        <label
                                                            class="form-label  fw-bold small ">{{ __('global.CONTACT_NAME') }}</label>
                                                        <p class="mb-0 fw-semibold" id="modal-contact_name">-</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="border-start border-info border-3 ps-3">
                                                        <label
                                                            class="form-label  fw-bold small">{{ __('global.CONTACT_EMAIL') }}</label>
                                                        <p class="mb-0 fw-semibold" id="modal-contact_email">-</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="border-start border-info border-3 ps-3">
                                                        <label
                                                            class="form-label  fw-bold small">{{ __('global.CONTACT_PHONE') }}</label>
                                                        <p class="mb-0 fw-semibold" id="modal-contact_phone">-</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Date Information -->
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h6 class="card-title mb-0 ">
                                                <i class="fas fa-calendar me-2"></i>{{ __('page.DATE_INFORMATION') }}
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-md-3">
                                                    <div class="border-start border-secondary border-3 ps-3">
                                                        <label
                                                            class="form-label  fw-bold small">{{ __('global.START_DATE') }}</label>
                                                        <p class="mb-0 fw-semibold" id="modal-start_date">-</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="border-start border-secondary border-3 ps-3">
                                                        <label
                                                            class="form-label  fw-bold small">{{ __('global.END_DATE') }}</label>
                                                        <p class="mb-0 fw-semibold" id="modal-end_date">-</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="border-start border-secondary border-3 ps-3">
                                                        <label
                                                            class="form-label  fw-bold small">{{ __('global.CREATED_AT') }}</label>
                                                        <p class="mb-0 fw-semibold" id="modal-created_at">-</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="border-start border-secondary border-3 ps-3">
                                                        <label
                                                            class="form-label  fw-bold small">{{ __('global.UPDATED_AT') }}</label>
                                                        <p class="mb-0 fw-semibold" id="modal-updated_at">-</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{ __('global.CLOSE') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="card mt-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 text-center py-5">
                            <h1 class="display-1 fw-bold text-danger">403</h1>
                            <h1 class="fw-bold mb-0 text-danger">Access Denied</h1>
                            <div class="d-flex justify-content-center mb-0">
                                <div class="col-8 col-md-8 col-lg-4">
                                    <img src="{{ asset('assets/images/access_Denied/no permission.png') }}"
                                        alt="Access Denied">
                                </div>
                            </div>

                            <div class="d-inline-block">
                                <h3 class="text-danger mb-0">{{ __('page.no_permission_custom_ads') }}
                                </h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    </div>
@endsection
