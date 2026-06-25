<?php

namespace App\Models\Language;

use App\Models\BaseModel;
use App\Parser\Language\TranslationParser;
use Illuminate\Database\Eloquent\SoftDeletes;

class Translation extends BaseModel
{
    use SoftDeletes;

    protected $table = 'translations';
    protected $guarded = [''];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

    protected $casts = [
        'translated' => 'boolean',
        'translations' => 'array',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime'
    ];

    public $parserClass = TranslationParser::class;


    /*
     |--------------------------------------------------------------------------
     | Relationships
     |-------------------------------------------------------------------------
     */

    public function group()
    {
        return $this->belongsTo(LanguageGroup::class, 'groupId');
    }


    /*
     |--------------------------------------------------------------------------
     | Scopes
     |-------------------------------------------------------------------------
     */

    public function scopeFilter($query, $request)
    {
        return $query->where(function ($query) use ($request) {

            if ($request->has('groupId') && $request->groupId != '') {
                $query->where('groupId', $request->groupId);
            }

            if ($request->has('translated') && $request->translated != '') {
                $query->where('translated', $request->translated);
            }

            if ($request->has('search') && strlen($request->search) > 1) {

                $query->where(function ($query) use ($request) {
                    $query->where("key", "LIKE", "%$request->search%");

                    foreach (Language::all() as $language) {
                        $query->orWhereRaw("LOWER(JSON_EXTRACT(translations,'$." . $language->code . "')) LIKE LOWER('%$request->search%')");
                    }

                });

            }

        });
    }


    /*
     |--------------------------------------------------------------------------
     | Functions
     |-------------------------------------------------------------------------
     */

    public static function findByKey($key)
    {
        return self::where('key', $key)->first();
    }

    public static function keyAlreadyAdded($group, $key, $ignoredId = null)
    {
        return Translation::where('key', $key)
                ->where(function ($query) use ($group, $ignoredId) {

                    if ($group) {
                        $query->where('groupId', $group->id);
                    }

                    if ($ignoredId) {
                        $query->where('id', $ignoredId);
                    }

                })->count() > 0;
    }

}
