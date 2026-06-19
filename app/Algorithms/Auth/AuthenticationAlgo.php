<?php

namespace App\Algorithms\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticationAlgo
{
    /**
     * @param string $guard
     */
    public function __construct(public string $guard)
    {
    }


    /**
     * @param Request $request
     * @param \Closure|null $authenticated
     *
     * @return \Illuminate\Http\JsonResponse|mixed|void
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function login(Request $request, \Closure $authenticated = null)
    {
        try {

            $token = Auth::guard($this->guard)->attempt($request->only('email', 'password'));
            if (!$token) {
                errCredentialIncorrect("Please check your email or password!!");
            }

            $user = Auth::guard($this->guard)->user();
            if (!$user) {
                errUnauthenticated("Can\'t get the user data!!");
            }

            if (!$user->account?->isActive) {
                errUnauthenticated("Your account is not active!!");
            }

            if ($authenticated != null) {
                $authenticated();
            }

            return success([
                'token' => $token,
                'type' => 'bearer'
            ]);

        } catch (\Error $exception) {
            exception($exception);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed|void
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function logout()
    {
        try {

            $user = Auth::guard($this->guard)->user();
            if (!$user) {
                errUnauthenticated("User not found");
            }

            Auth::logout();

            return success();

        } catch (\Error $error) {
            exception($error);
        }
    }
}
