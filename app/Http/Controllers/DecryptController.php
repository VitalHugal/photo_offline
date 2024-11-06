<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DecryptController extends Controller
{

    public function decrypt()
    {
        $data = DB::table('decrypt_info')->get();
        
        $d = new Encrypt();

        foreach ($data as $item) {
            try {
                $decryptedName = $d->decrypt($item->name);
                $decryptedCPF = $d->decrypt($item->CPF);

                DB::table('decrypt_info')
                    ->where('id', $item->id)
                    ->update([
                        'name' => $decryptedName,
                        'CPF' => $decryptedCPF,
                    ]);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error:' . $e->getMessage(),
                ]);
            }
        }
    }
}