<?php

namespace App\Models\Team;

use App\Models\BaseModel;
use App\Parser\Team\TeamMemberParser;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class TeamMember extends BaseModel
{
    use SoftDeletes;

    protected $table = 'teams';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $casts = [
        'order' => 'integer',
        'isActive' => 'boolean',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
    ];

    public $parserClass = TeamMemberParser::class;

    /*
     |--------------------------------------------------------------------------
     | Scopes
     |-------------------------------------------------------------------------
     */

    public function scopeFilter($query, $request)
    {
        return $query->where(function ($query) use ($request) {

            if ($request->has('search') && strlen($request->search) > 1) {
                $query->where(function ($search) use ($request) {
                    $search->where('name', 'LIKE', "%$request->search%")
                        ->orWhere('role', 'LIKE', "%$request->search%");
                });
            }

            if ($request->has('role') && $request->role) {
                $query->where('role', $request->role);
            }

            if ($request->has('isActive') && $request->isActive !== null && $request->isActive !== '') {
                $query->where('isActive', $request->isActive);
            }

        })->orderBy('order', 'ASC')->orderBy('id', 'ASC');
    }

    /*
     |--------------------------------------------------------------------------
     | Functions
     |-------------------------------------------------------------------------
     */

    public function photoUrl()
    {
        if (!$this->photo) {
            return null;
        }

        return Storage::disk('public')->url(PathConstant::IMAGES_TEAM_MEMBER . $this->photo);
    }
}
