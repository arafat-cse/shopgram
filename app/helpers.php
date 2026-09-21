<?php

if (!function_exists('coin_system_enabled')) {
    /**
     * Check if the coin/loyalty system is enabled.
     *
     * @return bool
     */
    function coin_system_enabled(): bool
    {
        return config('coins.enabled', true) === true;
    }
}
