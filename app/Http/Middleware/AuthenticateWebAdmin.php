<?php

namespace App\Http\Middleware;

use App\Http\Middleware\Traits\HasAuthenticationMiddleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateWebAdmin
{
    use HasAuthenticationMiddleware;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('web-admin')->check()) {
            errUnauthenticated();
        }

        $token = JWTAuth::getToken();
        if (!$token) {
            errUnauthenticated("Unable to get token");
        }

        $payload = JWTAuth::decode($token);
        if (!$payload) {
            errUnauthenticated("Unable to decode token");
        }

        if ($payload['url'] != request()->getHttpHost() . '/' . config('core.login.web.admin')) {
            errUnauthenticated("This token is not for you!");
        }

        $account = $this->setAccount('web-admin');

        $request->createdByAccount = $this->setCreatedBy('web-admin', $account);
        $request->authStaffId = $account->id;

        
        return $next($request);
    }
}
