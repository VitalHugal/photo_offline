<?php

namespace App\Http\Controllers;

use App\Models\InfoParticipation;
use App\Http\Controllers\Controller;
use App\Models\Register;
use App\Models\Session;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


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

            $session = Session::where('start_time', 1)->where('in_progress', 1)->where('end_time', 0)->latest()->first();

            $checksTheNeedForRegistration = $request->register;

            if ($session == false && ($checksTheNeedForRegistration === true)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Necessita de cadastro.'
                ]);
            }

            $idUser = Register::orderBy('id', 'desc')->first();

            $idUserId = $idUser ? $idUser->id : null;

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

            $idSession = $session ? $session->id : null;

            $idParticipation = InfoParticipation::orderBy('id', 'desc')->first();

            if ($idParticipation) {

                $startParticipation = $idParticipation ? $idParticipation->start_participation : null;
                $endParticipation = $idParticipation ? $idParticipation->end_participation : null;

                if ($startParticipation == true && $endParticipation == null) {
                    return response()->json([
                        'success' => false,
                        'message' => "Participação em andamento.",
                        'data' => ['idParticipation' => $idParticipation->id],
                    ]);
                }
            }

            // if ($idParticipationId >= $idSession) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Sessão em andamento.',
            //         'data' => $idSession
            //     ]);
            // }

            // if (!$session) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Sessão em andamento.',
            //         'data' => $idSession
            //     ]);
            // }

            if ($checksTheNeedForRegistration == false) {

                $info = $request->validate(
                    $this->info->rulesParticipation(),
                    $this->info->feedbackParticipation()
                );

                $info = $this->info->create([
                    'start_participation' => $formatedDate,
                ]);

                if (!$info) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Erro ao criar idParticpation',
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

                $directoryPath = 'images';

                if (!Storage::disk('public')->exists($directoryPath)) {
                    Storage::disk('public')->makeDirectory($directoryPath);
                }


                $idSession = $session->id;

                return response()->json([
                    'success' => true,
                    'message' => 'Usuario e seção criados com sucesso.',
                    'data' => ['idParticipation' => $info->id, 'idSection' => $idSession],
                ]);
            }

            $info = $request->validate(
                $this->info->rulesParticipation(),
                $this->info->feedbackParticipation()
            );

            $info = $this->info->create([
                'start_participation' => $formatedDate,
            ]);

            if (!$info) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao criar idParticpation',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Participação iniciada',
                'data' => ['idParticpation' => $info['id']],
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
        try {
            $idParticipation = $this->info->find($id);

            if ($idParticipation === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhum id encontrado.',
                ]);
            }

            $idParticipation = InfoParticipation::orderBy('id', 'desc')->first();

            if ($idParticipation) {

                $startParticipation = $idParticipation ? $idParticipation->start_participation : null;
                $endParticipation = $idParticipation ? $idParticipation->end_participation : null;

                if ($startParticipation == true && $endParticipation == null) {
                    return response()->json([
                        'success' => false,
                        'message' => "Participação em andamento.",
                        'data' => ['idParticipation' => $idParticipation->id],
                    ]);
                }
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

    public function downloadPhoto($id)
    {
        try {
            $idParticipation = $this->info->find($id);

            if ($idParticipation === null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhum id encontrado.',
                ]);
            }

            $photo = $idParticipation->name_photo;

            if (!$photo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma foto encontrada.',
                ]);
            }

            return Storage::disk('public')->download($photo);
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

    public function infoZabbix()
    {
        try {
            $infoParticipations = InfoParticipation::all();

            $infoPhoto = InfoParticipation::whereNotNull('name_photo')->get();

            $participations = count($infoParticipations);

            $photos = count($infoPhoto);

            return response()->json([
                'success' => true,
                'participation' => $participations,
                'images_sent' =>  $photos,
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
}