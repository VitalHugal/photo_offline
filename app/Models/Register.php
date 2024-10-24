<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Register extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'CPF', 'CPF_hash', 'adulthood', 'fk_id_photo', 'responsible_name', 'responsible_cpf', 'responsible_cpf_hash'];
    protected $table = 'user_registers';
    protected $dates = ['deleted_at'];

    public function rulesRegister()
    {
        return [
            "adulthood" => 'required|boolean|in:0,1',
            'name' => 'required|max:255',
            "CPF" => 'required|digits:11',
            "CPF_hash" => '',
            "fk_id_photo" => '',
        ];
    }
    
    public function rulesRegisterResponsible()
    {
        return [
            'responsible_name' => 'required|max:255|',
            'responsible_cpf' => 'required|digits:11|', 
            'responsible_cpf_hash' => '',
        ];
    }

    public function feedbackRegister()
    {
        return [
            'required' => 'Campo obrigatório.',
            'max:255' => 'O campo deve conter até 255 caracteres.',
            'digits:11' => 'O campo deve conter até 11 dígitos.',
            'boolean' => 'Valido apenas 0 ou 1.',
        ];
    }
    
    public function feedbackRegisterResponsible()
    {
        return [
            'required' => 'Campo obrigatório.',
            'max:255' => 'O campo deve conter até 255 caracteres.',
            'digits:11' => 'O campo deve conter até 11 dígitos.',
        ];
    }
}