<?php

namespace App\Models;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

trait HasAuthentication
{
    /**
     * @param string $email
     * @param $userId
     *
     * @return bool
     */
    public static function emailAlreadyUsed(string $email, $userId = null)
    {
        return self::where('email', $email)
            ->where(function ($query) use ($userId) {
                if ($userId) {
                    $query->where('id', '!=', $userId);
                }
            })->exists();
    }

    /**
     * @param $password
     *
     * @return void
     */
    public function changePassword($password)
    {
        $this->password = Hash::make($password);
        $this->save();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function addTokenPassword()
    {
        return $this->passwords()->firstOrCreate([], [
            'token' => Str::random(60),
            'expiredDate' => now()->addHour()
        ]);
    }
}
