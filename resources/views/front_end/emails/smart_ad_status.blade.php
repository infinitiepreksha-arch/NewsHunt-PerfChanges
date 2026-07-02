<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $template?->title ?? 'Smart Ad Notification' }}</title>
</head>

<body
    style="max-width: {{ $template?->layout_width ?? 600 }}px; margin:auto; font-family:Arial, sans-serif; background:#f9f9f9; padding:20px; border-radius:8px;">

    {{-- Logo --}}
    @if ($template && $template->logo)
        <div style="text-align:center; margin-top:20px;">
            <img src="{{ asset('storage/' . $template->logo) }}" alt="Logo" height="60">
        </div>
    @endif

    {{-- Subject / Header --}}
    <h2 style="color:#333; text-align:center;">
        {{ $template?->subject ?? 'Smart Ad Notification' }}
    </h2>

    <div style="margin:20px 0; line-height:1.6; color:#444;">
        {!! $template->html_content !!}
    </div>
    {{-- Fallback messages based on status --}}
    @if ($smartAdDetail->ad_publish_status === 'pending')
        <p>Dear {{ $smartAdDetail->smartAd->name }}</p>
        <p>Your advertisement request has been received and is currently <strong>pending review</strong>.
            Our team will notify you once it is approved or rejected.</p>
    @elseif ($smartAdDetail->ad_publish_status === 'approved')
        <p>Dear {{ $smartAdDetail->smartAd->name }}</p>
        <p>Good news 🎉 Your advertisement request has been <strong>approved</strong>.
            Please complete the payment within the reject deadline.</p>

        {{-- Buttons --}}
        <div style="text-align:center; margin:25px 0;">
            {{-- <a href="{{ $paymentUrl ?? '#' }}"
                style="background-color:#388e3c; color:#fff; padding:12px 24px; text-decoration:none;
               font-size:16px; border-radius:5px; display:block; width:100%; max-width:280px;
               margin:10px auto;">
                Complete Payment
            </a> --}}
            <a href="{{ route('smart-ads-index-page') }}"
                style="background-color:#d32f2f; color:#fff; padding:12px 24px; text-decoration:none;
               font-size:16px; border-radius:5px; display:block; width:100%; max-width:280px;
               margin:10px auto;">
                Go to Payment Page
            </a>
        </div>
    @elseif ($smartAdDetail->ad_publish_status === 'rejected' && $smartAdDetail->payment_status === 'failed')
        <p>Dear {{ $smartAdDetail->smartAd->name }},</p>
        <p>We regret to inform you that your advertisement request has been <strong>rejected</strong> and the payment
            was <strong>unsuccessful</strong>.</p>
        <p>You may resubmit your advertisement request.</p>
    @else
        <p>Dear {{ $smartAdDetail->smartAd->name }}</p>
        <p>Your advertisement status has been updated. Please check your dashboard for details.</p>
    @endif

    {{-- Closing --}}
    @if (!empty($template?->closing))
        <p style="margin-top:20px;">{{ $template->closing }}</p>
    @endif

    {{-- Signature --}}
    @if (!empty($template?->signature))
        <p style="font-weight:bold; margin-top:5px;">{{ $template->signature }}</p>
    @endif

    {{-- Footer --}}
    <hr style="margin:20px 0;">
    <p style="font-size:12px; color:#777; text-align:center;">
        {{ $template?->footer_text ?? 'This is an automated message. Please do not reply.' }}
    </p>
</body>

</html>
