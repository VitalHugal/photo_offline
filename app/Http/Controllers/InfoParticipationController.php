<?php

namespace App\Http\Controllers;

use App\Models\InfoParticipation;
use App\Http\Controllers\Controller;
use App\Models\Session;
use DateTime;
use DateTimeZone;

class InfoParticipationController extends Controller
{
    protected $info;

    public function __construct(InfoParticipation $info)
    {
        $this->info = $info;
    }

    public function startParticipation()
    {
        $date = new DateTime();
        $serverTimezone = $date->getTimezone()->getName();
        $saoPauloTimezone = 'America/Sao_Paulo';

        if ($serverTimezone !== $saoPauloTimezone) {
            $date->setTimezone(new DateTimeZone($saoPauloTimezone));
        }

        $formatedDate = $date->format('d-m-Y H:i:s');

        //pega o ultimo id que esteja em em progresso
        $session = Session::where('start_time', 1)->where('in_progress', 1)->where('end_time', 0)->latest()->first();

        // caso seja diferente de vazio seção em andamento
        if ($session !== null) {

            $idSession = $session->id;
            
            return response()->json([
                'success' => false,
                'message' => 'Sessão em andamento.',
                'data' => $idSession
            ]);
        }

        $info = $this->info->create([
            'start_participation' => $formatedDate,
        ]);

        if (!$info) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar idUser',
            ]);
        }

        $session = Session::create([
            'start_time' => 1,
            'in_progress'  => 1,
        ]);

        if (!$session) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar idSection',
            ]);
        }

        $idSession = $session->id;

        return response()->json([
            'success' => true,
            'message' => 'Usuario e seção criados com sucesso.',
            'data' => ['idUser' => $info, 'idSection' => $idSession],
        ]);
    }

    public function getPhoto($id)
    {
        $idParticipation = $this->info->find($id);

        if ($idParticipation === null) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhum resultado encontrado.',
            ]);
        }

        $photo = $idParticipation->name_photo;

        if ($photo === null) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhuma foto encontrada.',
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $photo,
        ]);
    }
}