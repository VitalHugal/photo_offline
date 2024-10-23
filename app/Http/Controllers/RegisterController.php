<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Register;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

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
            $register = $request->validate(
                $this->register->rulesRegister(),
                $this->register->feedbackRegister()
            );


            $CPF = $request->CPF;
            $telephone = $request->telephone;
            $CPF_hash = password_hash($CPF, PASSWORD_BCRYPT);

            if ($register) {
                $e = new Encrypt();
                $CPF = $e->encrypt($CPF);
                $telephone = $e->encrypt($telephone);
            }

            $hashes = Register::pluck('CPF_hash');

            $exists = false;

            for ($i = 0; $i < count($hashes); $i++) {
                $verify = password_verify($request->CPF, $hashes[$i]);
                if ($verify) {
                    $exists = true;
                    break;
                }
            }

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário já cadastrado.'
                ]);
            }

            $register = $this->register->create([
                'CPF' => $CPF,
                'CPF_hash' => $CPF_hash,
                'telephone' => $telephone,
            ]);

            if ($register) {
                return response()->json([
                    'success' => true,
                    'message' => 'Cadastrado com sucesso.',
                    'data' => ['idUser' => $register['id']],
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