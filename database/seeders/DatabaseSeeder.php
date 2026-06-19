<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Database\Seeders\Access\AccessPermissionSeeder;
use Database\Seeders\Access\AccessRoleSeeder;
use Database\Seeders\Setting\CountrySeeder;
use Database\Seeders\Staff\StaffSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            StaffSeeder::class,
            AccessRoleSeeder::class,
            AccessPermissionSeeder::class,
            CountrySeeder::class,
        ]);
    }
}
