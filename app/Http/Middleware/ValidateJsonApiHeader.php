<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ValidateJsonApiHeader
{
    public function handle(Request $request, Closure $next)
    {

        if ($request->header('accept') !== 'application/vnd.api+json') {
            throw new HttpException(406, 'Not acceptable');
        }

        if ($request->isMethod('POST') || $request->isMethod('patch')) {
            if ($request->header('content-type') !== 'application/vnd.api+json') {
                throw new HttpException(415, 'Unsupported media type');
            }
        }

        return $next($request)->withHeaders([
            'content-type' => 'application/vnd.api+json'
        ]);
    }
}
