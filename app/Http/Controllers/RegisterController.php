<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Register;
use App\Models\Session;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RegisterController extends Controller
{
    protected $register;

    public function __construct(Register $register)
    {
        $this->register = $register;
    }
    public function register(Request $request)
    {
        try {

            $checksTheNeedForRegistration = $request->register;

            if ($checksTheNeedForRegistration == 'false' || $checksTheNeedForRegistration == 'False') {
                return response()->json([
                    'success' => false,
                    'message' => 'Não necessita de cadastro.'
                ]);
            }

            $register = $request->validate(
                $this->register->rulesRegister(),
                $this->register->feedbackRegister()
            );

            if ($register) {
                $CPF = $request->CPF;
                $name = $request->name;
                $CPF_hash = password_hash($CPF, PASSWORD_BCRYPT);
                $adulthood = $request->adulthood;

                $e = new Encrypt();
                $CPF = $e->encrypt($CPF);
                $name = $e->encrypt($name);
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
                    'data' => ['idSession' => $idSession]
                ]);
            }

            if ($request->adulthood == 1) {
                $register = $this->register->create([
                    'CPF' => $CPF,
                    'CPF_hash' => $CPF_hash,
                    'name' => $name,
                    'adulthood' => $adulthood,
                ]);
            }

            if ($request->adulthood == 0) {

                $register = $request->validate(
                    $this->register->rulesRegisterResponsible(),
                    $this->register->feedbackRegisterResponsible(),
                );

                if ($register) {
                    $responsible_cpf = $request->responsible_cpf;
                    $responsible_name = $request->responsible_name;
                    $responsible_cpf_hash = password_hash($responsible_cpf, PASSWORD_BCRYPT);

                    $e = new Encrypt();
                    $responsible_cpf = $e->encrypt($responsible_cpf);
                    $responsible_name = $e->encrypt($responsible_name);
                }

                $register = $this->register->create([
                    'CPF' => $CPF,
                    'CPF_hash' => $CPF_hash,
                    'name' => $name,
                    'adulthood' => $adulthood,
                    'responsible_cpf' => $responsible_cpf,
                    'responsible_cpf_hash' => $responsible_cpf_hash,
                    'responsible_name' => $responsible_name,
                ]);
            }

            if (!$register) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao cadastrar usuário.',
                ]);
            }

            if ($register) {

                $directoryPath = 'images';

                if (!Storage::disk('public')->exists($directoryPath)) {
                    Storage::disk('public')->makeDirectory($directoryPath);
                }

                $session = Session::create([
                    'start_time' => 1,
                    'in_progress'  => 1,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Cadastrado com sucesso.',
                    'data' => [
                        'idUser' => $register['id'],
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