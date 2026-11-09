<?php

namespace App\Models\Component\Traits;

use App\Models\Activity\Traits\HasActivity;
use App\Parser\Component\ComponentParser;
use App\Services\Constant\Activity\ActivityType;

trait HasActivityComponentProperty
{
    use HasActivity;

    public function getActivityType(): string
    {
        return ActivityType::COMPONENT;
    }

    public function getActivitySubType(): string
    {
        return '';
    }

    public function getActivityPropertyCreate()
    {
        return $this->setActivityPropertyParser();
    }

    public function getActivityPropertyUpdate()
    {
        return $this->setActivityPropertyParser();
    }

    public function getActivityPropertyDelete()
    {
        return $this->setActivityPropertyParser() + [
                'deletedAt' => $this->deletedAt?->format('d/m/Y H:i')
            ];
    }

    private function setActivityPropertyParser()
    {
        $this->refresh();

        return ComponentParser::first($this);
    }
}
