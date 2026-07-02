<footer class="z-10 py-6 bg-white shadow-inner dark:bg-gray-900">
    <div
        class="container flex flex-col items-center justify-between px-6 mx-auto space-y-4 md:flex-row md:space-y-0 md:divide-x md:divide-gray-200 dark:md:divide-gray-700 text-gray-600 dark:text-gray-300">

        <!-- Center: Links -->
        <ul class="flex flex-wrap justify-center space-x-6 text-sm font-medium">
            <li>
                <a href="{{ route('frontend-terms-and-condition') }}"
                    class="transition-colors duration-200 hover:text-purple-600 dark:hover:text-purple-400">
                    {{ __('frontend-labels.sponsor_ads.terms_conditions') }}
                </a>
            </li>
            <li>
                <a href="{{ route('frontend-privacy-policies') }}"
                    class="transition-colors duration-200 hover:text-purple-600 dark:hover:text-purple-400">
                    {{ __('frontend-labels.sponsor_ads.privacy_policy') }}
                </a>
            </li>
        </ul>

        <!-- Right side: Copy -->
        <span class="pt-2 md:pt-0 md:pl-6 text-sm text-gray-500 dark:text-gray-400">
            <script>
                document.write("{{ __('frontend-labels.sponsor_ads.copyright') }}" + new Date().getFullYear());
            </script>

            <a href="{{ url()->current() }}" class="ml-1 font-semibold text-danger dark:text-danger  hover:underline">
                {{ $socialsettings['app_name'] ?? '#' }}
            </a>
        </span>
    </div>
</footer>
