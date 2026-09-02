<?php

return [
    'issuer' => env('LICENSE_ISSUER', 'zbiz-plus-owner'),
    'signing_key' => env('LICENSE_SIGNING_KEY', env('APP_KEY')),
    'clock_skew_minutes' => (int) env('LICENSE_CLOCK_SKEW_MINUTES', 10),
];
