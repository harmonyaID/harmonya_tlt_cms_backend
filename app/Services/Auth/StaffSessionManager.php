<?php

namespace App\Services\Auth;

use App\Models\Staff\StaffUserSession;
use Illuminate\Http\Request;

/** One row per login (JWT `jti`) so a device can be listed and logged out. */
class StaffSessionManager
{
    private const TOUCH_EVERY = 60;

    /** Returns false when the session was logged out. */
    public function touch($payload, int $userId, Request $request): bool
    {
        $jti = $payload['jti'] ?? null;

        if (!$jti) {
            return true;
        }

        $session = StaffUserSession::where('jti', $jti)->first();

        if (!$session) {
            $session = StaffUserSession::create($this->describe($request) + [
                'userId' => $userId,
                'jti' => $jti,
                'lastActiveAt' => now(),
                'expiresAt' => $this->expiry($payload),
            ]);
        }

        if ($session->revokedAt) {
            return false;
        }

        if (!$session->lastActiveAt || $session->lastActiveAt->diffInSeconds(now()) >= self::TOUCH_EVERY) {
            $session->forceFill(['lastActiveAt' => now()])->save();
        }

        return true;
    }

    public function list(int $userId, ?string $currentJti): array
    {
        return StaffUserSession::where('userId', $userId)
            ->active()
            ->orderByDesc('lastActiveAt')
            ->get()
            ->map(fn (StaffUserSession $s) => [
                'id' => $s->id,
                'deviceName' => $s->deviceName,
                'browser' => $s->browser,
                'platform' => $s->platform,
                'ipAddress' => $s->ipAddress,
                'lastActiveAt' => $s->lastActiveAt?->toDateTimeString(),
                'createdAt' => $s->createdAt?->toDateTimeString(),
                'isCurrent' => $currentJti !== null && $s->jti === $currentJti,
            ])
            ->all();
    }

    public function revoke(int $userId, int $sessionId): bool
    {
        $session = StaffUserSession::where('userId', $userId)->active()->find($sessionId);

        if (!$session) {
            return false;
        }

        $session->forceFill(['revokedAt' => now()])->save();

        return true;
    }

    public function revokeOthers(int $userId, ?string $currentJti): int
    {
        return StaffUserSession::where('userId', $userId)
            ->active()
            ->when($currentJti, fn ($q) => $q->where('jti', '!=', $currentJti))
            ->update(['revokedAt' => now()]);
    }

    public function revokeByJti(?string $jti): void
    {
        if ($jti) {
            StaffUserSession::where('jti', $jti)->whereNull('revokedAt')->update(['revokedAt' => now()]);
        }
    }

    private function describe(Request $request): array
    {
        $ua = (string) $request->userAgent();

        $browser = match (true) {
            (bool) preg_match('/Edg(e|A|iOS)?\//i', $ua) => 'Edge',
            (bool) preg_match('/OPR\/|Opera/i', $ua) => 'Opera',
            (bool) preg_match('/Firefox|FxiOS/i', $ua) => 'Firefox',
            (bool) preg_match('/Chrome|CriOS/i', $ua) => 'Chrome',
            (bool) preg_match('/Safari/i', $ua) => 'Safari',
            default => 'Unknown browser',
        };

        $platform = match (true) {
            (bool) preg_match('/iPhone|iPad|iPod/i', $ua) => 'iOS',
            (bool) preg_match('/Android/i', $ua) => 'Android',
            (bool) preg_match('/Windows/i', $ua) => 'Windows',
            (bool) preg_match('/Mac OS X|Macintosh/i', $ua) => 'macOS',
            (bool) preg_match('/Linux/i', $ua) => 'Linux',
            default => 'Unknown OS',
        };

        return [
            'deviceName' => "{$browser} on {$platform}",
            'browser' => $browser,
            'platform' => $platform,
            'ipAddress' => $request->ip(),
            'userAgent' => $ua ?: null,
        ];
    }

    private function expiry($payload): ?\Carbon\Carbon
    {
        $exp = $payload['exp'] ?? null;

        return $exp ? \Carbon\Carbon::createFromTimestamp((int) $exp) : null;
    }
}
