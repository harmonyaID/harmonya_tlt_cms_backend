<?php

namespace App\Http\Controllers\Web\Admin\Setting;

use App\Algorithms\Setting\SettingNotificationCredentialAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\SettingNotificationCredentialRequest;
use App\Models\Setting\SettingNotificationCredential;
use App\Parser\Setting\SettingNotificationCredentialParser;
use App\Services\Constant\Access\AccessPermissionName;
use App\Services\Constant\Setting\FirebaseCredential;
use App\Services\Constant\Setting\NotificationProvider;
use App\Services\Constant\Setting\PostmarkCredential;
use Illuminate\Http\Request;

class SettingNotificationCredentialController extends Controller
{
    public function __construct()
    {
        if (config('auth.with-permission')) {
            $this->middleware(function ($request, $next) {
                has_permission_staff(
                    AccessPermissionName::STAFF_SETTING_VIEW
                );

                return $next($request);
            })->only([
                'get',
                'detail',
            ]);

            $this->middleware(function ($request, $next) {
                has_permission_staff(
                    AccessPermissionName::STAFF_SETTING_UPDATE
                );

                return $next($request);
            })->only([
                'create',
                'update',
                'delete',
            ]);
        }
    }

    public function get(Request $request)
    {
        $settings = SettingNotificationCredential::filter($request)
            ->getOrPaginate($request);

        return success(
            SettingNotificationCredentialParser::briefs($settings),
            pagination: pagination($settings)
        );
    }

    public function detail($id)
    {
        $setting = SettingNotificationCredential::find($id);

        if (!$setting) {
            errSettingNotificationCredentialGet();
        }

        return success(
            SettingNotificationCredentialParser::first($setting)
        );
    }

    public function create(SettingNotificationCredentialRequest $request)
    {
        $algo = new SettingNotificationCredentialAlgo();

        return $algo->create($request);
    }

    public function update(
        $id,
        SettingNotificationCredentialRequest $request
    ) {
        $algo = new SettingNotificationCredentialAlgo((int) $id);

        return $algo->update($request);
    }

    public function delete($id)
    {
        $algo = new SettingNotificationCredentialAlgo((int) $id);

        return $algo->delete();
    }

    public function providerType()
    {
        return success(NotificationProvider::get());
    }

    public function postmark()
    {
        return success(PostmarkCredential::OPTION);
    }

    public function firebase()
    {
        return success(FirebaseCredential::OPTION);
    }
}
