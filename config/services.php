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

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'httpsms' => [
        'key' => env('HTTPSMS_KEY', env('HTTPSMS_API_KEY')),
        'from' => env('HTTPSMS_FROM', '+258862134230'),
    ],

    'sync' => [
        'cloud_url' => env('CLOUD_SYNC_URL', 'https://cloud.zbizplus.com/api/sync/ingest'),
        'token'     => env('CLOUD_SYNC_TOKEN', env('SYNC_TOKEN', 'zbiz_sync_default_token')),
    ],

    'mpesa' => [
        'env'                   => env('MPESA_ENV', 'sandbox'),
        'api_key'               => env('MPESA_API_KEY'),
        'public_key'            => env('MPESA_PUBLIC_KEY'),
        'service_provider_code' => env('MPESA_SERVICE_PROVIDER_CODE', '171717'),
        'webhook_secret'        => env('MPESA_WEBHOOK_SECRET'),
        'allowed_ips'           => array_values(array_filter(array_map('trim', explode(',', (string) env('MPESA_WEBHOOK_ALLOWED_IPS', ''))))),
    ],

    'health' => [
        'token' => env('HEALTH_CHECK_TOKEN'),
    ],

];
