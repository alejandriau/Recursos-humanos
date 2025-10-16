<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\pasivodos;

class Seleccion extends Model
{


    protected $table = 'seleccions';
    protected $primaryKey = 'id';   // por defecto es 'id', pero asegúrate si es distinto
    public $timestamps = false;

    protected $fillable = [
        'idPasivodos',
        'registro',
        'user_id'
    ];

    // Relación inversa: muchos seleccionados pertenecen a un pasivo
    public function pasivodos()
    {
        return $this->belongsTo(Pasivodos::class, 'idPasivodos');
    }

    // Relación con el modelo User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
