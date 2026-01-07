<?php

/**
 * Global API URL Resolver
 */
function api_url(string $key = 'complaint'): string
{
    $app = config('App');

    return match ($key) {
        'complaint' => $app->complaintApiUrl,
        'material' => $app->materialApiUrl,
        'hris' => $app->hrisApiUrl,
        'tls' => $app->tlsApiUrl,
        default     => '',
    };
}
