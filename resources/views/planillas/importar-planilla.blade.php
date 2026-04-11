@extends('dashboard') <!-- Cambia por tu layout principal -->

@section('contenido')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Importar planilla mensual</div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form action="{{ route('planillas.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="archivo" class="form-label">Archivo Excel (xlsx, xls, csv)</label>
                            <input type="file" class="form-control" id="archivo" name="archivo" required>
                            <div class="form-text">El archivo debe tener las mismas columnas que el ejemplo (CARNET, NOMBRE, TOT_GAN, etc.)</div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="mes" class="form-label">Mes</label>
                                <select class="form-select" id="mes" name="mes" required>
                                    <option value="">Seleccione...</option>
                                    <option value="1">Enero</option>
                                    <option value="2">Febrero</option>
                                    <option value="3">Marzo</option>
                                    <option value="4">Abril</option>
                                    <option value="5">Mayo</option>
                                    <option value="6">Junio</option>
                                    <option value="7">Julio</option>
                                    <option value="8">Agosto</option>
                                    <option value="9">Septiembre</option>
                                    <option value="10">Octubre</option>
                                    <option value="11">Noviembre</option>
                                    <option value="12">Diciembre</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="anio" class="form-label">Año</label>
                                <input type="number" class="form-control" id="anio" name="anio" value="{{ date('Y') }}" min="2000" max="{{ date('Y') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="tipo" class="form-label">Tipo de planilla</label>
                                <select class="form-select" id="tipo" name="tipo" required>
                                    <option value="">Seleccione...</option>
                                    <option value="planta">Planta</option>
                                    <option value="eventual">Eventual</option>
                                </select>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Importar planilla</button>
                        <a href="{{ route('planillas.import.form') }}" class="btn btn-secondary">Cancelar</a>
                    </form>
                </div>
            </div>
            
            <div class="card mt-4">
                <div class="card-header">Notas importantes</div>
                <div class="card-body">
                    <ul>
                        <li>El archivo debe tener una fila de encabezados con nombres de columna exactamente como vienen del DBF (CARNET, NOMBRE, F_INGRESO, TOT_GAN, NETO, etc.).</li>
                        <li>Si un carnet ya existe en la tabla <code>persona</code>, se usará ese registro. Si no, se creará automáticamente a partir del nombre y fechas.</li>
                        <li>No se permiten duplicados: no se puede importar el mismo mes y año para la misma persona dos veces. Si lo intentas, la importación fallará para esa fila (el resto se guarda).</li>
                        <li>Los campos decimales deben usar coma como separador decimal (ej: <code>2392,00</code>). El sistema lo convierte automáticamente.</li>
                        <li>Las fechas deben estar en formato <code>dd/mm/yyyy</code> (como en los ejemplos).</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection