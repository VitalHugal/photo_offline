<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Http\Controllers\Controller;
use App\Models\InfoParticipation;
use App\Models\Register;
use DateTime;
use DateTimeZone;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    protected $session;
    protected $info;

    public function __construct(Session $session, InfoParticipation $info)
    {
        $this->session = $session;
        $this->info = $info;
    }

    public function participationActive()
    {
        try {
            $idParticipation = InfoParticipation::orderBy('id', 'desc')->first();

            $participationStar = $idParticipation->start_participation;
            $participationEnd = $idParticipation->end_participation;

            $idParticipationId =  $idParticipation->id;

            if ($participationStar == true && $participationEnd == null) {
                return response()->json([
                    'success' => true,
                    'message' => 'Participação em andamento.',
                    'data' => $idParticipationId,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Disponível para iniciar participação.',
                ]);
            }
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

    public function finishing(Request $request, $id)
    {
        try {
            $finishing = $this->info->find($id);

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

            $idParticipation = InfoParticipation::where('id', $id)->first();

            if ($idParticipation) {
                $idParticipationId = $idParticipation->id;
            }

            $register = $idParticipation->register;

            if ($register == 'false') {

                $session = Session::where('start_time', 1)->where('in_progress', 1)->where('end_time', 0)->latest()->first();

                //add
                if (!$session) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Nenhuma sessão para finalizar.'
                    ]);
                }
                
                $idSession = $session->id;
                //add

                //se não houver nada na requisição encerra por tempo excedido
                if ($name_photo === null) {

                    Session::where('id', $idSession)->update(['end_time' => 1]);
                    InfoParticipation::where('id', $idParticipationId)->update(['end_participation' => $formatedDate]);

                    return response()->json([
                        'success' => false,
                        'message' => 'Sessão finalizada, tempo de participação excedido.'
                    ]);
                }

                Session::where('id', $idSession)->update(['end_time' => 1]);
                InfoParticipation::where('id', $idParticipationId)->update(['name_photo' => $name_photo, 'end_participation' => $formatedDate]);

                return response()->json([
                    'success' => true,
                    'message' => 'Sessão finalizada.'
                ]);
            }

            $idUser = Register::where('fk_id_photo', $id)->first();

            if ($idUser) {
                $idUserId = $idUser->id;
                $idSession = $idUser->fk_id_session;
            }

            //se não houver nada na requisição encerra por tempo excedido
            if (!$name_photo) {

                Session::where('id', $idSession)->update(['end_time' => 1]);

                InfoParticipation::where('id', $idParticipationId)->update(['end_participation' => $formatedDate]);

                Register::where('id', $idUserId)->update(['fk_id_photo' => null]);

                return response()->json([
                    'success' => false,
                    'message' => 'Sessão finalizada, tempo de participação excedido.'
                ]);
            }

            Session::where('id', $idSession)->update(['end_time' => 1]);

            InfoParticipation::where('id', $idParticipationId)->update(['name_photo' => $name_photo, 'end_participation' => $formatedDate]);

            Register::where('id', $idUserId)->update(['fk_id_photo' => $idParticipationId]);

            return response()->json([
                'success' => true,
                'message' => 'Sessão finalizada.'
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

    public function finishingSessionForce()
    {
        try {
            $session = Session::orderBy('id', 'desc')->first();

            if ($session) {
                $idSession = $session->id;
            }

            if ($session->end_time == 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nenhuma sessão ativa para finalizar.',
                ]);
            }

            $date = new DateTime();
            $serverTimezone = $date->getTimezone()->getName();
            $saoPauloTimezone = 'America/Sao_Paulo';

            if ($serverTimezone !== $saoPauloTimezone) {
                $date->setTimezone(new DateTimeZone($saoPauloTimezone));
            }

            $formatedDate = $date->format('d-m-Y H:i:s');

            $idParticipation = InfoParticipation::orderBy('id', 'desc')->first();

            if ($idParticipation) {
                $participationStar = $idParticipation->start_participation;
                $participationEnd = $idParticipation->end_participation;
                $idParticipationId =  $idParticipation->id;


                if ($participationStar == true && $participationEnd == null) {

                    InfoParticipation::where('id', $idParticipationId)->update(['name_photo' => null, 'end_participation' => $formatedDate]);
                    Session::where('id', $idSession)->update(['end_time' => 1]);
                }

            }

            Session::where('id', $idSession)->update(['end_time' => 1]);

            return response()->json([
                'success' => true,
                'message' => 'Sessão finalizada.',
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