<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('national_id')->nullable();
            $table->string('player_number', 10)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('nationality')->nullable();
            $table->string('position')->nullable();
            $table->string('photo')->nullable();
            $table->enum('status', ['active', 'suspended', 'injured', 'retired', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['club_id', 'national_id']);
            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
