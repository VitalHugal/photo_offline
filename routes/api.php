<?php

use App\Http\Controllers\InfoParticipationController;
use App\Http\Controllers\SessionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//verficar se tem sessão disponivel ou não
Route::get('/session-active', [SessionController::class, 'sessionActive']);

//finalizar sessão ativa
Route::post('/finishing-session/{id}', [SessionController::class, 'finishingSession']);

//iniciar sessão e idUser
Route::post('/start-participation', [InfoParticipationController::class, "startParticipation"]);

//recuperar foto
Route::get('/get-photo/{id}', [InfoParticipationController::class, "getPhoto"]);

//informações sobre as participações
Route::get('/info-zabbix', [InfoParticipationController::class, "infoZabbix"]);