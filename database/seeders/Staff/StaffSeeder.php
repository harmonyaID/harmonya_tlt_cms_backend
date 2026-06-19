<?php

namespace Database\Seeders\Staff;

use App\Models\Staff\Staff;
use App\Models\Staff\StaffUser;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = $this->getData();
        foreach ($data as $datum) {
            $user = StaffUser::where('email', $datum['email'])->first();
            if ($user) {
                $user->account->update(collect($datum)->only([
                    'fullName',
                    'phone',
                    'genderId',
                    'countryId',
                    'address',
                    'isActive',
                    'isSuperadmin',
                ])->toArray());
            } else {
                $staff = Staff::create(collect($datum)->only([
                    'fullName',
                    'phone',
                    'genderId',
                    'countryId',
                    'address',
                    'isActive',
                    'isSuperadmin',
                ])->toArray());

                $staff->user()->save(new StaffUser([
                    'email' => $datum['email'],
                    'password' => Hash::make('admin123'),
                ]));
            }

        }
    }


    /** --- FUNCTIONS --- */

    private function getData()
    {
        return array(
            [
                'fullName' => 'Administrator',
                'email' => 'admin@example.com',
                'phone' => '08123432321',
                'genderId' => 1,
                'countryId' => 1,
                'address' => 'Jl. Testing',
                'isActive' => 1,
                'isSuperadmin' => 1,
            ]
        );
    }

}
