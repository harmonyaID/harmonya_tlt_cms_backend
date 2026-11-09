<?php

namespace App\Services\Misc;

use App\Models\Activity\Activity;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SaveActivity
{
    public function __construct(public Activity $activity)
    {
    }

    public function setType(string $type)
    {
        $this->activity->type = $type;
        return $this;
    }

    public function setSubType(string $subType)
    {
        $this->activity->subType = $subType;
        return $this;
    }

    public function setAction(string $action)
    {
        $this->activity->action = $action;
        return $this;
    }

    public function setCausedBy()
    {
        if ($data = created_by()) {
            $this->activity->causedBy = $data['createdBy'];
            $this->activity->causedByName = $data['createdByName'];
        }

        return $this;
    }

    public function setReference(Model $reference)
    {
        $this->activity->referable()->associate($reference);
        return $this;
    }

    public function setDescription(string $description)
    {
        $this->activity->description = $description;
        return $this;
    }

    public function setProperties(array|string $properties)
    {
        $this->activity->properties = is_string($properties) ? [$properties] : $properties;
        return $this;
    }

    public function setCreatedAt(Carbon $date)
    {
        $this->activity->{Activity::CREATED_AT} = $date;
        return $this;
    }

    public function log(string|null $description = null)
    {
        if ($description) {
            $this->setDescription($description);
        }

        $this->activity->save();
        return $this->activity;
    }
}
