<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feriado extends Model
{
    protected $table = 'feriados';

    protected $fillable = [
        'fechaf',
        'descripcion',
        'gestion_id',
    ];

    protected $casts = [
        'fechaf' => 'date',
    ];

    public function gestion()
    {
        return $this->belongsTo(Gestion::class);
    }
}