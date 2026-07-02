<?php
namespace App\Console\Commands;

use App\Mail\SmartAdStatusMail;
use App\Models\SmartAdDetail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ExpireSmartAds extends Command
{
    protected $signature   = 'ads:expire';
    protected $description = 'Handle expired and pending smart ads cleanup';

    public function handle()
    {
        /**
         * 3. Delete Pending Ads - if start_date already passed but still pending
         */
        $deletePendingAds = SmartAdDetail::where('ad_publish_status', 'pending')
            ->where('payment_status', 'pending')
            ->where('start_date', '<', now())
            ->get();

        foreach ($deletePendingAds as $ad) {
            Mail::to($ad->contact_email)
                ->queue(new SmartAdStatusMail($ad, 'deleted'));

            $ad->delete();

            $this->info("Ad ID {$ad->id} deleted (pending but start date already passed).");
        }

        // Output summary
        $this->info(count($expiredAds) . ' ads expired successfully.');
        $this->info(count($notYetStartAds) . ' ads kept pending (not reached start date).');
        $this->info(count($deletePendingAds) . ' pending ads deleted (start date passed).');
    }
}
