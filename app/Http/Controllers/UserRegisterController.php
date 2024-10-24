<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\UserRegister;
use App\Models\Session;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class UserRegisterController extends Controller
{
    protected $user_register;

    public function __construct(UserRegister $user_register)
    {
        $this->user_register = $user_register;
    }
    public function register(Request $request)
    {
        try {

            $user_register = $request->validate([
                $this->user_register->rulesRegister(),
                $this->user_register->feedbackRegister()
            ]);

            $CPF = $request->CPF;
            $telephone = $request->telephone;
            $CPF_hash = password_hash($CPF, PASSWORD_BCRYPT);
            $adulthood = $request->adulthood;

            if ($user_register) {
                $e = new Encrypt();
                $CPF = $e->encrypt($CPF);
                $telephone = $e->encrypt($telephone);
            }

            // $hashes = Register::pluck('CPF_hash');

            // $exists = false;

            // for ($i = 0; $i < count($hashes); $i++) {
            //     $verify = password_verify($request->CPF, $hashes[$i]);
            //     if ($verify) {
            //         $exists = true;
            //         break;
            //     }
            // }

            // if ($exists) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Usuário já cadastrado.'
            //     ]);
            // }

            //pega o ultimo id que esteja em em progresso
            $session = Session::where('start_time', 1)->where('in_progress', 1)->where('end_time', 0)->latest()->first();

            if ($session !== null) {

                $idSession = $session->id;

                return response()->json([
                    'success' => false,
                    'message' => 'Sessão em andamento.',
                    'data' => $idSession
                ]);
            }

            $user_register = $this->user_register->create([
                'CPF' => $CPF,
                'CPF_hash' => $CPF_hash,
                'telephone' => $telephone,
                'adulthood' => $adulthood,
            ]);

            if (!$user_register) {
                return response()->json([
                    'success' => true,
                    'message' => 'Erro ao cadastrar usuário.',
                ]);
            }

            if ($user_register) {

                $session = Session::create([
                    'start_time' => 1,
                    'in_progress'  => 1,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Cadastrado com sucesso.',
                    'data' => [
                        'idUser' => $user_register['id'],
                        'idSession' => $session->id
                    ],
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
}
