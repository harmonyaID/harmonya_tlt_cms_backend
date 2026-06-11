<?php

namespace App\Algorithms\Component;

use App\Services\Constant\Activity\ActivityAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ComponentAlgo
{
    /**
     * @param $model
     * @param Request $request
     *
     * @return JsonResponse|mixed
     */
    public function createBy($model, Request $request)
    {
        try {

            $component = DB::transaction(function () use ($model, $request) {
                $createdBy = [];

                // TODO: Enable after install globalxtreme/laravel-identifier.
//                $tableName = app($model)->getTable();
//                $columns = DB::getSchemaBuilder()->getColumnListing($tableName);
//                if (in_array('createdBy', $columns)) {
//                    if ($user = auth_user()) {
//                        $createdBy = [
//                            'createdBy' => $user['id'],
//                            'createdByName' => $user['fullName'],
//                        ];
//                    }
//                }

                $component = $model::create($request->all() + $createdBy);

                $component->setActivityPropertyAttributes(ActivityAction::CREATE)
                    ->saveActivity("Enter new " . $component->getTable() . ": $component->name [$component->id]");

                return $component;

            });

            return success($component);

        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    /**
     * @param Model $model
     * @param Request $request
     *
     * @return mixed|JsonResponse
     */
    public function update(Model $model, Request $request)
    {
        try {

            DB::transaction(function () use ($model, $request) {

                $model->setOldActivityPropertyAttributes(ActivityAction::UPDATE);

                $model->update($request->all());

                $model->setActivityPropertyAttributes(ActivityAction::UPDATE)
                    ->saveActivity("Update " . $model->getTable() . ": $model->name [$model->id]");

            });

            return success($model->fresh());

        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    /**
     * @param Model $model
     *
     * @return mixed|JsonResponse
     */
    public function delete(Model $model)
    {
        try {

            DB::transaction(function () use ($model) {

                $model->setOldActivityPropertyAttributes(ActivityAction::DELETE);

                $model->delete();

                $model->setActivityPropertyAttributes(ActivityAction::DELETE)
                    ->saveActivity("Delete " . $model->getTable() . ": $model->name [$model->id]");

            });

            return success();

        } catch (\Exception $exception) {
            exception($exception);
        }
    }

}
