<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Http\Controllers\Controller;
use App\Models\InfoParticipation;
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
        //pega o ultimo id que esteja em progresso
        $session = Session::where('start_time', 1)->where('in_progress', 1)->where('end_time', 0)->latest()->first();

        // caso seja vazio disponivel
        if ($session === null) {
            return response()->json([
                'success' => true,
                'message' => 'Disponível para iniciar sessão',
            ]);
        }

        // caso seja diferente de vazio seção em andamento
        if ($session !== null) {
            $idSession = $session->id;
            return response()->json([
                'success' => false,
                'message' => 'Sessão em andamento.',
                'data' => $idSession
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

        $name_photo = $request->input('name_photo');

        //se não houver nada na requisição encerra por tempo excedido
        if ($name_photo === null) {
            
            Session::where('id', $id)->update(['end_time' => 1]);

            InfoParticipation::where('id', $id)->update(['end_participation' => $formatedDate]);

            return response()->json([
                'success' => false,
                'message' => 'Sessão finalizada, tempo de participação excedido.'
            ]);
        }

        Session::where('id', $id)->update(['end_time' => 1]);

        InfoParticipation::where('id', $id)->update(['name_photo' => $name_photo ,'end_participation' => $formatedDate]);

        return response()->json([
            'success' => true,
            'message' => 'Sessão finalizada.'
        ]);
    }
}