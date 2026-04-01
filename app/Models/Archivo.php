<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Archivo extends Model
{
    protected $fillable = [
        'titulo', 'nombre_archivo', 'ruta', 
        'extension', 'tipo', 'tema', 'descripcion', 'tamano'
    ];
    
    public function getRutaCompletaAttribute()
    {
        return storage_path('app/public/' . $this->ruta);
    }
    
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->ruta);
    }
}
