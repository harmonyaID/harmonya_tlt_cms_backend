<?php

namespace App\Models\Staff;

use App\Models\Access\Traits\HasAccession;
use App\Models\BaseModel;
use App\Models\HasActivation;
use App\Models\Notification\NotificationStaff;
use App\Models\Notification\NotificationStaffToken;
use App\Models\Setting\SettingCountry;
use App\Models\Traits\HasDateRangeFilter;
use App\Models\Traits\HasMultiValueFilter;
use App\Parser\Staff\StaffParser;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Logia\Core\Parser\Trait\HasParser;

class Staff extends BaseModel
{
    use HasActivation;
    use HasAccession;
    use HasDateRangeFilter;
    use HasMultiValueFilter;

    protected $table = 'staffs';
    protected $guarded = ['id'];

    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
        'isActive' => 'boolean',
        'isSuperadmin' => 'boolean',
    ];

    public $parserClass = StaffParser::class;

    public function user(): HasOne
    {
        return $this->hasOne(StaffUser::class, 'staffId');
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(SettingCountry::class, 'countryId');
    }

    public function scopeFilter($query, $request)
    {
        return $query->where(function ($query) use ($request) {
            if ($request->countryId != '') {
                $query->where('countryId', $request->countryId);
            }

            if ($request->genderId != '') {
                $query->where('genderId', $request->genderId);
            }

            if ($request->isActive != '') {
                $query->where('isActive', $request->isActive);
            }

            if ($request->isSuperadmin != '') {
                $query->where('isSuperadmin', $request->isSuperadmin);
            }

            if ($request->roleIds != '' && $request->roleIds !== null) {
                $query->whereHas('roles', function ($role) use ($request) {
                    $role->whereIn('roleId', $this->toValueArray($request->roleIds));
                });
            }

            if ($this->hasSearch($request)) {
                $query->where(function ($query) use ($request) {
                    $query->where('fullName', 'LIKE', "%$request->search%")
                        ->orWhere('phone', 'LIKE', "%$request->search%")
                        ->orWhereHas('user', function ($query) use ($request) {
                            $query->where('email', 'LIKE', "%$request->search%");
                        });
                });
            }

            $this->applyDateRangeFilter($query, $request);
        });
    }

    public function updateSuperadmin($isSuperadmin)
    {
        $this->isSuperadmin = $isSuperadmin;
        $this->save();
    }

}