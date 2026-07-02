<div class="py-4 text-gray-500 dark:text-gray-400">
    <a>
        <button>
            <template x-if="!dark">
                <img class="object-cover "
                    src="{{ $dark_logo != null ? url('storage/' . $dark_logo->value) : asset('assets/images/logo/DarkLogo.png') }}"
                    alt="Light" aria-hidden="true">
            </template>
            <template x-if="dark">
                <img class="object-cover "
                    src="{{ $light_logo != null ? url('storage/' . $light_logo->value) : asset('assets/images/logo/LightLogo.png') }}"
                    aria-hidden="true" alt="Dark">
            </template>
        </button>
    </a>

    <span class="navbar-brand navbar-brand-autodark text-lg font-bold text-gray-800 dark:text-gray-200">

    </span>
    <ul class="mt-6">
        <li class="relative px-6 py-3">
            <span class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg"
                aria-hidden="true"></span>
            <a class="inline-flex items-center w-full text-sm font-semibold text-gray-800 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100"
                href="{{ route('smart-ads.dashboard') }}">
                <svg class="w-5 h-5 {{ request()->routeIs('smart-ads.dashboard') ? 'active_tab_sponsor_ads fw-bold' : '' }}"
                    aria-hidden="true" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                    </path>
                </svg>
                <span
                    class="ml-4 {{ request()->routeIs('smart-ads.dashboard') ? 'active_tab_sponsor_ads fw-bold' : '' }}">{{ __('frontend-labels.sponsor_ads.dashboard') }}</span>
            </a>
        </li>
        <li class="relative px-6 py-3">
            <span class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg"
                aria-hidden="true"></span>
            <a class="inline-flex items-center w-full text-sm font-semibold text-gray-800 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100"
                href="{{ route('smart-ads.index') }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor"
                    class="w-6 h-6 {{ request()->routeIs('smart-ads.index') ? 'active_tab_sponsor_ads fw-bold' : '' }}">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                </svg>

                <span class="ml-4 {{ request()->routeIs('smart-ads.index') ? 'active_tab_sponsor_ads fw-bold' : '' }}">{{ __('frontend-labels.sponsor_ads.sponsor_ads_details') }}</span>
            </a>
        </li>
        <li class="relative px-6 py-3">
            <span class="absolute inset-y-0 left-0 w-1 bg-purple-600 rounded-tr-lg rounded-br-lg"
                aria-hidden="true"></span>
            <a class="inline-flex items-center w-full text-sm font-semibold text-gray-800 transition-colors duration-150 hover:text-gray-800 dark:hover:text-gray-200 dark:text-gray-100"
                href="{{ route('smart-ads-transaction-page') }}">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor"
                    class="w-6 h-6 {{ request()->routeIs('smart-ads-transaction-page') ? 'active_tab_sponsor_ads fw-bold' : '' }}">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                </svg>
                <span
                    class="ml-4 {{ request()->routeIs('smart-ads-transaction-page') ? 'active_tab_sponsor_ads fw-bold' : '' }}">{{ __('frontend-labels.sponsor_ads.transaction_details') }}</span>
            </a>
        </li>
    </ul>


    <div class="px-6 my-6">
        <a href="{{ route('smart-ads.create') }}"
            class="flex items-center justify-between w-full px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded   focus:outline-none focus:shadow-outline-purple">
            {{ __('frontend-labels.sponsor_ads.create_news_ad') }}
            <span class="ml-2" aria-hidden="true">+</span>
        </a>
    </div>
</div>
