<?php

namespace imbalance\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{

    public function handle($request, \Closure $next)
    {
        if ( ! $request->is('api/*'))
        {
            return parent::handle($request, $next);
        }

        return $next($request);
    }

    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        'api/*',
        '/dashboard'
    ];
}
