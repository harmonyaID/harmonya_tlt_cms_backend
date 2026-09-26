<?php

namespace App\Models\Setting;

use App\Models\BaseModel;
use App\Parser\Setting\SettingAnalyticsParser;

class SettingAnalytic extends BaseModel
{
    protected $table = 'setting_analytics';

    protected $guarded = ['id'];

    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
    ];

    public $parserClass = SettingAnalyticsParser::class;

    public function scopeFilter($query, $request)
    {
        return $query->where(function ($query) use ($request) {

            if ($this->hasSearch($request)) {
                $query->where('name', 'LIKE', "%$request->search%")
                    ->orWhere('key', 'LIKE', "%$request->search%");
            }

        })->orderBy('id', 'ASC');
    }
}