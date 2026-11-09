<?php

namespace App\Models;

trait HasActivation
{
    public function activation($isActive)
    {
        $this->isActive = $isActive;
        $this->save();
    }
}
