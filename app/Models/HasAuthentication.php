<?php

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

trait HasAuthentication
{
    public static function emailAlreadyUsed(string $email, $userId = null)
    {
        return self::where('email', $email)
            ->where(function ($query) use ($userId) {
                if ($userId) {
                    $query->where('id', '!=', $userId);
                }
            })->exists();
    }

    public function changePassword($password)
    {
        $this->password = Hash::make($password);
        $this->save();
    }

    public function addTokenPassword()
    {
        return $this->passwords()->firstOrCreate([], [
            'token' => Str::random(60),
            'expiredDate' => now()->addDays(30)
        ]);
    }
}
