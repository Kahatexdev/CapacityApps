<?php

/**
 * Global API URL Resolver
 */
function api_url(string $key = 'material'): string
{
    $app = config('App');

    return match ($key) {
        'material' => $app->materialApiUrl,
        'hris' => $app->hrisApiUrl,
        'tls' => $app->tlsApiUrl,
        default     => '',
    };
}
