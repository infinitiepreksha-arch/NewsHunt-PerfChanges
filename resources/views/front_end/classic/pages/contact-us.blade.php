@extends('front_end.' . $theme . '.layout.main')

@section('body')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/css/intlTelInput.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>
    <!-- Wrapper start -->
    <div id="wrapper" class="wrap overflow-hidden-x">
        <div class="breadcrumbs panel z-1 py-2 bg-gray-25 dark:bg-gray-100 dark:bg-opacity-5 dark:text-white">
            <div class="container max-w-xl">
                <ul class="breadcrumb nav-x justify-center gap-1 fs-7 sm:fs-6 m-0">
                    <li><a href="{{ url('home') }}">{{ __('frontend-labels.home.title') }}</a></li>
                    <li><i class="unicon-chevron-right opacity-50"></i></li>
                    <li><span class="opacity-70">{{ $title }}</span></li>
                </ul>
            </div>
        </div>
        <div class="section py-3 sm:py-6 lg:py-9">
            <div class="container max-w-xl">
                <div class="panel vstack gap-1 sm:gap-6 lg:gap-9">
                    <header class="page-header panel vstack text-center">
                        <h1 class="h3 lg:h1">{{ $title }}</h1>
                    </header>
                    <div id="contact-form-wrapper" class="panel pt-2">
                        <h4 class="h5 xl:h4 mb-3 xl:mb-3">{{ __('frontend-labels.contactus.leave_a_message') }}</h4>
                        <div class="comment_form_holder">
                            <form action="{{ route('contact_us.store') }}" method="POST" class="vstack gap-1"
                                id="contact-form">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <input class="form-control form-control-sm" type="text" id="first_name"
                                            name="first_name" placeholder="{{ __('frontend-labels.contactus.first_name') }}">
                                        <span class="help-block text-danger"></span> <!-- Error message container -->
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <input class="form-control form-control-sm" type="text" id="last_name"
                                            name="last_name" placeholder="{{ __('frontend-labels.contactus.last_name') }}">
                                        <span class="help-block text-danger"></span> <!-- Error message container -->
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <input class="form-control form-control-sm" type="email" id="email"
                                            name="email" placeholder="{{ __('frontend-labels.contactus.your_email') }}">
                                        <span class="help-block text-danger"
                                            data-email-required="{{ __('frontend-labels.contactus.email_required') }}"
                                            data-email-invalid="{{ __('frontend-labels.contactus.email_invalid') }}"></span>
                                    </div>

                                    <div class="col-md-6 mb-2">
                                        <div class="iti-container">
                                            <input class="form-control form-control-sm phone-input" type="tel"
                                                id="phone" name="phone"
                                                placeholder="{{ __('frontend-labels.contactus.enter_mobile_number') }}">
                                            <span class="help-block text-danger"
                                                data-phone-invalid="{{ __('frontend-labels.contactus.phone_invalid') }}"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <textarea class="form-control h-250px w-full fs-6" id="message" name="message"
                                        placeholder="{{ __('frontend-labels.contactus.describe_your_issue') }}"></textarea>
                                    <span class="help-block text-danger"
                                        data-message-required="{{ __('frontend-labels.contactus.message_required') }}"></span>
                                </div>

                                <button class="btn btn-primary btn-sm"
                                    type="submit">{{ __('frontend-labels.contactus.send') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
