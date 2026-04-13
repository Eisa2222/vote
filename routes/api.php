<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('web', 'auth')->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json($request->user());
    });

    Route::prefix('v1')->group(function () {
        Route::get('/campaigns/{campaign}/stats', function (\App\Models\VotingCampaign $campaign) {
            $service = app(\App\Services\VotingService::class);
            return response()->json($service->getCampaignStats($campaign));
        });
    });
});
