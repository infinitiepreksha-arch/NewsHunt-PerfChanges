@extends('front_end.layouts.app')

@section('content')
    <div id="validationEditPassAdsModel" class="m-6">
        <div class="container px-6 mx-auto grid  divide-y dark:divide-gray-700  mt-4 ">
            <div class="mt-6  pt-4 p-3 flex flex-col items-center justify-center text-center space-y-4">

                <div class="bg-white p-5 m-5 dark:bg-gray-800 current_pass_Ad border dark:border-gray-700 rounded">
                    <h3 class="text-lg font-medium text-gray-700 dark:text-gray-200 mb-4">
                        {{ $title }}
                    </h3>
                    <div class="flex flex-col items-center justify-center text-center space-y-4">
                        <div class="lottie_css">
                            <dotlottie-player src="{{ asset('front_end/classic/images/place-holser/password.json') }}"
                                background="transparent" speed="1" loop autoplay>
                            </dotlottie-player>
                        </div>
                    </div>
                    <form method="POST" id="validationEditPassAds" action="{{ route('smart-ads-update-password') }}">
                        @csrf

                        <!-- Old Password -->
                        <label class="block text-sm mt-5 ">
                            <span class="text-gray-700 dark:text-white">{{ __('frontend-labels.sponsor_ads.current_password') }}</span>
                            <input type="password" name="old_password" id="old_password"
                                placeholder="{{ __('frontend-labels.sponsor_ads.current_password') }}"
                                class="current_pass_Ad block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 
                               focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                               dark:text-gray-300 dark:focus:shadow-outline-gray form-input rounded" />
                            <span class="text-danger-ad fw-bold error-text old_password_error"></span>
                        </label>

                        <!-- New Password -->
                        <label class="block text-sm mt-5">
                            <span class="text-gray-700 dark:text-white">{{ __('frontend-labels.sponsor_ads.new_password') }}</span>
                            <input type="password" name="new_password" id="new_password" placeholder="{{ __('frontend-labels.sponsor_ads.new_password') }}"
                                class="current_pass_Ad block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 
                               focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                               dark:text-gray-300 dark:focus:shadow-outline-gray form-input rounded"
                                minlength="8" />
                            <span class="text-danger-ad fw-bold error-text new_password_error"></span>
                        </label>

                        <!-- Confirm Password -->
                        <label class="block text-sm mt-5">
                            <span class="text-gray-700 dark:text-white">{{ __('frontend-labels.sponsor_ads.confirm_new_password') }}</span>
                            <input type="password" name="confirm_password" id="confirm_password"
                                placeholder="{{ __('frontend-labels.sponsor_ads.confirm_new_password') }}"
                                class="current_pass_Ad block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 
                               focus:border-purple-400 focus:outline-none focus:shadow-outline-purple 
                               dark:text-gray-300 dark:focus:shadow-outline-gray form-input rounded" />
                            <span class="text-danger-ad fw-bold error-text confirm_password_error"></span>
                        </label>

                        <!-- Submit -->
                        <div class="mt-6">
                            <button type="submit"
                                class="px-4 py-2 text-sm font-medium leading-5 text-white 
                               transition-colors duration-150 bg-purple-600 border border-transparent 
                               rounded  hover:bg-purple-700 
                               focus:outline-none focus:shadow-outline-purple">
                                {{ __('frontend-labels.sponsor_ads.update_password') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
