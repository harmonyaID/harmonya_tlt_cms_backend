<?php

namespace App\Models\Setting;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class SettingNotificationCredential extends BaseModel
{
    use SoftDeletes;

    protected $table = 'setting_notification_credentials';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'credentials' => 'array',
        'isActive' => 'boolean',
    ];

    public function scopeFilter(
        Builder $query,
        Request $request
    ) {
        return $query
            ->when(
                $request->has('search') && strlen($request->search) > 1,
                function ($query) use ($request) {
                    $query->where(function ($query) use ($request) {
                        $query
                            ->where(
                                'name',
                                'LIKE',
                                "%{$request->search}%"
                            );
                    });
                }
            )
            ->when(
                $request->has('providerId'),
                function ($query) use ($request) {
                    $query->where(
                        'providerId',
                        $request->providerId
                    );
                }
            )
            ->when(
                $request->has('isActive'),
                function ($query) use ($request) {
                    $query->where(
                        'isActive',
                        $request->boolean('isActive')
                    );
                }
            );
    }
}