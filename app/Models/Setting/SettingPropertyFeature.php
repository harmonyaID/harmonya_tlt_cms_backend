<?php

namespace App\Models\Setting;

use App\Models\BaseModel;
use App\Parser\Setting\SettingPropertyFeatureParser;
use Illuminate\Database\Eloquent\SoftDeletes;

class SettingPropertyFeature extends BaseModel
{
    use SoftDeletes;

    protected $table = 'setting_property_features';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $casts = [
        'hasValue' => 'boolean',
        'order' => 'integer',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
    ];

    public $parserClass = SettingPropertyFeatureParser::class;

    /*
     |--------------------------------------------------------------------------
     | Scopes
     |-------------------------------------------------------------------------
     */

    public function scopeFilter($query, $request)
    {
        return $query->where(function ($query) use ($request) {

            if ($request->has('search') && strlen($request->search) > 1) {
                $query->where('name', 'LIKE', "%$request->search%");
            }

        })->orderBy('order', 'ASC')->orderBy('id', 'ASC');
    }
}
