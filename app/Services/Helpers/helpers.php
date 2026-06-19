<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
if (!function_exists('has_role_staff')) {

    /**
     * @param $name
     * @param $account
     * @param bool $useException
     *
     * @return bool
     */
    function has_role_staff($name, $account = null, bool $useException = true): bool
    {
        if (!$account) {
            $account = get_account();
            if (!$account) {
                if ($useException) {
                    errPermissionRestricted();
                }

                return false;
            }
        }

        if ($account->isSuperadmin) {
            return true;
        }

        if (!$account->hasRole($name)) {
            if ($useException) {
                errPermissionRestricted();
            }

            return false;
        }

        return true;
    }

}

if (!function_exists('has_permission_staff')) {

    /**
     * @param $name
     * @param $account
     * @param bool $useException
     *
     * @return bool
     */
    function has_permission_staff($name, $account = null, bool $useException = true): bool
    {
        if (!$account) {
            $account = get_account();
            if (!$account) {
                if ($useException) {
                    errPermissionRestricted();
                }

                return false;
            }
        }

        if ($account->isSuperadmin) {
            return true;
        }

        if (!$account->hasPermission($name)) {
            if ($useException) {
                errPermissionRestricted();
            }

            return false;
        }

        return true;
    }

}

if (!function_exists("created_by")) {

    /**
     * @return array
     */
    function created_by()
    {
        return request()->createdByAccount;
    }

}

if (!function_exists("get_auth")) {

    function get_auth()
    {
        $user = null;
        foreach (config('auth.guards') as $guard => $item) {
            $user = Auth::guard($guard)->user();
            if ($user) {
                break;
            }
        }

        if (!$user) {
            errUnauthenticated("User not found");
        }

        return $user;
    }

}

if (!function_exists("get_account")) {

    function get_account()
    {
        $user = get_auth();
        if (!$user) {
            return null;
        }

        return $user->account;
    }
}
