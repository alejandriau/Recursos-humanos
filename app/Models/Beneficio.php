<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beneficio extends Model
{
    use HasFactory;
    protected $fillable = [
        'cantidad',
        'tiposalida_id',
        'persona_id',
        'gestion_id',
    ];
    public function tiposalida()
    {
        return $this->belongsTo(Tiposalida::class);
    }
    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }
    public function gestion()
    {
        return $this->belongsTo(Gestion::class);
    }

}
