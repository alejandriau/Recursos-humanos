<?php

namespace App\Imports;

use App\Models\Persona;
use App\Models\Planilla;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Maatwebsite\Excel\Validators\ValidationException;
use Maatwebsite\Excel\Exceptions\NoTypeDetectedException;
use Illuminate\Support\Facades\Log;

class PlanillaImport implements ToModel, WithHeadingRow, WithChunkReading
{
    protected $anio;
    protected $mes;
    protected $tipo;
    
    public function __construct($anio, $mes, $tipo)
    {
        $this->anio = $anio;
        $this->mes = $mes;
        $this->tipo = $tipo;
    }
    
    public function model(array $row)
    {
        try {
            // 1. Normalizar campos (los nombres de columna vienen del Excel)
            $ci = strtoupper(trim($row['carnet'] ?? ''));
            $ci = preg_replace('/[^A-Z0-9]/', '', $ci); // mantiene letras y números
            
            if (empty($ci)) return null;
            
            // 2. Buscar o crear persona
            $persona = Persona::where('ci', $ci)->first();
            
            if (!$persona) {
                $nombreCompleto = trim($row['nombre'] ?? '');
                $partes = preg_split('/\s+/', $nombreCompleto);

                $apellidoPat = '';
                $apellidoMat = '';
                $nombres = '';

                if (count($partes) == 1) {
                    $nombres = $partes[0];
                } elseif (count($partes) == 2) {
                    $apellidoPat = $partes[0];
                    $nombres = $partes[1];
                } else {
                    $apellidoPat = $partes[0];
                    $apellidoMat = $partes[1];
                    $nombres = implode(' ', array_slice($partes, 2));
                }
                
                $persona = Persona::firstOrCreate([
                    'ci' => $ci,
                    'nombre' => $nombres ?: $nombreCompleto,
                    'apellidoPat' => $apellidoPat,
                    'apellidoMat' => $apellidoMat,
                    'fechaIngreso' => $this->parseFecha($row['f_ingreso'] ?? null),   // ← se guarda en persona
                    'fechaNacimiento' => $this->parseFecha($row['f_naci'] ?? null),
                    'sexo' => $row['s'] ?? null,
                    'telefono' => null,
                    'observaciones' => null,
                    'estado' => 1,
                    'user_id' => null,
                    'archivo' => null,
                ]);
            }
            
            // 3. Crear registro de planilla
            return new Planilla([
                'persona_id' => $persona->id,
                'anio' => $this->anio,
                'mes' => $this->mes,
                'tipo' => $this->tipo,
                'num' => $row['num'] ?? null,
                'expedido' => $row['exp'] ?? null,
                'partida' => $row['partida'] ?? null,
                'indi' => $row['indi'] ?? null,
                'cargo' => $row['cargo'] ?? null,
                'h_basico' => $this->parseDecimal($row['h_basico'] ?? null),
                'h_basejec' => $this->parseDecimal($row['h_basejec'] ?? null),
                'viatico' => $this->parseDecimal($row['viatico'] ?? null),
                'fecha_ingreso' => $this->parseFecha($row['f_ingreso'] ?? null),
                'fecha_vencimiento' => $this->parseFecha($row['f_vencmto'] ?? null),
                'categ' => $this->parseDecimal($row['categ'] ?? null),
                'categejec' => $this->parseDecimal($row['categejec'] ?? null),
                'tot_gan' => $this->parseDecimal($row['tot_gan'] ?? null),
                'dia_trab' => $row['dia_trab'] ?? null,
                'neto' => $this->parseDecimal($row['neto'] ?? null),
                'f_cap_i' => $this->parseFecha($row['f_cap_i'] ?? null),
                'r_comun' => $this->parseDecimal($row['r_comun'] ?? null),
                'c_afp' => $this->parseDecimal($row['c_afp'] ?? null),
                'a_sol' => $this->parseDecimal($row['a_sol'] ?? null),
                's' => $row['s'] ?? null,
                't_afp' => $this->parseDecimal($row['t_afp'] ?? null),
                'bbv' => $this->parseDecimal($row['bbv'] ?? null),
                'futuro' => $row['futuro'] ?? null,
                'gestora' => $row['gestora'] ?? null,
                'cuot_mor' => $this->parseDecimal($row['cuot_mor'] ?? null),
                'ret_jud' => $this->parseDecimal($row['ret_jud'] ?? null),
                'falt_atr' => $this->parseDecimal($row['falt_atr'] ?? null),
                'fom_101' => $this->parseDecimal($row['fom_101'] ?? null),
                'pa_iva' => $this->parseDecimal($row['pa_iva'] ?? null),
                'sal_iva' => $this->parseDecimal($row['sal_iva'] ?? null),
                'tot_deo' => $this->parseDecimal($row['tot_deo'] ?? null),
                'otros' => $this->parseDecimal($row['otros'] ?? null),
                'otros_des' => $this->parseDecimal($row['otros_des'] ?? null),
                'tot_des' => $this->parseDecimal($row['tot_des'] ?? null),
                'tot_par' => $this->parseDecimal($row['tot_par'] ?? null),
                'tot_parcom' => $this->parseDecimal($row['tot_parcom'] ?? null),
                'liq_pag' => $this->parseDecimal($row['liq_pag'] ?? null),
                'cuenta' => $row['cuenta'] ?? null,
                'sol_p' => $this->parseDecimal($row['sol_p'] ?? null),
                'afp_p' => $this->parseDecimal($row['afp_p'] ?? null),
                'fonvi_p' => $this->parseDecimal($row['fonvi_p'] ?? null),
                'cns_p' => $this->parseDecimal($row['cns_p'] ?? null),
                't_labor' => $this->parseDecimal($row['t_labor'] ?? null),
                't_patro' => $this->parseDecimal($row['t_patro'] ?? null),
                't_carga' => $this->parseDecimal($row['t_carga'] ?? null),
                'financia' => $row['financia'] ?? null,
                'separa2' => $row['separa2'] ?? null,
                'cga' => $row['cga'] ?? null,
                'cua' => $row['cua'] ?? null,
                'des' => $row['des'] ?? null,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error importando fila: ' . $e->getMessage(), [
                'ci' => $ci ?? 'desconocido',
                'row' => $row
            ]);
            return null; // Salta esta fila
        }
    }
    
    private function parseDecimal($value)
    {
        if (is_null($value) || $value === '') return null;
        // Reemplazar coma por punto y eliminar espacios
        $value = str_replace(',', '.', trim($value));
        return is_numeric($value) ? (float) $value : null;
    }
    
private function parseFecha($value)
{
    if (empty($value)) return null;
    
    // Si ya es un objeto Carbon o DateTime
    if ($value instanceof \DateTime) {
        return $value->format('Y-m-d');
    }
    
    // Si es un número serial de Excel (ej: 44562 → 2022-01-01)
    if (is_numeric($value)) {
        $unix = ($value - 25569) * 86400; // Excel usa 1900 o 1904, aproximación
        return date('Y-m-d', $unix);
    }
    
    // Si es string, intentar parsear formatos d/m/yyyy o dd/mm/yyyy
    $value = trim($value);
    $parts = explode('/', $value);
    if (count($parts) === 3) {
        $day = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
        $month = str_pad($parts[1], 2, '0', STR_PAD_LEFT);
        $year = $parts[2];
        if (checkdate($month, $day, $year)) {
            return "{$year}-{$month}-{$day}";
        }
    }
    
    // Si nada funciona, intentar con Carbon (formatos adicionales)
    try {
        return \Carbon\Carbon::parse($value)->format('Y-m-d');
    } catch (\Exception $e) {
        \Log::warning("No se pudo parsear fecha: {$value}");
        return null;
    }
}
    
    public function chunkSize(): int
    {
        return 500;
    }


}