<?php

namespace App\Http\Controllers;

use App\Models\Section;
use App\Http\Controllers\Controller;
use App\Models\InfoParticipation;
use DateInterval;
use DateTime;
use DateTimeZone;
use Illuminate\Http\Request;
use Symfony\Polyfill\Intl\Idn\Info;

class SectionController extends Controller
{
    protected $section;

    public function __construct(Section $section)
    {
        $this->section = $section;
    }

    public function sectionActive()
    {
        //pega o ultimo id que esteja em em progresso
        $section = Section::where('start_time', 1)->where('in_progress', 1)->where('end_time', 0)->latest()->first();

        // caso seja vazio disponivel
        if ($section === null) {
            return response()->json([
                'success' => true,
                'message' => 'Disponível para iniciar sessão',
            ]);
        }

        // caso seja diferente de vazio seção em andamento
        if ($section !== null) {
            $idSection = $section->id;
            return response()->json([
                'success' => false,
                'message' => 'Sessão em andamento.',
                'data' => $idSection
            ]);
        }
    }

    public function finishingSection(Request $request, $id)
    {
        $finishing = $this->section->find($id);

        // se id não existir retorna erro
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

            $finishing = Section::where('id', $id)->update(['end_time' => 1]);

            InfoParticipation::where('id', $id)->update(['end_participation' => $formatedDate]);

            return response()->json([
                'success' => false,
                'message' => 'Sessão finalizada, tempo de participação excedido.'
            ]);
        }

        $finishing = Section::where('id', $id)->update(['end_time' => 1]);

        InfoParticipation::where('id', $id)->update(['name_photo' => $name_photo ,'end_participation' => $formatedDate]);

        return response()->json([
            'success' => true,
            'message' => 'Sessão finalizada.'
        ]);
    }
}