<?php

use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\CampaignController;
use App\Http\Controllers\Admin\ClubController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\LeagueController;
use App\Http\Controllers\Admin\PlayerController;
use App\Http\Controllers\Admin\WorkflowController;
use App\Http\Controllers\Club\ClubCampaignController;
use App\Http\Controllers\Club\ClubDashboardController;
use App\Http\Controllers\Club\ClubPlayerController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Voting\PublicVotingController;
use Illuminate\Support\Facades\Route;

// Shared Voting Link (رابط واحد لجميع لاعبي النادي)
Route::get('/vote/s/{token}', [PublicVotingController::class, 'shared'])->name('vote.shared');
Route::post('/vote/s/{token}/verify', [PublicVotingController::class, 'sharedVerify'])->name('vote.shared.verify');

// Individual Voting Link (رابط فردي لكل لاعب)
Route::get('/vote/{token}', [PublicVotingController::class, 'show'])->name('vote.show');
Route::post('/vote/{token}/verify', [PublicVotingController::class, 'verify'])->name('vote.verify');

// Shared: Review & Submit (both flows use player's individual token)
Route::post('/vote/{token}/review', [PublicVotingController::class, 'review'])->name('vote.review');
Route::post('/vote/{token}/submit', [PublicVotingController::class, 'submit'])->name('vote.submit');

// Home redirect based on role
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->hasRole('super-admin')) {
            return redirect()->route('admin.dashboard');
        }
        if (auth()->user()->hasRole('club-admin')) {
            return redirect()->route('club.dashboard');
        }
    }
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    if (auth()->user()->hasRole('super-admin')) {
        return redirect()->route('admin.dashboard');
    }
    if (auth()->user()->hasRole('club-admin')) {
        return redirect()->route('club.dashboard');
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Super Admin Routes
Route::middleware(['auth', 'role:super-admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Leagues
    Route::resource('leagues', LeagueController::class);
    Route::patch('/leagues/{league}/toggle-status', [LeagueController::class, 'toggleStatus'])->name('leagues.toggle-status');

    // Clubs
    Route::resource('clubs', ClubController::class)->except(['show']);
    Route::patch('/clubs/{club}/toggle-status', [ClubController::class, 'toggleStatus'])->name('clubs.toggle-status');

    // Admins
    Route::resource('admins', AdminUserController::class)->except(['show']);
    Route::patch('/admins/{admin}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('admins.toggle-status');
    Route::post('/admins/{admin}/reset-password', [AdminUserController::class, 'resetPassword'])->name('admins.reset-password');

    // Players (global view)
    Route::get('/players', [PlayerController::class, 'index'])->name('players.index');
    Route::get('/players/{player}', [PlayerController::class, 'show'])->name('players.show');

    // Campaigns
    Route::resource('campaigns', CampaignController::class)->except(['show']);
    Route::get('/campaigns/{campaign}/questions', [CampaignController::class, 'questions'])->name('campaigns.questions');
    Route::post('/campaigns/{campaign}/questions', [CampaignController::class, 'storeQuestion'])->name('campaigns.questions.store');
    Route::put('/campaigns/{campaign}/questions/{question}', [CampaignController::class, 'updateQuestion'])->name('campaigns.questions.update');
    Route::delete('/campaigns/{campaign}/questions/{question}', [CampaignController::class, 'destroyQuestion'])->name('campaigns.questions.destroy');
    Route::post('/campaigns/{campaign}/questions/reorder', [CampaignController::class, 'reorderQuestions'])->name('campaigns.questions.reorder');

    // Send Campaign
    Route::get('/campaigns/{campaign}/send', [CampaignController::class, 'send'])->name('campaigns.send');
    Route::post('/campaigns/{campaign}/send', [CampaignController::class, 'sendToAdmins'])->name('campaigns.send-to-admins');

    // Results & Tracking
    Route::get('/campaigns/{campaign}/results', [CampaignController::class, 'results'])->name('campaigns.results');
    Route::get('/campaigns/{campaign}/tracking', [CampaignController::class, 'tracking'])->name('campaigns.tracking');

    // Exports
    Route::get('/campaigns/{campaign}/export/csv', [ExportController::class, 'campaignResultsCsv'])->name('campaigns.export.csv');
    Route::get('/campaigns/{campaign}/export/pdf', [ExportController::class, 'campaignResultsPdf'])->name('campaigns.export.pdf');
    Route::get('/campaigns/{campaign}/export/participation', [ExportController::class, 'participationCsv'])->name('campaigns.export.participation');

    // Audit Logs
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

    // Settings
    Route::get('/settings', function () { return view('admin.settings'); })->name('settings');
});

// Workflow Routes (accessible by association roles)
Route::middleware(['auth', 'role:super-admin|campaign-creator|campaign-reviewer|campaign-approver'])
    ->prefix('admin/workflow')->name('admin.workflow.')->group(function () {
        Route::get('/', [WorkflowController::class, 'index'])->name('index');
        Route::get('/{campaign}', [WorkflowController::class, 'show'])->name('show');
        Route::post('/{campaign}/submit-review', [WorkflowController::class, 'submitForReview'])->name('submit-review');
        Route::post('/{campaign}/approve-review', [WorkflowController::class, 'approveReview'])->name('approve-review');
        Route::post('/{campaign}/reject-review', [WorkflowController::class, 'rejectReview'])->name('reject-review');
        Route::post('/{campaign}/approve-final', [WorkflowController::class, 'approveFinal'])->name('approve-final');
        Route::post('/{campaign}/reject-final', [WorkflowController::class, 'rejectFinal'])->name('reject-final');
    });

// Club Admin Routes
Route::middleware(['auth', 'role:club-admin', 'club.access'])->prefix('club')->name('club.')->group(function () {
    Route::get('/dashboard', [ClubDashboardController::class, 'index'])->name('dashboard');

    // Players
    Route::get('/players/import', [ClubPlayerController::class, 'importForm'])->name('players.import-form');
    Route::post('/players/import', [ClubPlayerController::class, 'import'])->name('players.import');
    Route::resource('players', ClubPlayerController::class)->except(['show']);

    // Campaigns
    Route::get('/campaigns', [ClubCampaignController::class, 'index'])->name('campaigns.index');
    Route::get('/campaigns/{campaign}', [ClubCampaignController::class, 'show'])->name('campaigns.show');
    Route::post('/campaigns/{campaign}/generate-links', [ClubCampaignController::class, 'generateLinks'])->name('campaigns.generate-links');
    Route::post('/campaigns/{campaign}/send-links', [ClubCampaignController::class, 'sendLinks'])->name('campaigns.send-links');
    Route::post('/campaigns/{campaign}/send-shared-sms', [ClubCampaignController::class, 'sendSharedSms'])->name('campaigns.send-shared-sms');
    Route::post('/campaigns/{campaign}/send-shared-email', [ClubCampaignController::class, 'sendSharedEmail'])->name('campaigns.send-shared-email');
    Route::get('/campaigns/{campaign}/results', [ClubCampaignController::class, 'results'])->name('campaigns.results');

    // Club Profile
    Route::get('/profile', function () {
        return view('club.profile');
    })->name('profile');
});

require __DIR__.'/auth.php';
