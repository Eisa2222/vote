<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voting_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('voting_campaigns')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['radio', 'checkbox', 'text', 'select', 'rating', 'yesno'])->default('radio');
            $table->boolean('is_required')->default(true);
            $table->integer('sort_order')->default(0);
            $table->unsignedInteger('max_selections')->nullable(); // for checkbox
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['campaign_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voting_questions');
    }
};
