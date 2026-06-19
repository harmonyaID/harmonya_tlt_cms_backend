<?php

namespace App\Models\Access;

use App\Models\BaseModel;
use App\Parser\Access\AccessPermissionParser;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AccessPermission extends BaseModel
{
    protected $table = 'access_permissions';
    protected $guarded = ['id'];

    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime'
    ];

    public $parserClass = AccessPermissionParser::class;


    /** --- RELATIONSHIPS --- */

    public function parent(): BelongsTo
    {
        return $this->belongsTo(AccessPermission::class, 'parentId');
    }


    /** --- SCOPES --- */

    public function scopeFilter($query, $request)
    {
        return $query->where(function ($query) use ($request) {
            if ($request->group != '') {
                $query->where('group', $request->group);
            }

            if ($this->hasSearch($request)) {
                $query->where(function ($query) use ($request) {
                    $query->where('name', 'LIKE', "%$request->search%")
                        ->orWhere('display', 'LIKE', "%$request->search%");
                });
            }
        });
    }

    public function scopeOfName($query, $name)
    {
        return $query->where('name', $name);
    }

    public function scopeOfGroup($query, $group)
    {
        return $query->where('group', $group);
    }

}
