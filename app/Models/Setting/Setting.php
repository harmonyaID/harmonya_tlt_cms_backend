<?php

namespace App\Models\Setting;

use App\Models\BaseModel;
use App\Models\HasActivation;

class Setting extends BaseModel
{
    use HasActivation;

    protected $table = 'settings';
    protected $guarded = ['id'];

    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
    ];


    /** --- SCOPES --- */

    public function scopeFilter($query, $request)
    {
        return $query->where(function ($query) use ($request) {

            if ($this->hasSearch($request)) {
                $query->where('name', 'LIKE', "%$request->search%")
                    ->orWhere('description', 'LIKE', "%$request->search%");
            }

        });
    }

    public function scopeOfName($query, $name)
    {
        return $query->where('name', $name);
    }

}
