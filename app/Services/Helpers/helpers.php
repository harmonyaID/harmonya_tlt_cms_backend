<?php

use App\Models\Language\Translation;
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

if (!function_exists('filename')) {

    /**
     * @param $file
     * @param $text
     *
     * @return string
     */
    function filename($file, $text)
    {
        return Str::random(20) . str_shuffle(str_replace(' ', '', $text)) . '.' . $file->getClientOriginalExtension();
    }

}

if (!function_exists("translations")) {

    /**
     * @param $key
     *
     * @return array|mixed
     */
    function translations($key)
    {
        $translations = config('app.translations');

        if (count($translations) > 0) {
            $translation = collect($translations)->where('key', $key)->first();
        } else {
            $translation = Translation::findByKey($key);
        }

        return optional($translation)->translations ?: [];
    }

}

if (!function_exists("locale")) {

    /**
     * @return \Illuminate\Config\Repository|\Illuminate\Contracts\Foundation\Application|mixed
     */
    function locale()
    {
        return config('app.locale') ?: config('app.fallback_locale');
    }

}