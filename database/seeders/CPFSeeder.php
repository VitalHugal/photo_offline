<?php

namespace Database\Seeders;

use App\Http\Controllers\Encrypt;
use DateTime;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CPFSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        function gerarNum($int, $int2)
        {
            for ($i = 0; $i < 11; $i++) {
                $abcd[] = rand($int, $int2);
            }

            return implode($abcd);
        }

        $date = new DateTime();
        $formatedDate = $date->format('d-m-Y H:i:s');

        for ($i = 0; $i < 100; $i++) {
            DB::table('info_participations')->insert([
                'name' => Str::random(10),
                'email' => Str::random(10) . '@example.com',
                'CPF' => $aqui = gerarNum(0, 9),
                'hash_CPF' => Hash::make($aqui),
                'start_participation' => $formatedDate,
                'end_participation' => $formatedDate,
                'name_photo' => 'photo_pesada.jpg',
            ]);
            DB::table('session')->insert([
                'start_time' => 1,
                'in_progress' => 1,
                'end_time' => '1',
            ]);
        }
    }
}