<?php

namespace App\Http\Controllers;

use App\Models\InfoParticipation;
use App\Http\Controllers\Controller;
use App\Models\Session;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class InfoParticipationController extends Controller
{
    protected $info;

    public function __construct(InfoParticipation $info)
    {
        $this->info = $info;
    }

    public function startParticipation(Request $request)
    {
        try {
            //verfica se o server esta com dateTime definido para america/sao_paulo senão atualiza e formata datetime para o padrão BR
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

            $info = $request->validate(
                $this->info->rulesParticipation(),
                $this->info->feedbackParticipation()
            );

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
                'data' => ['idUser' => $info['id'], 'idSection' => $idSession],
            ]);
        } catch (QueryException $qe) {
            return response()->json([
                'success' => false,
                'message' => 'Error DB: ' . $qe->getMessage(),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }

    public function getPhoto($id)
    {
        $idParticipation = $this->info->find($id);

        if ($idParticipation === null) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhum id encontrado.',
            ]);
        }

        $session = Session::where('id', $id)->where('start_time', 1)->where('in_progress', 1)->where('end_time', 0)->first();

        if ($session !== null) {
            return response()->json([
                'success' => false,
                'message' => 'Em andamento...',
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

    public function infoZabbix()
    {
        $infoParticipations = InfoParticipation::all();

        $infoPhoto = InfoParticipation::whereNotNull('name_photo')->get();

        $participations = count($infoParticipations);

        $photos = count($infoPhoto);

        return response()->json([
            'success' => true,
            'participation' => $participations,
            'images_sent' =>  $photos,
        ]);
    }
}