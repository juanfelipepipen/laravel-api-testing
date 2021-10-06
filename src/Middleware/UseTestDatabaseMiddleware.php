<?php

namespace Pipen\ApiTesting\Middleware;

use Closure;
use Illuminate\Http\Request;
use Pipen\ApiTesting\Traits\DatabaseConfigs;

class UseTestDatabaseMiddleware
{
    use DatabaseConfigs;
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $this->setDatabaseConnection();

        return $next($request);
    }
}
