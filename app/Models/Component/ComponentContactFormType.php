<?php

namespace App\Models\Component;

use App\Models\Activity\Traits\HasActivity;
use App\Models\BaseModel;
use App\Parser\Component\ComponentParser;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComponentContactFormType extends BaseModel
{
    use SoftDeletes;
    use HasActivity;

    protected $table = 'component_contact_form_types';
    protected $guarded = ['id'];

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';


    protected $casts = [
        self::CREATED_AT => 'datetime',
        self::UPDATED_AT => 'datetime',
        self::DELETED_AT => 'datetime'
    ];

    public $parserClass = ComponentParser::class;

    public function getActivityType(): string
    {
        return ActivityType::C_CONTACT_FORM_TYPE;
    }

    public function getActivitySubType(): string
    {
        return 'component_contact_form_types';
    }
    public function getActivityPropertyCreate(): array
    {
        return $this->attributesToArray();
    }

    public function getActivityPropertyUpdate(): array
    {
        return $this->attributesToArray();
    }

    public function getActivityPropertyDelete(): array
    {
        return $this->attributesToArray();
    }

}
