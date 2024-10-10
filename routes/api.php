<?php

use App\Http\Controllers\InfoParticipationController;
use App\Http\Controllers\SectionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/section-active', [SectionController::class, 'sectionActive']);

Route::post('/finishing-section/{id}', [SectionController::class, 'finishingSection']);

Route::post('/start-participation', [InfoParticipationController::class, "startParticipation"]);

Route::get('/get-photo/{id}', [InfoParticipationController::class, "getPhoto"]);