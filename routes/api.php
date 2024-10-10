<?php

use App\Http\Controllers\InfoParticipationController;
use App\Http\Controllers\SectionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//verficar se tem sessão disponivel ou não
Route::get('/section-active', [SectionController::class, 'sectionActive']);

//finalizar sessão ativa
Route::post('/finishing-section/{id}', [SectionController::class, 'finishingSection']);

//iniciar sessão e idUser
Route::post('/start-participation', [InfoParticipationController::class, "startParticipation"]);

//recuperar foto
Route::get('/get-photo/{id}', [InfoParticipationController::class, "getPhoto"]);