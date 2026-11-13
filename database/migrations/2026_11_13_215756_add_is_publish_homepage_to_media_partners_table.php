
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media_partners', function (Blueprint $table) {
            $table->boolean('isPublishHomepage')
                ->default(false)
                ->after('isPublish')
                ->index();
        });
    }

    public function down(): void
    {
        Schema::table('media_partners', function (Blueprint $table) {
            $table->dropIndex(['isPublishHomepage']);
            $table->dropColumn('isPublishHomepage');
        });
    }
};
