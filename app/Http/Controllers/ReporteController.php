<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Persona;
use App\Models\Profesion;
use App\Models\Memopuesto;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    public function inicio()
    {
        return view('admin.inicio.index');

    }
    public function index()
    {
        $personas = Persona::with(['puestoActual.puesto', 'profesion'])
            ->where('estado', 1)
            ->get();

        return view('reportes.index', compact('personas'));
    }


    public function exportarpdf(){
        $personas = Persona::with(['memopuesto', 'profesion'])->get();
        $pdf = Pdf::loadView('reportes.pdf', compact('personas'));
        return $pdf->download('personal.pdf');
    }
    public function buscar(Request $request)
    {
        $search = $request->input('search');
        $tipo = $request->input('tipo');

        if ($tipo == "ITEM") {
            $personas = Persona::with(['puestoActual.puesto', 'profesion'])
                ->where('estado', 1)
                ->where('tipo', 'item')
                ->where(function ($query) use ($search) {
                    $query->whereRaw("CONCAT(nombre, ' ', apellidoPat, ' ', apellidoMat) LIKE ?", ["%$search%"])
                        ->orWhere('apellidoPat', 'LIKE', "%$search%")
                        ->orWhere('apellidoMat', 'LIKE', "%$search%")
                        ->orWhere('ci', 'LIKE', "%$search%")
                        ->orWhereHas('profesion', function ($q) use ($search) {
                            $q->where('provisionN', 'LIKE', "%$search%");
                        })
                        ->orWhereHas('puestoActual.puesto', function ($q) use ($search) {
                            $q->where('nombre', 'LIKE', "%$search%");
                        });
                })
                ->get();

            return response()->view('reportes.partes.buscar', compact('personas'));
        } elseif ($tipo == "CONTRATO") {
            $personas = Persona::with(['puestoActual.puesto', 'profesion'])
                ->where('estado', 1)
                ->where('tipo', 'contrato')
                ->where(function ($query) use ($search) {
                    $query->whereRaw("CONCAT(nombre, ' ', apellidoPat, ' ', apellidoMat) LIKE ?", ["%$search%"])
                        ->orWhere('ci', 'LIKE', "%$search%")
                        ->orWhereHas('profesion', function ($q) use ($search) {
                            $q->where('provisionN', 'LIKE', "%$search%");
                        })
                        ->orWhereHas('puestoActual.puesto', function ($q) use ($search) {
                            $q->where('nombre', 'LIKE', "%$search%");
                        });
                })
                ->get();

            return response()->view('reportes.partes.buscarcontrato', compact('personas'));
        }
    }
    public function tipo(Request $request)
    {
        $tipo = $request->input('tipo');

        if ($tipo == "ITEM") {
            $personas = Persona::with(['puestoActual.puesto', 'profesion'])
                ->where('estado', 1)
                ->where('tipo', 'item')
                ->get();

            return response()->view('reportes.partes.tipo', compact('personas'));

        } elseif ($tipo == "CONTRATO") {
            $personas = Persona::with(['puestoActual.puesto', 'profesion'])
                ->where('estado', 1)
                ->where('tipo', 'contrato')
                ->get();

            return response()->view('reportes.partes.pers', compact('personas'));
        }
    }

    public function personalPDF(Request $request)
    {


        $personas = Persona::with(['memopuesto', 'profesion'])->get();


        $pdf = new \FPDF('L', 'mm', [216, 356]);
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 10, utf8_decode("Reporte de Datos - personal"), 0, 1, 'C');
        $pdf->Ln(5);

        $pdf->Cell(15, 10, 'ITEM', 1, 0, 'C');
        $pdf->Cell(50, 10, 'Nivel gerárquico', 1, 0, 'C');
        $pdf->Cell(25, 10, 'Apellido 1', 1, 0, 'C');
        $pdf->Cell(25, 10, 'Apellido 2', 1, 0, 'C');
        $pdf->Cell(40, 10, 'Nombre', 1, 0, 'C');
        $pdf->Cell(20, 10, 'CI', 1, 0, 'C');
        $pdf->Cell(15, 10, 'Haber', 1, 0, 'C');
        $pdf->Cell(25, 10, 'Ingreso', 1, 0, 'C');
        $pdf->Cell(25, 10, 'Nacimiento', 1, 0, 'C');
        $pdf->Cell(60, 10, 'Título profesional', 1, 0, 'C');
        $pdf->Cell(25, 10, 'Fecha título', 1, 1, 'C');



        $pdf->SetFont('Arial', '', 10);
        foreach ($personas as $persona) {
            $pdf->Cell(15, 10, $persona->memopuesto->item ?? '', 1, 0, 'L');
            $pdf->Cell(50, 10, utf8_decode($persona->memopuesto->nivelGerarquico ?? ''), 1, 0, 'L');
            $pdf->Cell(25, 10, utf8_decode($persona->apellidoPat), 1, 0, 'L');
            $pdf->Cell(25, 10, utf8_decode($persona->apellidoMat), 1, 0, 'L');
            $pdf->Cell(40, 10, utf8_decode($persona->nombre), 1, 0, 'L');
            $pdf->Cell(20, 10, utf8_decode($persona->ci), 1, 0, 'L');
            $pdf->Cell(15, 10, utf8_decode(number_format($persona->memopuesto->haber ?? 0, 2, ',', '.')), 1, 0, 'L');
            $pdf->Cell(25, 10, utf8_decode(!empty($persona->fechaIngreso) ? \Carbon\Carbon::parse($persona->fechaIngreso)->format('d/m/Y') : ''), 1, 0, 'L');
            $pdf->Cell(25, 10, utf8_decode(!empty($persona->fechaNacimiento) ? \Carbon\Carbon::parse($persona->fechaNacimiento)->format('d/m/Y') : ''), 1, 0, 'L');
            $pdf->Cell(60, 10, utf8_decode($persona->profesion->provisionN ?? ''), 1, 0, 'L');
            $pdf->Cell(25, 10, utf8_decode(!empty($persona->profesion->fechaProvision) ? \Carbon\Carbon::parse($persona->profesion->fechaProvision)->format('d/m/Y') : ''), 1, 0, 'L');
            $pdf->Ln();
        }

        return response($pdf->Output('S'), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="personal.pdf"');

    }
    public function personalXLS()
    {
        $personas = Persona::with(['memopuesto', 'profesion'])->get();

        $filename = "personal.xls";

        $headers = [
            "Content-type" => "application/vnd.ms-excel; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ["ITEM","Nivel gerárquico","Apellido 1","Apellido 2","Nombre","CI","Haber","Ingreso","Nacimiento","Título profesional","Fecha título"];

        $callback = function () use ($personas, $columns) {
            echo "<meta charset='UTF-8'>";
            echo "<table border='1'><tr>";
            foreach ($columns as $column) {
                echo "<th>" . htmlspecialchars($column) . "</th>";
            }
            echo "</tr>";

            foreach ($personas as $persona) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($persona->memopuesto->item ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($persona->memopuesto->nivelGerarquico ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($persona->apellidoPat ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($persona->apellidoMat ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($persona->nombre ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($persona->ci ?? '') . "</td>";
                echo "<td>" . number_format($persona->memopuesto->haber ?? 0, 2, ',', '.') . "</td>";
                echo "<td>" . (!empty($persona->fechaIngreso) ? \Carbon\Carbon::parse($persona->fechaIngreso)->format('d/m/Y') : '') . "</td>";
                echo "<td>" . (!empty($persona->fechaNacimiento) ? \Carbon\Carbon::parse($persona->fechaNacimiento)->format('d/m/Y') : '') . "</td>";
                echo "<td>" . htmlspecialchars($persona->profesion->provisionN ?? '') . "</td>";
                echo "<td>" . (!empty($persona->profesion->fechaProvision) ? \Carbon\Carbon::parse($persona->profesion->fechaProvision)->format('d/m/Y') : '') . "</td>";
                echo "</tr>";
            }

            echo "</table>";
        };

        return response()->stream($callback, 200, $headers);
    }

}
