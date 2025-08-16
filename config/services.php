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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'whatsapp' => [
        'access_token' => env('WHATSAPP_ACCESS_TOKEN'),
        'phone_number_id' => env('WHATSAPP_PHONE_NUMBER_ID'),
        'business_account_id' => env('WHATSAPP_BUSINESS_ACCOUNT_ID'),
        'webhook_verify_token' => env('WHATSAPP_WEBHOOK_VERIFY_TOKEN'),
        'api_version' => env('WHATSAPP_API_VERSION', 'v18.0'),
    ],

    'twilio' => [
        'account_sid' => env('TWILIO_ACCOUNT_SID'),
        'auth_token' => env('TWILIO_AUTH_TOKEN'),
        'from_number' => env('TWILIO_FROM_NUMBER'),
        'verify_service_sid' => env('TWILIO_VERIFY_SERVICE_SID'),
        'whatsapp_from' => env('TWILIO_WHATSAPP_FROM', 'whatsapp:+14155238886'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'google_vision' => [
        'api_key' => env('GOOGLE_VISION_API_KEY'),
    ],

    'azure_vision' => [
        'api_key' => env('AZURE_VISION_API_KEY'),
        'endpoint' => env('AZURE_VISION_ENDPOINT'),
    ],

    'food_recognition' => [
        'api_key' => env('FOOD_RECOGNITION_API_KEY', 'demo'),
    ],

    'google_gemini' => [
        'api_key' => env('GOOGLE_GEMINI_API_KEY'),
        'model' => env('GOOGLE_GEMINI_MODEL', 'gemini-1.5-flash'),
        'max_tokens' => env('GOOGLE_GEMINI_MAX_TOKENS', 2048),
    ],

    'payvibe' => [
        'public_key' => env('PAYVIBE_PUBLIC_KEY'),
        'secret_key' => env('PAYVIBE_SECRET_KEY'),
        'base_url' => env('PAYVIBE_BASE_URL', 'https://payvibeapi.six3tech.com/api'),
        'test_mode' => env('PAYVIBE_TEST_MODE', true),
        'product_identifier' => env('PAYVIBE_PRODUCT_IDENTIFIER', 'fast'),
    ],

];
