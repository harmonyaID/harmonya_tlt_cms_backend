<?php

namespace App\Models\Configuration;

use App\Models\BaseModel;
use App\Models\Setting\SettingCountry;
use App\Parser\Configuration\WebsiteInformationParser;
use Illuminate\Database\Eloquent\SoftDeletes;

class WebsiteInformation extends BaseModel
{
    use SoftDeletes;

    protected $table = 'website_informations';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';


    protected $casts = [
        'socialMedia' => 'array',
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime'
    ];

    public $parserClass = WebsiteInformationParser::class;

    /*
     |--------------------------------------------------------------------------
     | Relationships
     |-------------------------------------------------------------------------
     */

    public function getCountry()
    {
        return $this->belongsTo(SettingCountry::class, 'country', 'cca2');
    }
}
