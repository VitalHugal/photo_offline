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
}