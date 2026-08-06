<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;

/**
 * Reusable "Trash" behaviour for any admin controller whose model uses SoftDeletes.
 *
 * A controller using this trait must implement:
 *   - trashModel(): the fully-qualified Eloquent model class (must use SoftDeletes)
 *   - trashParser(): the Parser class (must expose briefs()/first())
 *   - trashActivityType(): an App\Services\Constant\Activity\ActivityType value
 *   - trashLabel($item): a short label used in the activity log, e.g. $item->name
 */
trait HasTrash
{
    abstract protected function trashModel(): string;

    abstract protected function trashParser(): string;

    abstract protected function trashActivityType(): string;

    abstract protected function trashLabel($item): string;

    /**
     * List soft-deleted records for this module.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function trash(Request $request)
    {
        $modelClass = $this->trashModel();
        $parserClass = $this->trashParser();

        $items = $modelClass::onlyTrashed()->filter($request)->getOrPaginate($request);

        return success($parserClass::briefs($items), pagination: pagination($items));
    }

    /**
     * Restore a soft-deleted record back to active.
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function restore($id)
    {
        $modelClass = $this->trashModel();
        $parserClass = $this->trashParser();

        $item = $modelClass::onlyTrashed()->find($id);
        if (!$item) {
            error(404, 'Item not found in trash');
        }

        $item->restore();

        activity()->setCausedBy()
            ->setReference($item)
            ->setType($this->trashActivityType())
            ->setAction(\App\Services\Constant\Activity\ActivityAction::RESTORE)
            ->log("Restore from trash: " . $this->trashLabel($item));

        return success($parserClass::first($item));
    }

    /**
     * Permanently delete a soft-deleted record. Cannot be undone.
     *
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function forceDelete($id)
    {
        $modelClass = $this->trashModel();

        $item = $modelClass::onlyTrashed()->find($id);
        if (!$item) {
            error(404, 'Item not found in trash');
        }

        $label = $this->trashLabel($item);

        activity()->setCausedBy()
            ->setType($this->trashActivityType())
            ->setAction(\App\Services\Constant\Activity\ActivityAction::DELETE)
            ->log("Permanently delete: " . $label);

        $item->forceDelete();

        return success();
    }
}
