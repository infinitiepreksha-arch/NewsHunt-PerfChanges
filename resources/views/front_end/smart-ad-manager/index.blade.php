@extends('front_end.layouts.app')
@section('content')
    <div class="container px-6 mx-auto grid">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            {{ $title }}
        </h2>

        <!-- Alert Message -->
        @if (session()->has('message'))
            <div
                class="bg-{{ session('color') }}-100 text-{{ session('color') }}-800 p-4 text-sm rounded border border-{{ session('color') }}-300 my-3">
                {{ session('message') }}
            </div>
            <span class="bg-green-100 bg-red-100"></span>
        @endif

        <!-- New Table -->
        <div class="w-full overflow-hidden rounded shadow-xs">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr
                            class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border dark:border-gray-700  bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                            <th class="px-4 py-3  border dark:border-gray-700 ">{{ __('frontend-labels.sponsor_ads.ad_name') }}</th>
                            <th class="px-4 py-3  border dark:border-gray-700 ">{{ __('frontend-labels.sponsor_ads.clicks') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                        @forelse($smartAds as $ad)
                            <tr class="text-gray-700 dark:text-gray-400">
                                <td class="px-4 py-3  border dark:border-gray-700 ">
                                    <div class="flex items-center text-sm">
                                        <div>
                                            <a href="/smart-ad-manager/ads/{{ $ad->id }}">
                                                <p class="font-semibold">{{ $ad->name }}</p>
                                            </a>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $ad->clicks }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="p-2 my-6 font-semibold text-gray-700 dark:text-gray-200 border dark:border-gray-700 " colspan="9">
                                    {{ __('frontend-labels.sponsor_ads.no_ads_found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div
                class="grid px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase border-t dark:border-gray-700 bg-gray-50 sm:grid-cols-9 dark:text-gray-400 dark:bg-gray-800">
                <span class="flex items-center col-span-3">
                    {{ __('frontend-labels.sponsor_ads.showing') }} {{ $smartAds->firstItem() }}-{{ $smartAds->lastItem() }}
                    {{ __('frontend-labels.sponsor_ads.of') }} {{ $smartAds->total() }}
                </span>
                <span class="col-span-2"></span>
                <!-- Pagination -->
                <div class="col-span-4 flex justify-end">
                    {{ $smartAds->onEachSide(2)->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
