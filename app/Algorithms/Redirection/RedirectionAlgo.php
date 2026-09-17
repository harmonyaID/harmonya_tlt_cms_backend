<?php

namespace App\Algorithms\Redirection;

use App\Models\Redirection\Redirection;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RedirectionAlgo
{
    public function __construct(
        protected Redirection|int|null $redirection = null
    ) {
        if (is_int($this->redirection)) {
            $this->redirection = Redirection::find($this->redirection);

            if (!$this->redirection) {
                errRedirectionGet();
            }
        }
    }

    public function create(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {

                $data = $request->only([
                    'name',
                    'sourceUrl',
                    'targetUrl',
                    'statusCode',
                    'isActive',
                ]);

                $this->redirection = Redirection::create(
                    $data + created_by()
                );

                if (!$this->redirection) {
                    errRedirectionSave();
                }

                activity()->setCausedBy()
                    ->setReference($this->redirection)
                    ->setType(ActivityType::REDIRECTION)
                    ->setAction(ActivityAction::CREATE)
                    ->log(
                        "Enter new redirection: " .
                        $this->redirection->name
                    );
            });

            return success($this->redirection);

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function update(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {

                $data = $request->only([
                    'name',
                    'sourceUrl',
                    'targetUrl',
                    'statusCode',
                    'isActive',
                ]);

                if (!$this->redirection->update($data)) {
                    errRedirectionUpdate();
                }

                activity()->setCausedBy()
                    ->setReference($this->redirection)
                    ->setType(ActivityType::REDIRECTION)
                    ->setAction(ActivityAction::UPDATE)
                    ->log(
                        "Update redirection: " .
                        $this->redirection->name
                    );
            });

            return success($this->redirection);

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete()
    {
        try {
            DB::transaction(function () {

                if (!$this->redirection->delete()) {
                    errRedirectionDelete();
                }

                activity()->setCausedBy()
                    ->setReference($this->redirection)
                    ->setType(ActivityType::REDIRECTION)
                    ->setAction(ActivityAction::DELETE)
                    ->log(
                        "Delete redirection: " .
                        $this->redirection->name
                    );
            });

            return success();

        } catch (\Error $error) {
            exception($error);
        }
    }
}