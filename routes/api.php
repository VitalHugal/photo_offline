<?php

use App\Http\Controllers\InfoParticipationController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\teste;
use App\Models\InfoParticipation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [RegisterController::class, 'register']);

//verficar se tem sessão disponivel ou não
Route::get('/session-active', [SessionController::class, 'sessionActive']); //verfica se tem participação ativa DEPOIS MUDAR NOME DA ROTA E METODO

// //verficar se tem participation disponivel ou não
// Route::get('/participation-active', [InfoParticipationController::class, 'participationActive']);

//finalizar sessão ativa
Route::post('/finishing-session/{id}', [SessionController::class, 'finishingSession']);

//iniciar sessão e idUser
Route::post('/start-participation', [InfoParticipationController::class, "startParticipation"]);

//recuperar foto
Route::get('/get-photo/{id}', [InfoParticipationController::class, "getPhoto"]);

//download foto
Route::get('/download-photo/{id}', [InfoParticipationController::class, "downloadPhoto"]);

//informações sobre as participações
Route::get('/info-zabbix', [InfoParticipationController::class, "infoZabbix"]);