<?php

namespace App\Models\Setting;

use App\Models\BaseModel;
use App\Models\HasActivation;
use App\Parser\Setting\SettingCountryParser;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SettingCountry extends BaseModel
{
    use HasActivation;

    protected $table = 'setting_countries';
    protected $guarded = ['id'];

    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime',
        'isPopular' => 'boolean',
        'isActive' => 'boolean',
    ];

    public $parserClass = SettingCountryParser::class;


    /** --- SCOPES --- */

    public function scopeFilter($query, $request)
    {
        return $query->where(function ($query) use ($request) {
            if ($request->isPopular != "") {
                $query->where('isPopular', $request->isPopular);
            }

            if ($request->isActive != "") {
                $query->where('isActive', $request->isActive);
            }

            if ($request->cca != "") {
                $query->where(function ($query) use ($request) {
                    $query->where('cca2', $request->cca)
                        ->orWhere('cca3', $request->cca);
                });
            }

            if ($this->hasSearch($request)) {
                $query->where('name', 'LIKE', "%$request->search%");
            }
        });
    }


    /** --- FUNCTIONS --- */

    /**
     * @param $isPopular
     *
     * @return void
     */
    public function popular($isPopular)
    {
        $this->isPopular = $isPopular;
        $this->save();
    }
}
