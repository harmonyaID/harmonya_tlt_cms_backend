<?php

namespace App\Models\Language;

use App\Models\BaseModel;
use App\Parser\Language\LanguageGroupParser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LanguageGroup extends BaseModel
{
    use SoftDeletes;
    protected $table = 'language_groups';
    protected $guarded = [''];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';
    
    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime'
    ];
    public $parserClass = LanguageGroupParser::class;

    /*
     |--------------------------------------------------------------------------
     | Constants
     |-------------------------------------------------------------------------
     */

    const DEFAULT_GROUP = 'global';


    /*
     |--------------------------------------------------------------------------
     | Relationships
     |-------------------------------------------------------------------------
     */

    public function translations()
    {
        return $this->hasMany(Translation::class, 'groupId');
    }


    /*
     |--------------------------------------------------------------------------
     | Scopes
     |-------------------------------------------------------------------------
     */

    public function scopeFilter($query, $request)
    {
        return $query->where(function ($query) use ($request) {

            if ($request->has('search') && strlen($request->search) > 1) {
                $query->where('path', 'LIKE', "%$request->search%");
            }

        });
    }

    public function scopeOfPath($query, $path)
    {
        return $query->where('path', $path);
    }


    /*
     |--------------------------------------------------------------------------
     | Functions
     |-------------------------------------------------------------------------
     */

    public function hasUsedInTranslations()
    {
        return $this->translations->count() > 0;
    }

}
