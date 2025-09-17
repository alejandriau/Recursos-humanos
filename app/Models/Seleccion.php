<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\pasivodos;

class Seleccion extends Model
{


    protected $table = 'seleccions';
    protected $primaryKey = 'id';   // por defecto es 'id', pero asegúrate si es distinto
    public $timestamps = false;

    protected $fillable = [
        'idPasivodos',
        'registro',
    ];

    // Relación inversa: muchos seleccionados pertenecen a un pasivo
    public function pasivodos()
    {
        return $this->belongsTo(Pasivodos::class, 'idPasivodos');
    }

}
