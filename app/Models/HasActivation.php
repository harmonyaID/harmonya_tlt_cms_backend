<?php

namespace App\Models;

trait HasActivation
{
    /**
     * @param $isActive
     *
     * @return void
     */
    public function activation($isActive)
    {
        $this->isActive = $isActive;
        $this->save();
    }
}
