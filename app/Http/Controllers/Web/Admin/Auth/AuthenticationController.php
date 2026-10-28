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
use App\Services\Auth\StaffSessionManager;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
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
        app(StaffSessionManager::class)->revokeByJti($this->currentJti());

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

    public function sessions(StaffSessionManager $sessions)
    {
        return success($sessions->list(Auth::guard($this->guard)->id(), $this->currentJti()));
    }

    public function revokeSession(int $id, StaffSessionManager $sessions)
    {
        if (!$sessions->revoke(Auth::guard($this->guard)->id(), $id)) {
            error(404, "Session not found!!");
        }

        return success();
    }

    public function revokeOtherSessions(StaffSessionManager $sessions)
    {
        $count = $sessions->revokeOthers(Auth::guard($this->guard)->id(), $this->currentJti());

        return success(['revoked' => $count]);
    }

    private function currentJti(): ?string
    {
        return JWTAuth::getPayload()['jti'] ?? null;
    }

}
