<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InfoParticipation extends Model
{

    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'start_participation', 'end_participation', 'url_photo'];
    protected $table = 'info_participations';
    protected $dates = 'deleted_at';
}