<?php

namespace App\Algorithms\Staff;

use App\Mail\Auth\AuthPasswordMail;
use App\Models\Setting\SettingCountry;
use App\Models\Staff\Staff;
use App\Models\Staff\StaffUser;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Activity\ActivityType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class StaffAlgo
{
    /**
     * @var string
     */
    protected string $newPassword = "";

    /**
     * @param Staff|int|null $staff
     */
    public function __construct(protected Staff|int|null $staff = null)
    {
        if (is_int($this->staff)) {
            $this->staff = Staff::find($this->staff);
            if (!$this->staff) {
                errStaffGet();
            }
        }
    }


    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|void
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function create(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->saveStaff($request);

                $user = $this->saveUser($request);

                activity()->setCausedBy()
                    ->setReference($this->staff)
                    ->setType(ActivityType::STAFF)
                    ->setAction(ActivityAction::CREATE)
                    ->log("Enter new staff. Name: " . $this->staff->fullName);

                $this->sendNewPassword($user);

            });

            return success($this->staff);

        } catch (\Error $error) {
            exception($error);
        }
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|void
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function update(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->saveStaff($request);

                $this->updateUser($request);

                activity()->setCausedBy()
                    ->setReference($this->staff)
                    ->setType(ActivityType::STAFF)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Update staff. Name: " . $this->staff->fullName);

            });

            return success($this->staff);

        } catch (\Error $error) {
            exception($error);
        }
    }

    /**
     * @return \Illuminate\Http\JsonResponse|mixed|void
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function delete()
    {
        try {

            DB::transaction(function () {

                if (!$this->staff->delete()) {
                    errStaffUpdate();
                }

                if (!$this->staff->user()->delete()) {
                    errStaffUserDelete();
                }

                $this->staff->roles()->delete();
                $this->staff->permissions()->delete();

                activity()->setCausedBy()
                    ->setReference($this->staff)
                    ->setType(ActivityType::STAFF)
                    ->setAction(ActivityAction::DELETE)
                    ->log("Delete staff. Name: " . $this->staff->fullName);

            });

            return success();

        } catch (\Error $error) {
            exception($error);
        }
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|void
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function activation(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->staff->activation($request->isActive);

                activity()->setCausedBy()
                    ->setReference($this->staff)
                    ->setType(ActivityType::STAFF)
                    ->setAction(ActivityAction::UPDATE)
                    ->log(sprintf("%s staff. Name: %s", ($request->isActive ? "Activate" : "Inactivate"), $this->staff->fullName));

            });

            return success($this->staff->fresh());

        } catch (\Error $error) {
            exception($error);
        }
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|void
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function changePassword(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->newPassword = $request->password;

                $this->staff->user->changePassword($request->password);

                $this->sendNewPassword($this->staff->user);

                activity()->setCausedBy()
                    ->setReference($this->staff)
                    ->setType(ActivityType::STAFF)
                    ->setAction(ActivityAction::UPDATE)
                    ->log("Change staff of password. Name: " . $this->staff->fullName);

            });

            return success();

        } catch (\Error $error) {
            exception($error);
        }
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse|mixed|void
     * @throws \Logia\Core\Exception\ErrorException
     */
    public function updateSuperadmin(Request $request)
    {
        try {

            DB::transaction(function () use ($request) {

                $this->staff->updateSuperadmin($request->isSuperadmin);

                activity()->setCausedBy()
                    ->setReference($this->staff)
                    ->setType(ActivityType::STAFF)
                    ->setAction(ActivityAction::UPDATE)
                    ->log(sprintf("Update staff to %s. Name: %s", ($request->superadmin ? "superadmin" : "admin"), $this->staff->fullName));

            });

            return success();

        } catch (\Error $error) {
            exception($error);
        }
    }


    /** --- FUNCTIONS --- */

    private function saveStaff(Request $request)
    {
        if ($request->countryId) {
            if (!SettingCountry::where('id', $request->countryId)->exists()) {
                errSettingCountryGet();
            }
        }

        $form = $request->only([
            'fullName',
            'phone',
            'genderId',
            'countryId',
            'address',
            'isActive',
            'isSuperadmin',
        ]);

        if ($this->staff) {
            $this->staff->update($form);
            $this->staff->refresh();
            return;
        }

        $this->staff = Staff::create($form + created_by());
    }

    private function saveUser(Request $request)
    {
        if (StaffUser::emailAlreadyUsed($request->email)) {
            errStaffSave("Email already used");
        }

        $this->newPassword = $request->password ?: Str::random(10);

        return $this->staff->user()->save(new StaffUser([
            'email' => $request->email,
            'password' => Hash::make($this->newPassword)
        ]));
    }

    private function updateUser(Request $request)
    {
        if (StaffUser::emailAlreadyUsed($request->email, $this->staff->user->id)) {
            errStaffUpdate("Email already used");
        }

        $this->staff->user->update(['email' => $request->email]);
    }

    private function sendNewPassword($user)
    {
        if ($this->newPassword) {
            // if (App::isProduction()) {
            //     $mail = Mail::driver('postmark');
            // } else {
            //     $mail = Mail::driver('smtp-mailtrap');
            // }

            // $message = new AuthPasswordMail($this->staff, $this->newPassword);
            // $mail->to($user->email)
            //     ->queue($message);
        }
    }

}
