<?php

namespace App\Http\Controllers;

use App\Models\ConfiguracionSalarioMinimo;
use App\Models\Cas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfiguracionSalarioMinimoController extends Controller
{
    public function index()
    {
        $salarios = ConfiguracionSalarioMinimo::orderBy('gestion', 'desc')->get();
        $salarioVigente = ConfiguracionSalarioMinimo::where('vigente', true)->first();

        return view('admin.cas.configuracion-salario-minimo.index', compact('salarios', 'salarioVigente'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'gestion' => 'required|integer|min:2000|max:2050',
            'monto_salario_minimo' => 'required|numeric|min:1|max:100000',
            'fecha_vigencia' => 'required|date|after_or_equal:today',
            'observaciones' => 'nullable|string|max:500'
        ]);

        // Validar que no exista salario para la misma gestión
        $existeGestion = ConfiguracionSalarioMinimo::where('gestion', $request->gestion)->exists();
        if ($existeGestion) {
            return redirect()->back()
                ->with('error', 'Ya existe un salario mínimo registrado para la gestión ' . $request->gestion)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Desactivar otros salarios vigentes si se marca como vigente
            if ($request->has('vigente') && $request->vigente) {
                ConfiguracionSalarioMinimo::where('vigente', true)->update(['vigente' => false]);
            }

            $salario = ConfiguracionSalarioMinimo::create([
                'gestion' => $request->gestion,
                'monto_salario_minimo' => $request->monto_salario_minimo,
                'fecha_vigencia' => $request->fecha_vigencia,
                'vigente' => $request->has('vigente') ? $request->vigente : false,
                'observaciones' => $request->observaciones
            ]);

            $actualizados = 0;

            // Si se activó como vigente, actualizar todos los CAS
            if ($salario->vigente) {
                $casVigentes = Cas::where('estado_cas', 'vigente')
                    ->where('aplica_bono_antiguedad', true)
                    ->get();

                foreach ($casVigentes as $cas) {
                    // Guardar valores anteriores
                    $valoresAnteriores = [
                        'porcentaje_bono' => $cas->porcentaje_bono,
                        'monto_bono' => $cas->monto_bono,
                        'id_salario_minimo' => $cas->id_salario_minimo
                    ];

                    // Recalcular bono con nuevo salario
                    $nuevoMontoBono = $salario->monto_salario_minimo * ($cas->porcentaje_bono / 100);

                    // Actualizar CAS
                    $cas->update([
                        'monto_bono' => $nuevoMontoBono,
                        'id_salario_minimo' => $salario->id
                    ]);

                    // Registrar en historial de bonos
                    if ($valoresAnteriores['monto_bono'] != $nuevoMontoBono) {
                        \App\Models\CasHistorialBonos::registrarCambio([
                            'id_cas' => $cas->id,
                            'id_usuario' => auth()->id(),
                            'porcentaje_anterior' => $valoresAnteriores['porcentaje_bono'],
                            'porcentaje_nuevo' => $cas->porcentaje_bono,
                            'monto_anterior' => $valoresAnteriores['monto_bono'],
                            'monto_nuevo' => $nuevoMontoBono,
                            'id_salario_minimo_anterior' => $valoresAnteriores['id_salario_minimo'],
                            'id_salario_minimo_nuevo' => $salario->id,
                            'anios_servicio_anterior' => $cas->anios_servicio,
                            'anios_servicio_nuevo' => $cas->anios_servicio,
                            'meses_servicio_anterior' => $cas->meses_servicio,
                            'meses_servicio_nuevo' => $cas->meses_servicio,
                            'dias_servicio_anterior' => $cas->dias_servicio,
                            'dias_servicio_nuevo' => $cas->dias_servicio,
                            'tipo_cambio' => 'salario',
                            'observacion' => "Nuevo salario mínimo nacional: {$salario->monto_salario_minimo} bs"
                        ]);

                        $actualizados++;
                    }
                }
            }

            DB::commit();

            $mensaje = $salario->vigente
                ? "Salario mínimo registrado y {$actualizados} bonos actualizados"
                : "Salario mínimo registrado (no activado como vigente)";

            return redirect()->route('configuracion-salario-minimo.index')
                ->with('success', $mensaje);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al registrar salario mínimo: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function activar($id)
    {
        DB::beginTransaction();

        try {
            $salario = ConfiguracionSalarioMinimo::findOrFail($id);

            // Desactivar todos los salarios
            ConfiguracionSalarioMinimo::where('vigente', true)->update(['vigente' => false]);

            // Activar el seleccionado
            $salario->update(['vigente' => true]);

            // Actualizar todos los CAS vigentes
            $casVigentes = Cas::where('estado_cas', 'vigente')
                ->where('aplica_bono_antiguedad', true)
                ->get();

            $actualizados = 0;

            foreach ($casVigentes as $cas) {
                $valoresAnteriores = [
                    'monto_bono' => $cas->monto_bono,
                    'id_salario_minimo' => $cas->id_salario_minimo
                ];

                $nuevoMontoBono = $salario->monto_salario_minimo * ($cas->porcentaje_bono / 100);

                $cas->update([
                    'monto_bono' => $nuevoMontoBono,
                    'id_salario_minimo' => $salario->id
                ]);

                if ($valoresAnteriores['monto_bono'] != $nuevoMontoBono) {
                    \App\Models\CasHistorialBonos::registrarCambio([
                        'id_cas' => $cas->id,
                        'id_usuario' => auth()->id(),
                        'porcentaje_anterior' => $cas->porcentaje_bono,
                        'porcentaje_nuevo' => $cas->porcentaje_bono,
                        'monto_anterior' => $valoresAnteriores['monto_bono'],
                        'monto_nuevo' => $nuevoMontoBono,
                        'id_salario_minimo_anterior' => $valoresAnteriores['id_salario_minimo'],
                        'id_salario_minimo_nuevo' => $salario->id,
                        'tipo_cambio' => 'salario',
                        'observacion' => "Activación de nuevo salario mínimo: {$salario->monto_salario_minimo} bs"
                    ]);

                    $actualizados++;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Salario activado y {$actualizados} bonos actualizados"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al activar salario: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $salario = ConfiguracionSalarioMinimo::findOrFail($id);

            // No permitir eliminar si es el único registro
            $totalSalarios = ConfiguracionSalarioMinimo::count();
            if ($totalSalarios <= 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el único registro de salario mínimo'
                ], 400);
            }

            // No permitir eliminar si está vigente
            if ($salario->vigente) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar el salario mínimo vigente'
                ], 400);
            }

            $salario->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Salario mínimo eliminado correctamente'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar salario: ' . $e->getMessage()
            ], 500);
        }
    }

    public function obtenerVigente()
    {
        $salarioVigente = ConfiguracionSalarioMinimo::where('vigente', true)->first();

        return response()->json([
            'success' => true,
            'data' => $salarioVigente
        ]);
    }
}
