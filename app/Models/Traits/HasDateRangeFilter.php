<?php

namespace App\Models\Traits;

use Illuminate\Support\Carbon;

trait HasDateRangeFilter
{
    protected function applyDateRangeFilter($query, $request, string $column = 'createdAt')
    {
        if ($request->has('fromDate') && $request->fromDate) {
            $fromDate = $this->parseFilterDate($request->fromDate);
            if ($fromDate) {
                $query->whereDate($column, '>=', $fromDate->format('Y-m-d'));
            }
        }

        if ($request->has('toDate') && $request->toDate) {
            $toDate = $this->parseFilterDate($request->toDate);
            if ($toDate) {
                $query->whereDate($column, '<=', $toDate->format('Y-m-d'));
            }
        }

        return $query;
    }

    private function parseFilterDate(string $value): ?Carbon
    {
        try {
            return Carbon::createFromFormat('d/m/Y', $value);
        } catch (\Exception $e) {
            return null;
        }
    }
}
