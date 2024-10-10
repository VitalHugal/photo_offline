<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{

    use HasFactory, SoftDeletes;

    protected $fillable = ['start_time', 'in_progress', 'end_time',];
    protected $table = 'sections';
    protected $dates = 'deleted_at';

    public function rulesSection()
    {
        return [
            'start_time' => 'in:1',
            'in_progress' => 'in:1',
            'end_time' =>  'in:1',
        ];
    }
    public function feedbackSection()
    {
        return [
            'in' => 'Válido apenas 1.',
        ];
    }
}