<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserRegister extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['telephone', 'CPF', 'CPF_hash'];
    protected $table = 'user_registers';
    protected $dates = ['deleted_at'];

    public function rulesRegister()
    {
        return [
            'telephone' => 'required|digits:9',
            "CPF" => 'required|digits:11|',
            "CPF_hash" => '',
        ];
    }

    public function feedbackRegister()
    {
        return [
            'required' => 'Campo obrigatório.',
            'digits:9' => 'O campo deve conter até 9 dígitos.',
            'digits:11' => 'O campo deve conter até 11 dígitos.',
        ];
    }
}