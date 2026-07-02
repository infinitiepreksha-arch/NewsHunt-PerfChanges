<?php
namespace App\Mail;

use App\Models\EmailTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SmartAdStatusMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $smartAdDetail;
    public $statusType;
    public $paymentLink;
    public $isExpired;
    public $template;

    public function __construct($smartAdDetail, $statusType, $paymentLink = null, $isExpired = false)
    {
        $this->smartAdDetail = $smartAdDetail;
        $this->paymentLink   = $paymentLink;
        $this->isExpired     = $isExpired;

        try {
            $this->template = EmailTemplate::where('type', 'sponsor')
                ->where('status', 'active')
                ->first();

        } catch (\Exception $e) {
            Log::warning('Could not load email template', [
                'type'          => 'sponsor',
                'smartAdDetail' => $smartAdDetail,
                'error'         => $e->getMessage(),
                'paymentlink'   => $paymentLink,
                'isExpired'     => $isExpired,
            ]);
            $this->template = null;
        }
    }

    public function build()
    {
        // Subject
        $subject = $this->template && $this->template->subject
            ? $this->template->subject
            : $this->template->subject;

        return $this->subject($subject)
            ->view('front_end.emails.smart_ad_status')
            ->with([
                'detail'      => $this->smartAdDetail,
                'paymentLink' => $this->paymentLink,
                'isExpired'   => $this->isExpired,
                'template'    => $this->template,
            ]);
    }

    public function failed(\Throwable $exception)
    {
        Log::error('SmartAdStatusMail job failed', [
            'recipient' => $this->smartAdDetail->contact_email ?? 'unknown',
            'error'     => $exception->getMessage(),
            'file'      => $exception->getFile(),
            'line'      => $exception->getLine(),
        ]);
    }
}
