<?php

namespace App\Services\System;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;

class SystemInformationService
{
    /**
     * @return array
     */
    public function get(): array
    {
        return [
            'system' => $this->getSystemEnv(),
            'server' => $this->getServerEnv(),
            'package' => $this->getPackages(),
            'email' => $this->getEmailEnv(),
        ];
    }

    /*
     |--------------------------------------------------------------------------
     | Functions
     |-------------------------------------------------------------------------
     */

    private function getSystemEnv(): array
    {
        return [
            'laravelVersion' => App::version(),
            'appVersion' => config('app.version'),
            'currency' => config('fixer.default_currency'),
            'timezone' => config('app.timezone'),
            'debugTime' => env('LOG_CHANNEL'),
            'debugMode' => config('app.debug'),
            'storageDirWritable' => is_writable(base_path('storage')),
            'cacheDirWritable' => is_writable(base_path('bootstrap/cache')),
            'appSize' => $this->humanFileSize($this->folderSize(base_path())),
        ];
    }

    private function getServerEnv(): array
    {
        return [
            'version' => phpversion(),
            'serverSoftware' => Arr::get($_SERVER, 'SERVER_SOFTWARE'),
            'serverOS' => php_uname(),
            'databaseConnectionName' => config('database.default'),
            'sslInstalled' => $this->checkSslIsInstalled(),
            'cacheDriver' => config('cache.default'),
            'sessionDriver' => config('session.driver'),
            'mbstring' => extension_loaded('mbstring'),
            'openssl' => extension_loaded('openssl'),
            'curl' => extension_loaded('curl'),
            'exif' => extension_loaded('exif'),
            'pdo' => extension_loaded('pdo'),
            'fileinfo' => extension_loaded('fileinfo'),
            'tokenizer' => extension_loaded('tokenizer'),
        ];
    }

    /**
     * NOTE: matches the VBM reference exactly, including exposing raw mail
     * credentials. Restrict access to this endpoint (STAFF_SYSTEM_INFORMATION_VIEW)
     * to trusted/superadmin roles only.
     */
    private function getEmailEnv(): array
    {
        return [
            'driver' => env('MAIL_MAILER'),
            'host' => env('MAIL_HOST'),
            'port' => env('MAIL_PORT'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'encryption' => env('MAIL_ENCRYPTION'),
            'senderEmail' => env('MAIL_FROM_ADDRESS'),
            'senderName' => env('MAIL_FROM_NAME'),
        ];
    }

    private function getPackages(): array
    {
        $composerRequire = $this->getComposerData()['require'] ?? [];

        $packages = [];
        foreach ($composerRequire as $key => $value) {
            if ($key === 'php') {
                continue;
            }

            $packageFile = base_path('/vendor/' . $key . '/composer.json');
            if (!file_exists($packageFile)) {
                continue;
            }

            $packages[] = [
                'name' => $key,
                'version' => $value,
            ];
        }

        return $packages;
    }

    /*
     |--------------------------------------------------------------------------
     | Sub Functions
     |-------------------------------------------------------------------------
     */

    private function humanFileSize($bytes, $precision = 2): string
    {
        $units = ['B', 'kB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return number_format($bytes, $precision, ',', '.') . ' ' . $units[$pow];
    }

    private function folderSize(string $dir): int
    {
        $size = 0;
        foreach (glob(rtrim($dir, '/') . '/*', GLOB_NOSORT) ?: [] as $each) {
            $size += is_file($each) ? filesize($each) : $this->folderSize($each);
        }

        return $size;
    }

    private function checkSslIsInstalled(): bool
    {
        return !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off';
    }

    private function getComposerData(): array
    {
        $file = base_path('composer.json');
        if (!file_exists($file)) {
            return [];
        }

        return json_decode(file_get_contents($file), true) ?? [];
    }
}
