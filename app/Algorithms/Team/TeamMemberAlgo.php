<?php

namespace App\Algorithms\Team;

use App\Models\Team\TeamMember;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use App\Services\Constant\Storage\PathConstant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeamMemberAlgo
{
    public function __construct(protected TeamMember|int|null $teamMember = null)
    {
        if (is_int($this->teamMember)) {
            $this->teamMember = TeamMember::find($this->teamMember);
            if (!$this->teamMember) {
                errTeamMemberGet();
            }
        }
    }

    public function create(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->teamMember = TeamMember::create($request->except(['photo', 'deletePhoto']) + created_by());
                if (!$this->teamMember) {
                    errTeamMemberSave();
                }

                if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                    $this->teamMember->photo = $this->uploadPhoto($request);
                    $this->teamMember->save();
                }

                activity()->setCausedBy()
                    ->setReference($this->teamMember)
                    ->setType(ActivityType::TEAM_MEMBER)
                    ->setAction(ActivityAction::CREATE)
                    ->log("Enter new team member: " . $this->teamMember->name);

            });

            return success($this->teamMember);

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function update(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->teamMember->update($request->except(['photo', 'deletePhoto']));

                if ($request->boolean('deletePhoto')) {
                    $this->deletePhoto();
                    $this->teamMember->photo = null;
                    $this->teamMember->save();
                }

                if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                    $this->teamMember->photo = $this->uploadPhoto($request);
                    $this->teamMember->save();
                }

                activity()->setCausedBy()
                    ->setReference($this->teamMember)
                    ->setType(ActivityType::TEAM_MEMBER)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update team member: " . $this->teamMember->name);

            });

            return success($this->teamMember);

        } catch (\Error $error) {
            exception($error);
        }
    }

    public function delete()
    {
        try {

            DB::transaction(function () {

                $this->deletePhoto();

                if (!$this->teamMember->delete()) {
                    errTeamMemberDelete();
                }

                activity()->setCausedBy()
                    ->setReference($this->teamMember)
                    ->setType(ActivityType::TEAM_MEMBER)
                    ->setAction(ActivityAction::DELETE)
                    ->log("Delete team member: " . $this->teamMember->name);

            });

            return success();

        } catch (\Error $error) {
            exception($error);
        }
    }

    /*
     |--------------------------------------------------------------------------
     | Functions
     |-------------------------------------------------------------------------
     */

    private function uploadPhoto(Request $request)
    {
        $photo = $request->file('photo');

        $dirPath = PathConstant::IMAGES_TEAM_MEMBER_STORAGE_PUBLIC_PATH();
        if (!file_exists($dirPath)) {
            mkdir($dirPath, 0777, true);
        }

        $this->deletePhoto();

        $filename = filename($photo, $this->teamMember->name);
        $photo->move($dirPath, $filename);

        return $filename;
    }

    private function deletePhoto(): void
    {
        if (!$this->teamMember->photo) {
            return;
        }

        $dirPath = PathConstant::IMAGES_TEAM_MEMBER_STORAGE_PUBLIC_PATH();
        if (file_exists($dirPath . $this->teamMember->photo)) {
            unlink($dirPath . $this->teamMember->photo);
        }
    }
}
