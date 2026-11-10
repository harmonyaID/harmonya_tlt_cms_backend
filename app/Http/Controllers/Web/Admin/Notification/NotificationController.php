<?php

namespace App\Http\Controllers\Web\Admin\Notification;

use App\Http\Controllers\Controller;
use App\Models\Notification\StaffNotification;
use App\Models\Notification\StaffUserDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function get(Request $request)
    {
        $notifications = $this->mine()
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->when($request->boolean('unread'), fn ($q) => $q->whereNull('readAt'))
            ->orderByDesc('id')
            ->getOrPaginate($request);

        return success($notifications->map(fn ($n) => $this->parse($n))->all(), pagination: pagination($notifications));
    }

    public function unreadCount()
    {
        return success(['count' => $this->mine()->whereNull('readAt')->count()]);
    }

    public function read($id)
    {
        $notification = $this->mine()->find($id);

        if (!$notification) {
            error(404, "Notification not found!!");
        }

        if (!$notification->readAt) {
            $notification->forceFill(['readAt' => now()])->save();
        }

        return success($this->parse($notification));
    }

    public function readAll()
    {
        $count = $this->mine()->whereNull('readAt')->update(['readAt' => now()]);

        return success(['read' => $count]);
    }

    public function registerDevice(Request $request)
    {
        $request->validate([
            'fcmToken' => 'required|string|max:500',
            'platform' => 'nullable|string|max:30',
        ]);

        StaffUserDevice::withTrashed()->updateOrCreate(
            ['fcmToken' => $request->fcmToken],
            [
                'staffUserId' => $this->userId(),
                'platform' => $request->input('platform', 'web'),
                'userAgent' => $request->userAgent(),
                'lastUsedAt' => now(),
                'deletedAt' => null,
            ]
        );

        return success();
    }

    public function removeDevice(Request $request)
    {
        $request->validate(['fcmToken' => 'required|string']);

        StaffUserDevice::where('staffUserId', $this->userId())
            ->where('fcmToken', $request->fcmToken)
            ->forceDelete();

        return success();
    }

    private function userId(): int
    {
        return (int) Auth::guard('web-admin')->id();
    }

    private function mine()
    {
        return StaffNotification::where('staffUserId', $this->userId());
    }

    private function parse(StaffNotification $n): array
    {
        return [
            'id' => $n->id,
            'type' => $n->type,
            'title' => $n->title,
            'body' => $n->body,
            'refType' => $n->refType,
            'refId' => $n->refId,
            'data' => $n->data,
            'isRead' => (bool) $n->readAt,
            'readAt' => $n->readAt?->toDateTimeString(),
            'createdAt' => $n->createdAt?->format('d/m/Y H:i'),
        ];
    }
}
