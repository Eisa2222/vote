<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\VotingCampaign;
use App\Models\VotingQuestion;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Public API for voting stats (optional)
Route::prefix('v1')->group(function () {
    Route::get('/campaigns/{campaign}/stats', function (VotingCampaign $campaign) {
        $service = app(\App\Services\VotingService::class);
        return response()->json($service->getCampaignStats($campaign));
    });
});
