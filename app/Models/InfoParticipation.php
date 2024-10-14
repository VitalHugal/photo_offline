<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InfoParticipation extends Model
{

    use HasFactory, SoftDeletes;

    protected $fillable = ['start_participation', 'end_participation', 'name_photo', 'name', "email", "CPF"];
    protected $table = 'info_participations';
    protected $dates = 'deleted_at';

    public function rulesParticipation()
    {
        return [
            'start_participation' => 'in:1|nullable',
            'end_participation' =>  'in:1|nullable',
            'name_photo' =>  'max:255|nullable',
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            "CPF" => 'required|digits:11',
        ];
    }
    public function feedbackParticipation()
    {
        return [
            'in' => 'Válido apenas 1.',
            'max:255' => 'Válido até 255 caracteres.',
            'max:11' => 'CPF deve conter 11 dígitos.',
            'required' => 'Campo obrigatório.',
            'email' => 'E-mail inválido.',
            'digits' => 'O CPF deve conter 11 dígitos.',
            
        ];
    }
}