<?php

namespace Pipen\ApiTesting\Traits\TemporarilyStore;

use Illuminate\Support\Facades\Http;

trait MaintenanceMode
{
    /**
     * Store maintenance mode cookie
     *
     * @param string $secret
     *
     * @return void
     */
    public function storeMaintenanceModeCookie(string $secret): void
    {
        $url      = url($secret);
        $response = Http::get($url);
        $cookie   = $response->cookies()->getCookieByName('laravel_maintenance');

        # Storage cookie
        session(['laravel_maintenance' => [
            'key'    => $secret,
            'cookie' => [
                'Value'  => $cookie->getValue(),
                'Domain' => $cookie->getDomain()
            ]
        ]]);
    }

    /**
     * Get laravel maintenance mode cookie value
     *
     * @return array|null
     */
    public function getMaintenanceModeCookie(): array | null
    {
        return session('laravel_maintenance');
    }
}
