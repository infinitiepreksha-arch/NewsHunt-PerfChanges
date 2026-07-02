<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    protected $fillable = [
        'gateway',
        'currency',
        'currency_symbol',
        'status',
        'stripe_secret',
        'stripe_publishable',
        'stripe_webhook_secret',
        'stripe_webhook_url',
        'razorpay_secret',
        'razorpay_key',
        'razorpay_webhook_secret',
        'razorpay_webhook_url',
        'apple_shared_secret',
        'apple_issuer_id',
        'apple_key_id',
        'apple_bundle_id',
        'apple_api_key_path',
        'apple_environment'
    ];
    public function getActiveGateway($gateway)
    {
        return PaymentSetting::where('gateway', $gateway)->where('status', true)->first();
    }
}
