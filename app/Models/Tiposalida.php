<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tiposalida extends Model
{
    use HasFactory;
    public function beneficios()
    {
        return $this->hasMany(Beneficio::class);
    }
    public function salidas(){
        return $this->hasMany(Salida::class);
    }
}
