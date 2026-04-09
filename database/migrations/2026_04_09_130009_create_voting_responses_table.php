<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voting_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('voting_sessions')->cascadeOnDelete();
            $table->foreignId('campaign_id')->constrained('voting_campaigns')->cascadeOnDelete();
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();
            $table->foreignId('club_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['campaign_id', 'player_id']);
        });

        Schema::create('voting_response_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('response_id')->constrained('voting_responses')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('voting_questions')->cascadeOnDelete();
            $table->foreignId('option_id')->nullable()->constrained('voting_question_options')->nullOnDelete();
            $table->text('text_answer')->nullable();
            $table->timestamps();

            $table->index(['response_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voting_response_answers');
        Schema::dropIfExists('voting_responses');
    }
};
