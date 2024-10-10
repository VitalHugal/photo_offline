<?php

namespace App\Http\Controllers;

use App\Models\InfoParticipation;
use App\Http\Controllers\Controller;
use App\Models\Section;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;

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

        $info = $this->info->create([
            'start_participation' => $formatedDate,
        ]);

        if (!$info) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar idUser',
            ]);
        }

        $section = Section::create([
            'start_time' => 1,
            'in_progress'  => 1,
        ]);

        if (!$section) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao criar idSection',
            ]);
        }

        $idSection = $section->id;

        return response()->json([
            'success' => true,
            'message' => 'Usuario e seção criados com sucesso.',
            'data' => ['idUser' => $info, 'idSection' => $idSection],
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