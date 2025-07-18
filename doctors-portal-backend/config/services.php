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

    'caretend' => [
    'api_key' => env('CARE_TEND_API_KEY'),
    'base_uri' => 'https://api.caretend.com/v3/', // confirm actual base URI in docs!
    ],

    'shipment_sheet' => [
    'api_key' => env('SHIPMENT_SHEET_API_KEY'),
    ],

    'microsoft_mail' => [
    'GRAPH_CLIENT_ID' => env('GRAPH_CLIENT_ID'),
    'GRAPH_CLIENT_SECRET' => env('GRAPH_CLIENT_SECRET'),
    ],
];
