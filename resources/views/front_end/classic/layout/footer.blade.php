<footer id="uc-footer" class="uc-footer panel uc-dark">
    <div class="footer-outer py-4 lg:py-6 bg-white dark:bg-gray-900 text-gray-900 dark:text-white text-opacity-50">
        <div class="container max-w-xl">
            <div class="footer-inner vstack gap-6 xl:gap-8">
                <div id="footer-ad-container" class="text-center my-3"></div>

                <div class="uc-footer-bottom panel vstack gap-4 justify-center lg:fs-5">
                    <div class="footer-social hstack justify-center gap-2 lg:gap-3">
                        <ul class="nav-x gap-2">
                            <li>
                                <a class="btn btn-md p-0 border-gray-900 border-opacity-15 w-32px lg:w-48px h-32px lg:h-48px text-dark dark:text-white dark:border-white hover:bg-primary hover:border-primary hover:text-white rounded-circle"
                                    aria-label="Instagram" href="{{ $socialsettings['instagram_link'] ?? '#' }}"><i
                                        class="unicon-logo-instagram icon-1"></i></a>
                            </li>
                            <li>
                                <a class="btn btn-md p-0 border-gray-900 border-opacity-15 w-32px lg:w-48px h-32px lg:h-48px text-dark dark:text-white dark:border-white hover:bg-primary hover:border-primary hover:text-white rounded-circle"
                                    aria-label="X" href="{{ $socialsettings['x_link'] ?? '#' }}"><i
                                        class="unicon-logo-x-filled icon-1"></i></a>
                            </li>
                            <li>
                                <a class="btn btn-md p-0 border-gray-900 border-opacity-15 w-32px lg:w-48px h-32px lg:h-48px text-dark dark:text-white dark:border-white hover:bg-primary hover:border-primary hover:text-white rounded-circle"
                                    aria-label="Facebook" href="{{ $socialsettings['facebook_link'] ?? '#' }}"><i
                                        class="unicon-logo-facebook icon-1"></i></a>
                            </li>
                            <li>
                                <a class="btn btn-md p-0 border-gray-900 border-opacity-15 w-32px lg:w-48px h-32px lg:h-48px text-dark dark:text-white dark:border-white hover:bg-primary hover:border-primary hover:text-white rounded-circle"
                                    aria-label="LinkedIn" href="{{ $socialsettings['linkedin_link'] ?? '#' }}"><i
                                        class="unicon-logo-linkedin icon-1"></i></a>
                            </li>
                            <li>
                                <a class="btn btn-md p-0 border-gray-900 border-opacity-15 w-32px lg:w-48px h-32px lg:h-48px text-dark dark:text-white dark:border-white hover:bg-primary hover:border-primary hover:text-white rounded-circle"
                                    aria-label="Pinterest" href="{{ $socialsettings['pinterest_link'] ?? '#' }}"><i
                                        class="unicon-logo-pinterest icon-1"></i></a>
                            </li>
                        </ul>
                    </div>
                    <div class="d-flex justify-end gap-2 d-none">
                        <img src="{{ asset('front_end/classic/images/common/playstore-large.svg') }}"
                            class="h-100 w-96px" alt="Download on Android">
                        <img src="{{ asset('front_end/classic/images/common/appstore-large.svg') }}"
                            class="h-100 w-96px" alt="Download on apple">
                    </div>
                    <div class="footer-copyright vstack sm:hstack justify-center items-center gap-1 lg:gap-2">
                        <p>
                            <script>
                                document.write("{{ __('frontend-labels.sponsor_ads.copyright') }}" + new Date().getFullYear());
                            </script>
                            <a href="{{ url()->current() }}"
                                class="uc-link  border-bottom hover:text-gray-900 dark:hover:text-white duration-150">{{ $socialsettings['app_name'] ?? '#' }}</a>
                        </p>
                        <ul class="nav-x gap-2 fw-medium">
                            <li><a class="uc-link border-bottom  hover:text-gray-900 dark:hover:text-white duration-150"
                                    href="{{ url('/privacy-policies') }}">{{ __('frontend-labels.sponsor_ads.privacy_policy') }}</a>
                            </li>
                            <li><a class="uc-link border-bottom  hover:text-gray-900 dark:hover:text-white duration-150"
                                    href="{{ url('/terms-and-condition') }}">{{ __('frontend-labels.sponsor_ads.terms_conditions') }}</a>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>
    @if (isset($footer_script))
        {!! $footer_script->value !!}
    @endif
</footer>
