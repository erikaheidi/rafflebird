<?php
#########################
## Configuration File
#########################

return [
    'app_path' => __DIR__ . '/app/Command',
    'debug' => true,

    # App Settings
    'ignore_usernames' => ['twitter', 'your_username'],
    'raffle_admin' => 'your_username', //the MustFollow criteria checks for this info to validate participation
    'raffle_rules' => [ 'App\\Rules\\DefaultRule' ],

    # Twitter API v2 Bearer Token
    'twitter_bearer_token' => 'TWITTER_BEARER_TOKEN',

    # Twitter API v1 Tokens
    'twitter_api_key' => 'TWITTER_API_KEY',
    'twitter_api_secret' => 'TWITTER_API_SECRET',
    'twitter_access_token' => 'USER_ACCESS_TOKEN',
    'twitter_token_secret' => 'USER_ACCESS_TOKEN_SECRET',
];