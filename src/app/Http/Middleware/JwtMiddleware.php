<?php

namespace App\Http\Middleware;

use Closure;
use Symfony\Component\HttpFoundation\Response;

use App\Jwt;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $valid = Jwt::Verify($request->input("jwt"))

        if ($valid) {
            return $next($request);
        }

        return response()->json(
            [
                'error' => 1
            ],
            Response::HTTP_UNAUTHORIZED
        );
    }
}
