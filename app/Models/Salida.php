<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salida extends Model
{
    use HasFactory;
    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }
    public function tiposalida()
    {
        return $this->belongsTo(Tiposalida::class);
    }
    public function nvobo()
    {
        return $this->belongsTo(Persona::class, 'id_vobo');
    }
}
