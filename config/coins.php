<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Coin System Enabled
    |--------------------------------------------------------------------------
    |
    | This option controls whether the coin/loyalty system is enabled for customers.
    | When set to false, all coin-related UI and functionality will be hidden from
    | customers. Admin can still access coin management pages.
    |
    | Set via .env: COIN_SYSTEM_ENABLED=true
    |
    */
    'enabled' => env('COIN_SYSTEM_ENABLED', true),
];
