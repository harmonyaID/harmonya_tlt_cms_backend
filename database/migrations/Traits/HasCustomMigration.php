<?php

namespace Database\Migrations\Traits;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

trait HasCustomMigration
{
    /**
     * @param Blueprint $table
     *
     * @return void
     */
    public function getDefaultTimestamps(Blueprint $table)
    {
        $table->timestamp('createdAt')->nullable();
        $table->timestamp('updatedAt')->nullable();
        $table->softDeletes('deletedAt');
    }

    /**
     * @param Blueprint $table
     *
     * @return void
     */
    public function getDefaultCreatedBy(Blueprint $table)
    {
        $table->char('createdBy')->nullable();
        $table->string('createdByName')->nullable();
    }

    /**
     * @param $from
     * @param $owner
     * @param $connection
     *
     * @return void
     */
    public function changeOwner($from = null, $to = null, $connection = null)
    {
        if (!$from) {
            $from = env('DB_USERNAME');
        }

        if (!$to) {
            $to = env("DB_OWNER");
        }

        if ($from && $to) {
            $query = sprintf("REASSIGN OWNED BY %s TO %s", $from, $to);

            if ($connection) {
                DB::connection($connection)->statement($query);
            } else {
                DB::statement($query);
            }
        }
    }

}
