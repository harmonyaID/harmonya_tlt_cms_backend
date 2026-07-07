<?php

namespace App\Models\Experience;

use App\Models\BaseModel;
use App\Parser\Experience\ExperienceTypeParser;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExperienceType extends BaseModel
{
    use SoftDeletes;

    protected $table = 'experience_types';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
    ];

    public $parserClass = ExperienceTypeParser::class;

    public function scopeFilter($query, $request)
    {
        return $query->where(function ($query) use ($request) {

            if ($request->has('search') && strlen($request->search) > 1) {
                $query->where('name', 'LIKE', "%$request->search%");
            }

        })->orderBy('id', 'ASC');
    }
}