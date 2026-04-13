<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_workflows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('voting_campaigns')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('from_status'); // draft, pending_review, pending_approval, approved, rejected
            $table->string('to_status');
            $table->string('action'); // submit_for_review, approve_review, reject_review, approve_final, reject_final, send
            $table->text('comment')->nullable();
            $table->timestamps();

            $table->index('campaign_id');
        });

        // Add workflow columns to voting_campaigns
        Schema::table('voting_campaigns', function (Blueprint $table) {
            $table->string('workflow_status')->default('draft')->after('status');
            // draft, pending_review, reviewed, pending_approval, approved, rejected, sent
            $table->foreignId('submitted_by')->nullable()->after('created_by')->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->after('submitted_by')->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->after('reviewed_by')->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('voting_campaigns', function (Blueprint $table) {
            $table->dropConstrainedForeignId('submitted_by');
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropConstrainedForeignId('approved_by');
            $table->dropColumn(['workflow_status', 'submitted_at', 'reviewed_at', 'approved_at']);
        });
        Schema::dropIfExists('campaign_workflows');
    }
};
