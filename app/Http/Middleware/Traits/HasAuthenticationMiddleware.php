<?php

namespace App\Http\Middleware\Traits;

use Illuminate\Support\Facades\Auth;

trait HasAuthenticationMiddleware
{
    protected function setAccount($guarded)
    {
        if ($user = Auth::guard($guarded)->user()) {
            if ($account = $user->account) {
                return $account;
            }
        }

        return null;
    }

    protected function setCreatedBy($guarded, $account = null): array
    {
        if (!$account) {
            if ($user = Auth::guard($guarded)->user()) {
                $account = $user->account;
                if (!$account) {
                    return [];
                }
            }
        }

        return [
            'createdBy' => $account->id,
            'createdByName' => $account->fullName
        ];
    }
}
