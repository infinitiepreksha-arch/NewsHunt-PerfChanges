<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun'  => [
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme'   => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses'      => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'google'   => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('GOOGLE_REDIRECT_URI'),
    ],

    'applepay' => [
        'merchant_id'          => env('SERVICES_APPLEPAY_MERCHANT_ID'),
        'certificate_path'     => env('APPLE_PAY_CERT_PATH', storage_path('certificates/apple_pay_merchant_id.pem')),
        'private_key_path'     => env('APPLE_PAY_KEY_PATH', storage_path('certificates/apple_pay_merchant_id.key')),
        'certificate_password' => env('APPLE_PAY_CERT_PASSWORD', ''),
        'environment'          => env('APPLE_PAY_ENVIRONMENT', 'sandbox'), // sandbox or production
    ],

    'apple'    => [
        'shared_secret' => env('APPLE_SHARED_SECRET'),
    ],

];
