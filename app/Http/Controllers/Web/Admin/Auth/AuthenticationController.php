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
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthenticationController extends Controller
{
    public function __construct(protected string $guard = 'web-admin')
    {
    }

    public function login(LoginRequest $request)
    {
        $algo = new AuthenticationAlgo($this->guard);
        return $algo->login($request);
    }

    public function profile()
    {
        $user = Auth::guard($this->guard)->user();
        if (!$user) {
            errUnauthenticated("User not found");
        }

        return success($user->account);
    }

    public function logout()
    {
        app(StaffSessionManager::class)->revokeByJti($this->currentJti());

        $algo = new AuthenticationAlgo($this->guard);
        return $algo->logout();
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $algo = new AuthenticationAlgo($this->guard);
        return $algo->changePassword($request);
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $algo = new AuthenticationAlgo($this->guard);
        return $algo->forgotPassword(StaffUser::class, $request);
    }

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
