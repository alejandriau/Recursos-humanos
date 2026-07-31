<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AsistenciaDiariaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'persona' => [
                'id' => $this->persona_id,
                'nombre' => $this->persona?->nombre ?? $this->persona?->nombres ?? 'N/A',
            ],
            'fecha' => $this->fecha,
            'estado' => $this->estado,
            'estado_label' => $this->estadoLabel(),
            'minutos_tardanza' => $this->minutos_tardanza,
            'total_marcas_esperadas' => $this->total_marcas_esperadas,
            'total_marcas_cumplidas' => $this->total_marcas_cumplidas,
            'total_marcas_justificadas' => $this->total_marcas_justificadas,
            'total_marcas_injustificadas' => $this->total_marcas_injustificadas,
            'procesado_en' => $this->procesado_en,
            
            // whenLoaded a nivel de Resource, no del modelo
            'marcas' => $this->whenLoaded('marcas', function () {
                return $this->marcas->map(function ($marca) {
                    return [
                        'tipo_marca' => $marca->tipo_marca,
                        'hora_esperada' => $marca->hora_esperada,
                        'hora_real' => $marca->hora_real,
                        'estado' => $marca->estado,
                        'diferencia_minutos' => $marca->diferencia_minutos,
                        // Verificamos si la relación salida está cargada y existe
                        'justificacion' => $marca->relationLoaded('salida') && $marca->salida
                            ? [
                                'tipo' => $marca->salida->tipo_salida ?? $marca->salida->tipo ?? null,
                                'motivo' => $marca->salida->motivo ?? null,
                            ]
                            : null,
                    ];
                });
            }),
        ];
    }
}