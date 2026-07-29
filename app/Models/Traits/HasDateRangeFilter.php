<?php

namespace App\Models\Traits;

trait HasDateRangeFilter
{
    /**
     * Apply a date-range filter (inclusive) on the given column, based on
     * 'dateFrom' / 'dateTo' request params (format: Y-m-d).
     *
     * @param $query
     * @param $request
     * @param string $column
     *
     * @return mixed
     */
    protected function applyDateRangeFilter($query, $request, string $column = 'createdAt')
    {
        if ($request->has('dateFrom') && $request->dateFrom) {
            $query->whereDate($column, '>=', $request->dateFrom);
        }

        if ($request->has('dateTo') && $request->dateTo) {
            $query->whereDate($column, '<=', $request->dateTo);
        }

        return $query;
    }
}