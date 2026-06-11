<?php

namespace App\Services\Misc;

use Closure;
use Illuminate\Support\Facades\DB;

class DBMultipleTransaction
{
    public static function beginTransaction(array $connections, Closure $callback)
    {
        $activeConnections = [];

        try {
            foreach ($connections as $conn) {
                DB::connection($conn)->beginTransaction();
                $activeConnections[] = $conn;
            }

            $result = $callback();

            foreach ($activeConnections as $conn) {
                DB::connection($conn)->commit();
            }
        } catch (\Error $e) {
            foreach ($activeConnections as $conn) {
                DB::connection($conn)->rollBack();
            }
            exception($e);
        }

        return $result;
    }

}
