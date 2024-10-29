<?php

use App\Http\Controllers\InfoParticipationController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\teste;
use App\Models\InfoParticipation;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [RegisterController::class, 'register']);

//verficar se tem sessão disponivel ou não
Route::get('/participation-active', [SessionController::class, 'participationActive']); 

//finalizar sessão ativa
Route::post('/finishing-participation/{id}', [SessionController::class, 'finishing']);

//iniciar sessão e idUser
Route::post('/start-participation', [InfoParticipationController::class, "startParticipation"]);

//recuperar foto
Route::get('/get-photo/{id}', [InfoParticipationController::class, "getPhoto"]);

//download foto
Route::get('/download-photo/{id}', [InfoParticipationController::class, "downloadPhoto"]);

//informações sobre as participações
Route::get('/info-zabbix', [InfoParticipationController::class, "infoZabbix"]);

//finalizando a seção que esta em andamento de forma forçada
Route::post('/finishing-session-force', [SessionController::class , 'finishingSessionForce']);