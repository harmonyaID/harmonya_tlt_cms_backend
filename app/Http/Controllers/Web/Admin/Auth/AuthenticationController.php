<?php

namespace App\Http\Controllers\Web\Admin\Auth;

use App\Algorithms\Auth\AuthenticationAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ChangePasswordRequest;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\Staff\StaffUser;
use App\Models\Staff\StaffUserPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticationController extends Controller
{
    public function __construct(protected string $guard = 'web-admin')
    {
    }

    /**
     * @param LoginRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function login(LoginRequest $request)
    {
        $algo = new AuthenticationAlgo($this->guard);
        return $algo->login($request);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed|null
     */
    public function profile()
    {
        $user = Auth::guard($this->guard)->user();
        if (!$user) {
            errUnauthenticated("User not found");
        }

        return success($user->account);
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function logout()
    {
        $algo = new AuthenticationAlgo($this->guard);
        return $algo->logout();
    }

    /**
     * @param ChangePasswordRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $algo = new AuthenticationAlgo($this->guard);
        return $algo->changePassword($request);
    }

    /**
     * @param ForgotPasswordRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $algo = new AuthenticationAlgo($this->guard);
        return $algo->forgotPassword(StaffUser::class, $request);
    }

    /**
     * @param $token
     * @param ResetPasswordRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|null
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function resetPassword($token, ResetPasswordRequest $request)
    {
        $algo = new AuthenticationAlgo($this->guard);
        return $algo->resetPassword(StaffUserPassword::class, $token, $request);
    }

}
