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
        @endif

        <!-- Transactions Table -->
        <div class="w-full overflow-hidden rounded shadow-xs">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr
                            class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border dark:border-gray-700  bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                            <th class="px-4 py-3  border dark:border-gray-700 ">{{ __('frontend-labels.sponsor_ads.ad_name') }}</th>
                            <th class="px-4 py-3  border dark:border-gray-700 ">{{ __('frontend-labels.sponsor_ads.amount') }}</th>
                            <th class="px-4 py-3  border dark:border-gray-700 ">{{ __('frontend-labels.sponsor_ads.currency') }}</th>
                            <th class="px-4 py-3  border dark:border-gray-700 ">{{ __('frontend-labels.sponsor_ads.gateway') }}</th>
                            <th class="px-4 py-3  border dark:border-gray-700 ">{{ __('frontend-labels.sponsor_ads.transaction_id') }}</th>
                            <th class="px-4 py-3  border dark:border-gray-700 ">{{ __('frontend-labels.sponsor_ads.start_date') }}</th>
                            <th class="px-4 py-3  border dark:border-gray-700 ">{{ __('frontend-labels.sponsor_ads.end_date') }}</th>
                            <th class="px-4 py-3  border dark:border-gray-700 ">{{ __('frontend-labels.sponsor_ads.status') }}</th>
                            <th class="px-4 py-3  border dark:border-gray-700 ">{{ __('frontend-labels.sponsor_ads.paid_at') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                        @forelse($ads_transactions as $txn)
                            <tr class="text-gray-700 dark:text-gray-400">
                                <td class="px-4 py-3 text-sm">
                                    {{ $txn->smartAd->name ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $txn->amount }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $txn->currency }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ ucfirst($txn->payment_gateway) }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $txn->transaction_id }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $txn->smartAd->smartAdsDetail->start_date }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $txn->smartAd->smartAdsDetail->end_date }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    <span
                                        class="px-2 py-1 font-semibold leading-tight 
                                            {{ $txn->status == 'success' ? 'text-green-700 bg-green-100' : 'text-red-700 bg-red-100' }} 
                                            rounded-full dark:bg-green-700 dark:text-green-100">
                                        {{ $txn->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    {{ $txn->paid_at ? $txn->paid_at->format('d M Y, h:i A') : '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="p-2 my-6 font-semibold text-gray-700 dark:text-gray-200 border dark:border-gray-700 ">
                                    {{ __('frontend-labels.sponsor_ads.no_transactions_found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($ads_transactions->hasPages())
                <div
                    class="grid px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase border-t dark:border-gray-700 bg-gray-50 sm:grid-cols-9 dark:text-gray-400 dark:bg-gray-800">
                    <span class="flex items-center col-span-3">
                        {{ __('frontend-labels.sponsor_ads.showing') }}
                        {{ $ads_transactions->firstItem() }}-{{ $ads_transactions->lastItem() }}
                        {{ __('frontend-labels.sponsor_ads.of') }} {{ $ads_transactions->total() }}
                    </span>
                    <span class="col-span-2"></span>
                    <div class="col-span-4 flex justify-end">
                        {{ $ads_transactions->onEachSide(2)->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
