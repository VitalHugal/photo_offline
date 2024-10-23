<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InfoParticipation extends Model
{

    use HasFactory, SoftDeletes;

    protected $fillable = ['start_participation', 'end_participation', 'name_photo', 'telephone','CPF'];
    protected $table = 'info_participations';
    protected $dates = 'deleted_at';

    public function rulesParticipation()
    {
        return [
            'start_participation' => 'in:1|nullable',
            'end_participation' =>  'in:1|nullable',
            'name_photo' =>  'max:255|nullable',
            'telephone' => 'required|max:11',
            "CPF" => 'required',
        ];
    }
    public function feedbackParticipation()
    {
        return [
            'in' => 'Válido apenas 1.',
            'max:255' => 'Válido até 255 caracteres.',
            'required' => 'Campo obrigatório.',
            'max:11' => 'O campo deve conter até 11 dígitos.',           
        ];
    }
}