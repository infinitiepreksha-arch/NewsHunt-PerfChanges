<?php
namespace App\Http\Livewire;

use App\Models\SmartAd;
use App\Models\SmartAdTracking;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Livewire\Component;

class MyAdReport extends Component
{
    public $totalClicksToday     = 0;
    public $totalClicksYesterday = 0;
    public $totalClicks7Days     = 0;
    public $totalClicksThisMonth = 0;

    public $reportStartDate;
    public $reportEndDate;
    public $clicksPerDate = [];
    public $clicksPerAd   = [];

    public function mount()
    {
        // Set default date range (last 7 days)
        $this->reportStartDate = Carbon::now()->subDays(7)->format('Y-m-d');
        $this->reportEndDate   = Carbon::now()->format('Y-m-d');

        $this->loadCardData();
        $this->calculateClicksReport();
    }

    public function render()
    {
        return view('livewire.my-ad-report');
    }


    private function loadCardData()
    {
        $userId = auth()->id();

        // Get all tracking records for the user
        $allTrackingRecords = SmartAdTracking::whereHas('smartAd.smartAdsDetail', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })->get();

        // Initialize counters
        $this->totalClicksToday     = 0;
        $this->totalClicksYesterday = 0;
        $this->totalClicks7Days     = 0;
        $this->totalClicksThisMonth = 0;

        $today        = Carbon::today()->format('Y-m-d');
        $yesterday    = Carbon::yesterday()->format('Y-m-d');
        $sevenDaysAgo = Carbon::today()->subDays(6)->format('Y-m-d');
        $currentMonth = Carbon::now()->month;
        $currentYear  = Carbon::now()->year;

        foreach ($allTrackingRecords as $tracking) {
            $adClicks = $tracking->ad_clicks;

            // Handle JSON decode if needed
            if (is_string($adClicks)) {
                $adClicks = json_decode($adClicks, true);
            }

            if (is_array($adClicks)) {
                // Check if this record has clicks for specific dates
                $hasClicksToday     = false;
                $hasClicksYesterday = false;
                $hasClicksLast7Days = false;
                $hasClicksThisMonth = false;

                foreach ($adClicks as $click) {
                    if (isset($click['timestamp'])) {
                        $clickDate       = Carbon::parse($click['timestamp']);
                        $clickDateString = $clickDate->format('Y-m-d');

                        if ($clickDateString === $today) {
                            $hasClicksToday = true;
                        }

                        if ($clickDateString === $yesterday) {
                            $hasClicksYesterday = true;
                        }

                        if ($clickDateString >= $sevenDaysAgo && $clickDateString <= $today) {
                            $hasClicksLast7Days = true;
                        }

                        if ($clickDate->month === $currentMonth && $clickDate->year === $currentYear) {
                            $hasClicksThisMonth = true;
                        }
                    }
                }

                // Add totalClicks if record has clicks for the respective periods
                if ($hasClicksToday) {
                    $this->totalClicksToday += $tracking->totalClicks;
                }

                if ($hasClicksYesterday) {
                    $this->totalClicksYesterday += $tracking->totalClicks;
                }

                if ($hasClicksLast7Days) {
                    $this->totalClicks7Days += $tracking->totalClicks;
                }

                if ($hasClicksThisMonth) {
                    $this->totalClicksThisMonth += $tracking->totalClicks;
                }
            }
        }
    }
    public function calculateClicksReport()
    {
        $userId    = auth()->id();
        $date_from = Carbon::parse($this->reportStartDate)->startOfDay();
        $date_to   = Carbon::parse($this->reportEndDate)->endOfDay();

        $smartAdTracking = SmartAdTracking::whereBetween('created_at', [$date_from, $date_to])
            ->whereHas('smartAd.smartAdsDetail', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->get();

        // Calculate clicks per date from JSON data
        $dateClicksCollection = collect();

        foreach ($smartAdTracking as $tracking) {
            $adClicks = $tracking->ad_clicks;

            // Handle JSON decode if needed
            if (is_string($adClicks)) {
                $adClicks = json_decode($adClicks, true);
            }

            if (is_array($adClicks)) {
                foreach ($adClicks as $click) {
                    if (isset($click['timestamp'])) {
                        // Parse timestamp and get date
                        $clickDate = Carbon::parse($click['timestamp'])->format('Y-m-d');

                        // Count clicks per date
                        if ($dateClicksCollection->has($clickDate)) {
                            $dateClicksCollection[$clickDate] = $dateClicksCollection[$clickDate] + 1;
                        } else {
                            $dateClicksCollection[$clickDate] = 1;
                        }
                    }
                }
            }
        }

        // Create period and fill missing dates with 0
        $period = CarbonPeriod::create($this->reportStartDate, $this->reportEndDate);
        $result = collect();

        foreach ($period as $p) {
            $dateString = $p->format('Y-m-d');
            if ($dateClicksCollection->has($dateString)) {
                $result[$dateString] = $dateClicksCollection->get($dateString);
            } else {
                $result[$dateString] = 0;
            }
        }

        $this->clicksPerDate = $result->toArray();

        // Calculate clicks per ad from JSON data
        $this->clicksPerAd = [];
        $adClicksSum       = [];

        foreach ($smartAdTracking as $tracking) {
            $adClicks = $tracking->ad_clicks;

            // Handle JSON decode if needed
            if (is_string($adClicks)) {
                $adClicks = json_decode($adClicks, true);
            }

            if (is_array($adClicks)) {
                                                           // Get ad slug from the tracking record or determine from context
                                                           // You might need to adjust this based on your data structure
                $adSlug = $tracking->ad_slug ?? 'unknown'; // Adjust this field name

                if (! isset($adClicksSum[$adSlug])) {
                    $adClicksSum[$adSlug] = 0;
                }

                // Count total clicks for this ad
                $adClicksSum[$adSlug] += count($adClicks);
            }
        }

        // Build final array with ad names
        $ads = SmartAd::all();
        foreach ($ads as $ad) {
            $clicks              = $adClicksSum[$ad->slug] ?? 0;
            $this->clicksPerAd[] = [
                'name'   => $ad->name,
                'slug'   => $ad->slug,
                'clicks' => $clicks,
            ];
        }

        // Sort by clicks descending
        usort($this->clicksPerAd, function ($a, $b) {
            return $b['clicks'] - $a['clicks'];
        });

        $this->dispatch('renderChart');
    }
    public function updatedReportStartDate()
    {
        $this->validateDateRange();
        $this->calculateClicksReport();
    }

    public function updatedReportEndDate()
    {
        $this->validateDateRange();
        $this->calculateClicksReport();
    }

    private function validateDateRange()
    {
        if ($this->reportStartDate && $this->reportEndDate) {
            $startDate = Carbon::parse($this->reportStartDate);
            $endDate   = Carbon::parse($this->reportEndDate);

            if ($startDate->gt($endDate)) {
                $this->reportEndDate = $this->reportStartDate;
            }
        }
    }

    public function refreshData()
    {
        $this->loadCardData();
        $this->calculateClicksReport();

        $this->dispatch('dataRefreshed');
    }
}
