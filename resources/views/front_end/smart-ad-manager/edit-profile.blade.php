@extends('front_end.layouts.app')

@section('content')
    <div id="validationEditProfileModel" class="m-6">
        <div class="container px-6 mx-auto grid bg-white divide-y dark:divide-gray-700 dark:bg-gray-800 mt-4 border dark:border-gray-700  rounded">
            <div class="mt-6 pt-4 p-3">
                <h3 class="text-lg font-medium text-black dark:text-gray-200 mb-4">
                    {{ $title }}
                </h3>

                <form method="POST" id="validationEditProfile" action="{{ route('smart-ads-update-profile') }}"
                    enctype="multipart/form-data">
                    @csrf

                    <!-- Name -->
                    <label class="block text-sm mt-5">
                        <span class="text-black dark:text-white">{{ __('frontend-labels.sponsor_ads.full_name') }}</span>
                        <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}"
                            placeholder="{{ __('frontend-labels.sponsor_ads.placeholder_full_name') }}"
                            class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700
                                      focus:border-purple-400 focus:outline-none focus:shadow-outline-purple
                                      dark:text-gray-300 dark:focus:shadow-outline-gray form-input rounded" />
                        <span class="text-danger fw-bold error-text name_error"></span>

                    </label>

                    <!-- Email (Read-Only) -->
                    <label class="block text-sm mt-5">
                        <span class="text-black dark:text-white">{{ __('frontend-labels.sponsor_ads.email') }}</span>
                        <input type="email" name="email" id="email"
                            value="{{ old('email', auth()->user()->email) }}"
                            class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700
                                      cursor-not-allowed opacity-70
                                      focus:border-purple-400 focus:outline-none focus:shadow-outline-purple
                                      dark:text-gray-300 dark:focus:shadow-outline-gray form-input rounded" />
                        <span class="text-danger fw-bold error-text email_error"></span>
                    </label>

                    <!-- Profile Image -->
                    <label class="block text-sm mt-5">
                        <span
                            class="text-black dark:text-white">{{ __('frontend-labels.sponsor_ads.profile_image') }}</span>
                        <input type="file" name="profile" id="profile"
                            class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700
                                      focus:border-purple-400 focus:outline-none focus:shadow-outline-purple
                                      dark:text-gray-300 dark:focus:shadow-outline-gray form-input rounded" />
                        <span class="text-danger fw-bold error-text profile_error"></span>
                        <!-- Preview -->
                        @if (auth()->user()->profile)
                            <div class="mt-3">
                                <img src="{{ auth()->user()->profile ?? url('assets/images/faces/2.jpg') }}"
                                    class="profile-update-image border border-2 border-gray-300 dark:border-gray-600 mt-6 rounded"
                                    alt="Profile Image">
                            </div>
                        @endif
                    </label>

                    <!-- Submit -->
                    <div class="mt-6">
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium leading-5 text-white
                                       transition-colors duration-150 bg-purple-600 border border-transparent
                                       rounded 
                                       focus:outline-none focus:shadow-outline-purple">
                            {{ __('frontend-labels.sponsor_ads.update_profile_btn') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
