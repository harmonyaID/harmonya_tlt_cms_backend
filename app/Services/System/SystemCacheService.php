<?php

namespace App\Services\System;

use Illuminate\Support\Facades\Artisan;

class SystemCacheService
{
    const ACTIONS = [
        'cache-clear' => 'cache:clear',
        'config-clear' => 'config:clear',
        'route-clear' => 'route:clear',
        'view-clear' => 'view:clear',
        'optimize' => 'optimize',
        'optimize-clear' => 'optimize:clear',
        'queue-restart' => 'queue:restart',
    ];

    const LABELS = [
        'cache-clear' => 'Application cache cleared',
        'config-clear' => 'Configuration cache cleared',
        'route-clear' => 'Route cache cleared',
        'view-clear' => 'Compiled views cleared',
        'optimize' => 'Application optimized (config, route & view cached)',
        'optimize-clear' => 'All caches cleared (config, route, view & application)',
        'queue-restart' => 'Queue workers will restart after their current job',
    ];

    public function run(string $action): array
    {
        $command = self::ACTIONS[$action];

        Artisan::call($command);

        return [
            'message' => self::LABELS[$action],
            'output' => trim(Artisan::output()),
        ];
    }

    public function available(): array
    {
        $results = [];
        foreach (self::ACTIONS as $key => $command) {
            $results[] = ['action' => $key, 'label' => self::LABELS[$key]];
        }

        return $results;
    }
}
