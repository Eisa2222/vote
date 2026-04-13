<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaign_assignments', function (Blueprint $table) {
            $table->string('shared_token', 32)->nullable()->unique()->after('club_id');
        });
    }

    public function down(): void
    {
        Schema::table('campaign_assignments', function (Blueprint $table) {
            $table->dropColumn('shared_token');
        });
    }
};
