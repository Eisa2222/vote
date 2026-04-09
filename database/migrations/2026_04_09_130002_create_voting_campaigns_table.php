<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voting_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('ends_at')->nullable();
            $table->enum('status', ['draft', 'scheduled', 'active', 'closed', 'archived'])->default('draft');
            $table->enum('target_audience', ['all', 'specific'])->default('all');
            $table->boolean('single_response')->default(true);
            $table->boolean('prevent_edit_after_submit')->default(true);
            $table->boolean('show_countdown')->default(true);
            $table->dateTime('results_visible_after')->nullable();
            $table->boolean('allow_review_before_submit')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index(['starts_at', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voting_campaigns');
    }
};
