<?php

namespace App\Http\Controllers\Web\Admin\Notification;

use App\Http\Controllers\Controller;
use App\Models\Notification\NotificationSetting;
use Illuminate\Http\Request;

class NotificationSettingController extends Controller
{
    public function get()
    {
        NotificationSetting::ensureAll();

        return success(NotificationSetting::orderBy('id')->get()->map(fn ($s) => $this->parse($s))->all());
    }

    public function update($id, Request $request)
    {
        $request->validate([
            'isActive' => 'nullable|boolean',
            'sendInApp' => 'nullable|boolean',
            'sendPush' => 'nullable|boolean',
        ]);

        $setting = NotificationSetting::find($id);

        if (!$setting) {
            error(404, "Notification setting not found!!");
        }

        $setting->update($request->only(['isActive', 'sendInApp', 'sendPush']));

        return success($this->parse($setting->fresh()));
    }

    private function parse(NotificationSetting $s): array
    {
        return [
            'id' => $s->id,
            'type' => $s->type,
            'name' => $s->name,
            'isActive' => $s->isActive,
            'sendInApp' => $s->sendInApp,
            'sendPush' => $s->sendPush,
        ];
    }
}
