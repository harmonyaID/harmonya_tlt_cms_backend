<?php

namespace App\Services\Misc\CompanyOffice;

trait HasCompanyOffice
{
    use FilteringCompanyOfficeAssigned;
    use SaveActiveCompanyOfficeId;

    public function getCompanyOfficeIdColumn(): string
    {
        return 'companyOfficeId';
    }

    public function getCompanyOfficeNameColumn(): string
    {
        return 'companyOfficeName';
    }

    public function scopeOfCompanyOfficeId($query, $companyOfficeId)
    {
        return $query->withoutGlobalScope(new FilteringCompanyOfficeAssignedScope())
            ->where($this->getCompanyOfficeIdColumn(), $companyOfficeId);
    }

    public function scopeOfCompanyOfficeIds($query, $companyOfficeIds)
    {
        return $query->withoutGlobalScope(new FilteringCompanyOfficeAssignedScope())
            ->whereIn($this->getCompanyOfficeIdColumn(), $companyOfficeIds);
    }
}
