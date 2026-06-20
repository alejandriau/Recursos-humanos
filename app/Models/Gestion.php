<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gestion extends Model
{
    use HasFactory;
    public function beneficios()
    {
        return $this->hasMany(Beneficio::class);
    }
    public function feriados(){
        return $this->hasMany(Feriado::class);
    }


}
