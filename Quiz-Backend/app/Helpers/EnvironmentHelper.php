<?php

if (!function_exists('getEnvironmentUrl')) {
    function getEnvironmentUrl()
    {
        $env = config('app.env');

        return match ($env) {
            'local' => 'http://localhost:5173',
            'staging' => 'https://dev.play.passafund.com',
            'production' => 'https://play.passafund.com',
            default => 'Unknown env',
        };
    }
}
