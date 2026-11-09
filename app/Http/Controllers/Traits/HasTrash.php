<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;

trait HasTrash
{
    abstract protected function trashModel(): string;

    abstract protected function trashParser(): string;

    abstract protected function trashActivityType(): string;

    abstract protected function trashLabel($item): string;

    public function trash(Request $request)
    {
        $modelClass = $this->trashModel();
        $parserClass = $this->trashParser();

        $items = $modelClass::onlyTrashed()->filter($request)->getOrPaginate($request);

        return success($parserClass::briefs($items), pagination: pagination($items));
    }

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
