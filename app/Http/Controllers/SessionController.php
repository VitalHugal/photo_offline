<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Http\Controllers\Controller;
use App\Models\InfoParticipation;
use App\Models\Register;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    protected $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function sessionActive()
    {
        $idParticipation = InfoParticipation::orderBy('id', 'desc')->first();

        $participationStar = $idParticipation->start_participation;
        $participationEnd = $idParticipation->end_participation;

        $idParticipationId =  $idParticipation->id;

        if ($participationStar == true && $participationEnd == null) {
            return response()->json([
                'success' => false,
                'message' => 'Participação em andamento.',
                'data' => $idParticipationId,
            ]);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Disponível para iniciar participação.',
            ]);
        }
    }

    public function finishingSession(Request $request, $id)
    {
        $finishing = $this->session->find($id);

        if ($finishing === null) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhum resultado encontrado.',
            ]);
        }

        $date = new DateTime();
        $serverTimezone = $date->getTimezone()->getName();
        $saoPauloTimezone = 'America/Sao_Paulo';

        if ($serverTimezone !== $saoPauloTimezone) {
            $date->setTimezone(new DateTimeZone($saoPauloTimezone));
        }

        $formatedDate = $date->format('d-m-Y H:i:s');

        // $name_photo = $request->file('name_photo');


        $name_photo = $request->input('name_photo');

        // ////
        // $logo_imagem = $request->file('name_photo');
        // $logo_imagem_urn = $logo_imagem->store('images', 'public');
        // dd($logo_imagem_urn);
        // ////

        $idParticipation = InfoParticipation::orderBy('id', 'desc')->first();

        if ($idParticipation) {
            $idParticipationId = $idParticipation->id ?? null;
        }
        
        $idUser = Register::orderBy('id', 'desc')->first();

        if ($idUser) {
            $idUserId = $idUser->id ?? null;
        }

        //se não houver nada na requisição encerra por tempo excedido
        if (!$name_photo) {

            Session::where('id', $id)->update(['end_time' => 1]);

            InfoParticipation::where('id', $idParticipationId)->update(['end_participation' => $formatedDate]);

            Register::where('id', $idUserId)->update(['fk_id_photo' => null]);


            return response()->json([
                'success' => false,
                'message' => 'Sessão finalizada, tempo de participação excedido.'
            ]);
        }

        Session::where('id', $id)->update(['end_time' => 1]);

        InfoParticipation::where('id', $idParticipationId)->update(['name_photo' => $name_photo, 'end_participation' => $formatedDate]);

        Register::where('id', $idUserId)->update(['fk_id_photo' => $idParticipationId]);

        return response()->json([
            'success' => true,
            'message' => 'Sessão finalizada.'
        ]);
    }
}