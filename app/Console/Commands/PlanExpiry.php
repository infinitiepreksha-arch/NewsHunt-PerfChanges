<?php
namespace App\Console\Commands;

use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PlanExpiry extends Command
{
    protected $signature   = 'expired:plan';
    protected $description = 'Expired Plans';

    public function handle()
    {
        $today = Carbon::today();

        $expiredSubscriptions = Subscription::where('end_date', '<', $today)
            ->where('status', '!=', 'expired')
            ->get();

        Log::info("Plan Expired Successfully!!!");

        foreach ($expiredSubscriptions as $subscription) {
            $subscription->status = 'expired';
            $subscription->save();
        }
    }
}
