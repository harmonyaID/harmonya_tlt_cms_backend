<?php

namespace App\Algorithms\Auth;

use App\Mail\Auth\AuthForgotPasswordMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

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

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|void
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function changePassword(Request $request)
    {
        try {

            $user = Auth::guard($this->guard)->user();
            if (!$user) {
                errUnauthenticated("User not found");
            }

            if ($user->password) {
                if (!Hash::check($request->oldPassword, $user->password)) {
                    errAuthChangePassword("Your old password is wrong!!");
                }
            }

            $user->changePassword($request->newPassword);

            return success();

        } catch (\Error $error) {
            exception($error);
        }
    }

    /**
     * @param $model
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|void
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function forgotPassword($model, Request $request)
    {
        try {

            $user = $model::where('email', $request->email)->first();
            if (!$user) {
                errAuthUserGet("Please check your email");
            }

            $account = $user->account;
            if (!$account) {
                errAuthAccountGet("Account not found");
            }

            $tokenPassword = $user->addTokenPassword();
            if (!$tokenPassword) {
                errAuthForgotPassword();
            }

            if (App::isProduction()) {
                $mail = Mail::driver('postmark');
            } else {
                $mail = Mail::driver('smtp-mailtrap');
            }
            $message = new AuthForgotPasswordMail($account, $tokenPassword);
            $mail->to($user->email)
                ->queue($message);

            return success();

        } catch (\Error $error) {
            exception($error);
        }
    }

    /**
     * @param $model
     * @param $token
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|void
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function resetPassword($model, $token, Request $request)
    {
        try {

            $userPassword = $model::where('token', $token)->first();
            if (!$userPassword) {
                errAuthForgotPasswordGet();
            }

            $user = $userPassword->user;
            if (!$user) {
                errAuthUserGet();
            }

            DB::transaction(function () use ($user, $request, $userPassword) {

                $user->changePassword($request->password);

                $userPassword->delete();

            });

            return success();

        } catch (\Error $error) {
            exception($error);
        }
    }

}
