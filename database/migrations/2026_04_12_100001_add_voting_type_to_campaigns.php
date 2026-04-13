<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('voting_campaigns', function (Blueprint $table) {
            $table->enum('voting_type', ['public', 'private'])->default('public')->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('voting_campaigns', function (Blueprint $table) {
            $table->dropColumn('voting_type');
        });
    }
};
