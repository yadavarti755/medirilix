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

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'sms_gateway' => [
        'url'             => env('SMS_GATEWAY_URL', 'https://hydgw.sms.gov.in/failsafe/MLink'),
        'username'        => env('SMS_GATEWAY_USERNAME', ''),
        'pin'             => env('SMS_GATEWAY_PIN', ''),
        'signature'       => env('SMS_GATEWAY_SIGNATURE', ''),
        'dlt_entity_id'   => env('SMS_DLT_ENTITY_ID', ''),
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URL'),
    ],

    'shiprocket' => [
        'email' => env('SHIPROCKET_EMAIL'),
        'password' => env('SHIPROCKET_PASSWORD'),
    ],

    'dhl' => [
        'api_key' => env('DHL_API_KEY'),
        'base_url' => env('DHL_API_BASE_URL', 'https://api-eu.dhl.com'),
    ],

];
