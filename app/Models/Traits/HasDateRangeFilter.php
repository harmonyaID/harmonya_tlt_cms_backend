<?php

namespace App\Models\Traits;

use Illuminate\Support\Carbon;

trait HasDateRangeFilter
{
    /**
     * Apply a date-range filter (inclusive) on the given column, based on
     * 'fromDate' / 'toDate' request params (format: d/m/Y, e.g. 28/07/2026).
     *
     * Carbon's automatic parser assumes American m/d/Y order for slash-separated
     * dates, so incoming values must be parsed explicitly with the d/m/Y format
     * rather than handed straight to whereDate().
     *
     * @param $query
     * @param $request
     * @param string $column
     *
     * @return mixed
     */
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

    /**
     * Parse a 'd/m/Y' formatted filter value (e.g. "28/07/2026") into a Carbon
     * instance. Returns null (silently skipping the filter) if the value isn't
     * a valid date in that format, rather than throwing on bad input.
     *
     * @param string $value
     *
     * @return Carbon|null
     */
    private function parseFilterDate(string $value): ?Carbon
    {
        try {
            return Carbon::createFromFormat('d/m/Y', $value);
        } catch (\Exception $e) {
            return null;
        }
    }
}
