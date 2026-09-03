<?php

namespace App\Models\Language;

use App\Models\BaseModel;
use App\Parser\Language\LanguageParser;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Language extends BaseModel
{
    use SoftDeletes;

    protected $table = 'languages';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';


    protected $casts = [
        'main' => 'boolean',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime'
    ];

    public $parserClass = LanguageParser::class;

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |-------------------------------------------------------------------------
     */

    public function groups()
    {
        return $this->hasMany(LanguageGroup::class, 'groupId');
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
                $query->where('country', 'LIKE', "%$request->search%")
                    ->orWhere('code', 'LIKE', "$request->search");
            }

        });
    }

    public function scopeOfCode($query, $code)
    {
        return $query->where('code', $code);
    }

    public function scopeOfMain($query)
    {
        return $query->where('main', true);
    }


    /*
     |--------------------------------------------------------------------------
     | Functions
     |-------------------------------------------------------------------------
     */

    public function imageUrl()
    {
        $image = PathConstant::IMAGES_FLAG . "$this->code.png";

        return Storage::disk('public')->url($image);
    }

    public function changeMain($main)
    {
        $anotherLangMain = Language::where('id', '!=', $this->id)->ofMain()->first();

        if ($anotherLangMain && $main) {
            $anotherLangMain->main = false;
            $anotherLangMain->save();
        }

        $this->main = $main;
        $this->save();
    }

    public static function langAlreadyExists($code, $country)
    {
        return self::where('code', $code)
                ->where('country', $country)
                ->count() > 0;
    }

}
