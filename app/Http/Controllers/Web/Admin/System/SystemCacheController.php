<?php

namespace App\Http\Controllers\Web\Admin\System;

use App\Http\Controllers\Controller;
use App\Services\Constant\Access\AccessPermissionName;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Support\Facades\Artisan;

class SystemCacheController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {

            $this->middleware(function ($request, $next) {
                has_permission_staff(AccessPermissionName::STAFF_SYSTEM_CACHE_RUN);
                return $next($request);
            })->only([
                'artisanCache',
                'artisanConfig',
                'artisanRoute',
                'artisanView',
                'artisanOptimize',
                'artisanOptimizeClear',
                'artisanQueueRestart',
            ]);
        }
    }

    public function artisanCache()
    {
        Artisan::call("cache:clear");
        $this->logRun('cache:clear');

        return success(null, 'Application cache cleared!');
    }

    public function artisanConfig()
    {
        Artisan::call("config:clear");
        $this->logRun('config:clear');

        return success(null, 'Configuration cache cleared!');
    }

    public function artisanRoute()
    {
        Artisan::call("route:clear");
        $this->logRun('route:clear');

        return success(null, 'Route cache cleared!');
    }

    public function artisanView()
    {
        Artisan::call("view:clear");
        $this->logRun('view:clear');

        return success(null, 'Compiled view cleared!');
    }

    public function artisanOptimize()
    {
        Artisan::call("optimize");
        $this->logRun('optimize');

        $internalMsg = [
            'Configuration cache cleared!',
            'Configuration cached successfully!',
            'Route cache cleared!',
            'Routes cached Successfully!',
            'Files cached successfully!',
        ];

        return success(null, json_encode($internalMsg));
    }

    public function artisanOptimizeClear()
    {
        // NOTE: the VBM reference calls the non-existent `Artisan::csall(...)` here,
        // which is a typo bug that would fatal-error at runtime. Using the correct
        // `Artisan::call(...)` so this endpoint actually works.
        Artisan::call("optimize:clear");
        $this->logRun('optimize:clear');

        $internalMsg = [
            'Compiled views cleared!',
            'Application cache cleared!',
            'Route cache cleared!',
            'Configuration cache cleared!',
            'Compiled services and packages files removed!',
            'Caches cleared successfully!',
        ];

        return success(null, json_encode($internalMsg));
    }

    public function artisanQueueRestart()
    {
        Artisan::call('queue:restart');
        $this->logRun('queue:restart');

        return success(null, 'Broadcasting queue restart signal.');
    }

    /*
     |--------------------------------------------------------------------------
     | Functions
     |-------------------------------------------------------------------------
     */

    private function logRun(string $command): void
    {
        activity()->setCausedBy()
            ->setType(ActivityType::SYSTEM_CACHE)
            ->setAction(ActivityAction::UPDATE)
            ->log("Ran maintenance action: " . $command);
    }
}
