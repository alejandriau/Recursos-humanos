<?php

use App\Models\Bajasaltas;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\PasivounoController;
use App\Http\Controllers\PasivodosController;
use App\Http\Controllers\ArchivosController;
use App\Http\Controllers\SeleccionController;
use App\Http\Controllers\PuestoController;
use App\Http\Controllers\GerarquiaController;
use App\Http\Controllers\HistorialController;
use App\Http\Controllers\ArchivoController;
use App\Http\Controllers\bajasaltasController;
use App\Http\Controllers\ProfesionController;
use App\Http\Controllers\CertificadoController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\CasController;
use App\Http\Controllers\CenviController;
use App\Http\Controllers\DjbrentaController;
use App\Http\Controllers\AfpController;
use App\Http\Controllers\CajaCordeController;
use App\Http\Controllers\CompromisoController;
use App\Http\Controllers\CroquiController;
use App\Http\Controllers\CedulaIdentidadController;
use App\Http\Controllers\CertNacimientoController;
use App\Http\Controllers\LicenciaConducirController;
use App\Http\Controllers\LicenciaMilitarController;
use App\Http\Controllers\CurriculumController;
use App\Http\Controllers\BachillerController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/reporte', [ReporteController::class, 'index'])->name('reportes.index');
    });


    Route::get('/personas', [PersonaController::class, 'index']);
    Route::get('/personas/show/{id}', [PersonaController::class, 'show'])->name('personas.show');


    Route::get('/reportes/buscar', [ReporteController::class, 'buscar'])->name('reportes.buscar');
    Route::get('/reportes/tipo', [ReporteController::class, 'tipo'])->name('reportes.tipo');
    Route::get('/reportes/pdf', [ReporteController::class, 'exportarpdf'])->name('reportes.pdf');
    Route::get('/reportes/personal', [ReporteController::class, 'personalPDF'])->name('reportes.personal');
    Route::get('/reportes/excel', [ReporteController::class, 'personalXLS'])->name('reportes.excel');
    //pasivos uno
    Route::get('/pasivouno', [PasivounoController::class, 'index'])->name('pasivouno');
    Route::get('/pasivouno/buscar', [PasivounoController::class, 'buscar'])->name('pasivouno.buscar');
    // pasivo dos
    Route::get('/pasivodos', [PasivodosController::class, 'index'])->name('pasivodos');        // Obtener todos

    //Route::get('/pasivodos/{id}', [PasivodosController::class, 'show'])->name('pasivodos');     // Obtener uno por ID
    Route::post('/pasivodos/guardar', [PasivodosController::class, 'store'])->name('pasivodos.guardar');       // Crear nuevo
    Route::put('/pasivodos/{id}', [PasivodosController::class, 'update'])->name('pasivodos');   // Actualizar
    Route::post('/pasivodos/eliminar/{id}', [PasivodosController::class, 'destroy'])->name('pasivodos.eliminar');
    Route::get('/pasivodos/letra', [PasivodosController::class, 'letra'])->name('pasivodos.letra');
    //Route::get('/reserva/{id}', [PasivodosController::class, 'reserva'])->name('reserva');
    //Route::get('/seleccionar/{id}', [PasivodosController::class, 'seleccionar'])->name('seleccionar');
    Route::get('/pasivodos/buscar', [PasivodosController::class, 'buscar'])->name('pasivodos.buscar');
    Route::get('/pasivodos/traer', [PasivodosController::class, 'traer'])->name('pasivodos.traer');
    Route::get('/pasivodos/pdf', [PasivodosController::class, 'reportepasivos'])->name('pasivodos.pdf');
    //ultimo registro
    Route::get('/pasivodos/ultimo', [PasivodosController::class, 'ultimo'])->name('pasivodos.ultimo');

    //archivos
    Route::get('/archivos', [ArchivosController::class, 'index'])->name('archivos');
    Route::post('/archivos/store/{id}', [ArchivosController::class, 'store'])->name('archivos.store');
    Route::get('/archivos/buscar', [ArchivosController::class, 'buscar'])->name('archivos.buscar');
    Route::get('/archivos/formulario', [ArchivosController::class, 'formulario'])->name('archivos.formulario');
    // selecciones
    Route::delete('/seleccion/eliminar', [SeleccionController::class, 'destroy'])->name('seleccion.eliminar');
    //puestos
    Route::post('/puesto/store', [PuestoController::class, 'store'])->name('puesto.store');
    Route::put('/puesto/update/{id}', [PuestoController::class, 'update'])->name('puesto.update');
    Route::get('/puesto', [PuestoController::class, 'index'])->name('puesto');
    Route::get('/puesto/create', [PuestoController::class, 'create'])->name('puesto.create');
    Route::get('/puesto/edit/{id}', [PuestoController::class, 'edit'])->name('edit');
    Route::delete('/puesto/{id}', [PuestoController::class, 'destroy'])->name('puesto.destroy');

    // Gerarquia
    Route::get('/secretarias', [GerarquiaController::class, 'secretarias'])->name('secretarias');
    Route::get('/direcciones', [GerarquiaController::class, 'direcciones'])->name('direcciones');
    Route::get('/unidades', [GerarquiaController::class, 'unidades'])->name('unidades');
    Route::get('/unidadessecre', [GerarquiaController::class, 'unidadesSecretaria'])->name('unidadessecre');
    Route::get('/areas', [GerarquiaController::class, 'areas'])->name('areas');
    Route::get('/areasdireccion', [GerarquiaController::class, 'areasDireccion'])->name('areasdireccion');
    Route::get('/areassecretaria', [GerarquiaController::class, 'areasSecretaria'])->name('areassecretaria');
    Route::get('/puestos', [GerarquiaController::class, 'puestos'])->name('puestos');
    //
    Route::get('/historial', [HistorialController::class, 'index'])->name('historial');
    Route::get('/historial/create/{id}', [HistorialController::class, 'create'])->name('historial.create');
    Route::post('/historial/store', [HistorialController::class, 'store'])->name('historial.store');
    Route::put('/historial/desactivar/{id}', [HistorialController::class, 'desactivar'])->name('historial.desactivar');
    Route::get('/historial/buscar', [HistorialController::class, 'buscarPersonas'])->name('historial.buscar');
    Route::get('/historial/vacio', [HistorialController::class, 'vacios'])->name('historial.vacio');

        Route::post('/historial/store', [HistorialController::class, 'store'])->name('historial.store');
        Route::get('/historial/{historial}', [HistorialController::class, 'show'])->name('historial.show');

        Route::get('/historial/{id}/edit', [HistorialController::class, 'edit'])->name('historial.edit');

        Route::put('/historial/{historial}', [HistorialController::class, 'update'])->name('historial.update');
        Route::get('/{historial}/descargar-memo', [HistorialController::class, 'descargarMemo'])->name('historial.descargar.memo');
        Route::get('/{historial}/descargar-persona', [HistorialController::class, 'descargarPersona'])->name('historial.descargar');

        Route::put('/historial/{historial}', [HistorialController::class, 'update'])->name('historial.update');
        Route::delete('/historial/{historial}', [HistorialController::class, 'destroy'])->name('historial.destroy');

    Route::get('/historial/estadisticas', [HistorialController::class, 'estadisticas'])->name('historial.estadisticas');
    Route::get('/persona/{persona}', [HistorialController::class, 'historialPersona'])->name('historial.persona');
    Route::put('/concluir/{historial}', [HistorialController::class, 'concluir'])->name('historial.concluir');
    //ir a guaradar
    Route::get('/registrar/archivo/{id}', [ArchivoController::class, 'index'])->name('regisrar.archivos');
    //afps
    Route::get('/personas/registrar', [PersonaController::class, 'create'])->name('personas.create');
    Route::post('/personas/store', [PersonaController::class, 'store'])->name('personas.store');
    Route::get('/personas/edit/{id}', [PersonaController::class, 'edit'])->name('personas.edit');
    Route::put('/personas/update/{id}', [PersonaController::class, 'update'])->name('personas.update');
    Route::patch('/personas/destroy/{id}', [PersonaController::class, 'destroy'])->name('personas.destroy');
    Route::get('/persona/foto/{id}', [PersonaController::class, 'mostrarFoto'])->name('persona.foto');

    //altas y bajas
    Route::get('/altasbajas', [PersonaController::class, 'index'])->name('altasbajas');
    Route::post('/altasbajas/store', [BajasaltasController::class, 'store'])->name('altasbajas.store');
    Route::get('/altasbajas/buscar', [PersonaController::class, 'buscar'])->name('altasbajas.buscar');
    //index
    Route::put('/bajasaltas/{id}', [BajasaltasController::class, 'update'])->name('bajasaltas.update');
    Route::get('/altasbajas/index', [BajasaltasController::class, 'index'])->name('bajasaltas.index');


    //afps
    Route::post('/afps/store', [ArchivoController::class, 'store'])->name('afps.store');
    //
    Route::get('/cajacordes/registrar', [PersonaController::class, 'Cajacreate'])->name('cajacordes.create');
    Route::post('/cajacordes/store', [PersonaController::class, 'Cajastore'])->name('cajacordes.store');
    Route::get('/cajacordes/edit/{id}', [PersonaController::class, 'Cajaedit'])->name('cajacordes.edit');
    Route::put('/cajacordes/update', [PersonaController::class, 'Cajaupdate'])->name('cajacordes.update');
    //INICIO
    Route::get('inicio/archivos', [ReporteController::class, 'inicio'])->name('inicio.index');

    //profesiones

    Route::get('profesion/index', [ProfesionController::class, 'index'])->name('profesion.index');
    Route::get('profesion/create/{persona}', [ProfesionController::class, 'create'])->name('profesion.create');
    Route::post('profesion/store/{persona}', [ProfesionController::class, 'store'])->name('profesion.store');
    Route::get('profesion/{profesion}/edit', [ProfesionController::class, 'edit'])->name('profesion.edit');
    Route::put('profesion/update/{profesion}', [ProfesionController::class, 'update'])->name('profesion.update');
    //certificados
        // Lista de certificados
    Route::get('certificados', [CertificadoController::class, 'index'])->name('certificados.index');
    Route::get('certificados/create', [CertificadoController::class, 'create'])->name('certificados.create');
    Route::post('/certificados/store', [CertificadoController::class, 'store'])->name('certificados.store');
    Route::get('/certificados/edit/{certificado}', [CertificadoController::class, 'edit'])->name('certificados.edit');
    Route::put('/certificados/update/{certificado}', [CertificadoController::class, 'update'])->name('certificados.update');
    Route::delete('/certificados/{certificado}', [CertificadoController::class, 'destroy'])->name('certificados.destroy');
    //lisa de cas
    Route::get('/cas', [CasController::class, 'index'])->name('cas.index');
    Route::get('/cas/create', [CasController::class, 'create'])->name('cas.create');
    Route::post('/cas', [CasController::class, 'store'])->name('cas.store');
    Route::get('/cas/{cas}/edit', [CasController::class, 'edit'])->name('cas.edit');
    Route::put('/cas/{cas}', [CasController::class, 'update'])->name('cas.update');
    Route::delete('/cas/{cas}', [CasController::class, 'destroy'])->name('cas.destroy');
    //usuarios


        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users.create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

        // Rutas adicionales para gestión de roles y permisos
        Route::get('/users/{user}/roles/edit', [UserController::class, 'editRoles'])->name('users.roles.edit');
        Route::get('/users/{user}/permissions/edit', [UserController::class, 'editPermissions'])->name('users.permissions.edit');
        Route::get('users/{user}/rol/edit', [UserController::class, 'editRoles'])->name('users.rol.edit');


        Route::put('users/{user}/roles/update', [UserController::class, 'updateRoles'])->name('users.roles.update');
        Route::delete('users/{user}/roles/{role}/remove', [UserController::class, 'removeRole'])->name('users.roles.remove');

        Route::get('/users/{user}/roles', [UserController::class, 'editRoles'])->name('roles.edit');
        Route::put('/users/{user}/roles', [UserController::class, 'updateRoles'])->name('roles.update');
        Route::get('/users/{user}/permissions', [UserController::class, 'editPermissions'])->name('permissions.edit');
        Route::put('/users/{user}/permissions', [UserController::class, 'updatePermissions'])->name('permissions.update');

        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
        Route::get('/roles/{role}/permissions', [RoleController::class, 'editPermissions'])->name('roles.permissions.edit');
        Route::put('/roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('permissions.update');

        // Rutas para gestión de permisos
        Route::get('users/{user}/permissions/edit', [UserController::class, 'editPermissions'])->name('users.permissions.edit');
        Route::put('users/{user}/permissions/update', [UserController::class, 'updatePermissions'])->name('users.permissions.update');
        Route::delete('users/{user}/permissions/{permission}/remove', [UserController::class, 'removePermission'])->name('users.permissions.remove');
            // Lista de CENVI
    Route::get('/cenvis', [CenviController::class, 'index'])->name('cenvis.index');
    // Formulario de creación
    Route::get('/cenvis/create', [CenviController::class, 'create'])->name('cenvis.create');
    // Guardar nuevo CENVI
    Route::post('/cenvis', [CenviController::class, 'store'])->name('cenvis.store');
    // Ver detalles
    Route::get('/cenvis/{cenvi}', [CenviController::class, 'show'])->name('cenvis.show');
    // Formulario de edición
    Route::get('/cenvis/{cenvi}/edit', [CenviController::class, 'edit'])->name('cenvis.edit');
    // Actualizar CENVI
    Route::put('/cenvis/{cenvi}', [CenviController::class, 'update'])->name('cenvis.update');
    // Eliminar CENVI
    Route::delete('/cenvis/{cenvi}', [CenviController::class, 'destroy'])->name('cenvis.destroy');
    // Descargar PDF
    Route::get('/cenvis/{cenvi}/download', [CenviController::class, 'downloadPdf'])->name('cenvis.download');

    //declaracion jurada de bienes y renta
    Route::get('/djbrentas', [DjbrentaController::class, 'index'])->name('djbrentas.index');
    Route::get('/djbrentas/create', [DjbrentaController::class, 'create'])->name('djbrentas.create');
    Route::post('/djbrentas', [DjbrentaController::class, 'store'])->name('djbrentas.store');
    Route::get('/djbrentas/{djbrenta}', [DjbrentaController::class, 'show'])->name('djbrentas.show');
    Route::get('/djbrentas/{djbrenta}/edit', [DjbrentaController::class, 'edit'])->name('djbrentas.edit');
    Route::put('/djbrentas/{djbrenta}', [DjbrentaController::class, 'update'])->name('djbrentas.update');
    Route::delete('/djbrentas/{djbrenta}', [DjbrentaController::class, 'destroy'])->name('djbrentas.destroy');
    Route::get('/djbrentas/{djbrenta}/download', [DjbrentaController::class, 'downloadPdf'])->name('djbrentas.download');

    //afps
    Route::get('/afps', [AfpController::class, 'index'])->name('afps.index');
    Route::get('/afps/create', [AfpController::class, 'create'])->name('afps.create');
    Route::post('/afps', [AfpController::class, 'store'])->name('afps.store');
    Route::get('/afps/{afp}', [AfpController::class, 'show'])->name('afps.show');
    Route::get('/afps/{afp}/edit', [AfpController::class, 'edit'])->name('afps.edit');
    Route::put('/afps/{afp}', [AfpController::class, 'update'])->name('afps.update');
    Route::delete('/afps/{afp}', [AfpController::class, 'destroy'])->name('afps.destroy');
    Route::get('/afps/{afp}/download', [AfpController::class, 'downloadPdf'])->name('afps.download');

    //cajacordes
    Route::get('/cajacordes', [CajaCordeController::class, 'index'])->name('cajacordes.index');
    Route::get('/cajacordes/create', [CajaCordeController::class, 'create'])->name('cajacordes.create');
    Route::post('/cajacordes', [CajaCordeController::class, 'store'])->name('cajacordes.store');
    Route::get('/cajacordes/{cajacorde}', [CajaCordeController::class, 'show'])->name('cajacordes.show');
    Route::get('/cajacordes/{cajacorde}/edit', [CajaCordeController::class, 'edit'])->name('cajacordes.edit');
    Route::put('/cajacordes/{cajacorde}', [CajaCordeController::class, 'update'])->name('cajacordes.update');
    Route::delete('/cajacordes/{cajacorde}', [CajaCordeController::class, 'destroy'])->name('cajacordes.destroy');
    Route::get('/cajacordes/{cajacorde}/download', [CajaCordeController::class, 'downloadPdf'])->name('cajacordes.download');

    //compromisos
    Route::get('/compromisos', [CompromisoController::class, 'index'])->name('compromisos.index');
    Route::get('/compromisos/create', [CompromisoController::class, 'create'])->name('compromisos.create');
    Route::post('/compromisos', [CompromisoController::class, 'store'])->name('compromisos.store');
    Route::get('/compromisos/{compromiso}', [CompromisoController::class, 'show'])->name('compromisos.show');
    Route::get('/compromisos/{compromiso}/edit', [CompromisoController::class, 'edit'])->name('compromisos.edit');
    Route::put('/compromisos/{compromiso}', [CompromisoController::class, 'update'])->name('compromisos.update');
    Route::delete('/compromisos/{compromiso}', [CompromisoController::class, 'destroy'])->name('compromisos.destroy');
    Route::get('/compromisos/{compromiso}/download/{numero}', [CompromisoController::class, 'downloadPdf'])->name('compromisos.download');

    //croquis
    Route::get('/croquis', [CroquiController::class, 'index'])->name('croquis.index');
    Route::get('/croquis/create', [CroquiController::class, 'create'])->name('croquis.create');
    Route::post('/croquis', [CroquiController::class, 'store'])->name('croquis.store');
    Route::get('/croquis/{croqui}', [CroquiController::class, 'show'])->name('croquis.show');
    Route::get('/croquis/{croqui}/edit', [CroquiController::class, 'edit'])->name('croquis.edit');
    Route::put('/croquis/{croqui}', [CroquiController::class, 'update'])->name('croquis.update');
    Route::delete('/croquis/{croqui}', [CroquiController::class, 'destroy'])->name('croquis.destroy');
    Route::get('/mapa/general', [CroquiController::class, 'mapa'])->name('croquis.mapa');
    Route::get('/api/datos', [CroquiController::class, 'getCroquisData'])->name('croquis.api.datos');
    // Agregar esta ruta dentro del grupo de croquis
Route::post('/geocode', [CroquiController::class, 'geocode'])->name('geocode');

    //cedula identidad
    Route::get('/cedulas', [CedulaIdentidadController::class, 'index'])->name('cedulas.index');
    Route::get('/cedulas/create', [CedulaIdentidadController::class, 'create'])->name('cedulas.create');
    Route::post('/cedulas', [CedulaIdentidadController::class, 'store'])->name('cedulas.store');
    Route::get('/cedulas/{cedula}', [CedulaIdentidadController::class, 'show'])->name('cedulas.show');
    Route::get('/cedulas/{cedula}/edit', [CedulaIdentidadController::class, 'edit'])->name('cedulas.edit');
    Route::put('/cedulas/{cedula}', [CedulaIdentidadController::class, 'update'])->name('cedulas.update');
    Route::delete('/cedulas/{cedula}', [CedulaIdentidadController::class, 'destroy'])->name('cedulas.destroy');
    Route::get('/cedulas/{cedula}/download', [CedulaIdentidadController::class, 'downloadPdf'])->name('cedulas.download');
    //certificado nacimiento
    Route::get('/certificados-nacimiento', [CertNacimientoController::class, 'index'])->name('certificados-nacimiento.index');
    Route::get('/certificados-nacimiento/create', [CertNacimientoController::class, 'create'])->name('certificados-nacimiento.create');
    Route::post('/certificados-nacimiento', [CertNacimientoController::class, 'store'])->name('certificados-nacimiento.store');
    Route::get('/certificados-nacimiento/{certificado}', [CertNacimientoController::class, 'show'])->name('certificados-nacimiento.show');
    Route::get('/certificados-nacimiento/{certificado}/edit', [CertNacimientoController::class, 'edit'])->name('certificados-nacimiento.edit');
    Route::put('/certificados-nacimiento/{certificado}', [CertNacimientoController::class, 'update'])->name('certificados-nacimiento.update');
    Route::delete('/certificados-nacimiento/{certificado}', [CertNacimientoController::class, 'destroy'])->name('certificados-nacimiento.destroy');
    Route::get('/certificados-nacimiento/{certificado}/download', [CertNacimientoController::class, 'downloadPdf'])->name('certificados-nacimiento.download');
    // Licencias de conducir
    Route::get('/licencias-conducir', [LicenciaConducirController::class, 'index'])->name('licencias-conducir.index');
    Route::get('/licencias-conducir/create', [LicenciaConducirController::class, 'create'])->name('licencias-conducir.create');
    Route::post('/licencias-conducir', [LicenciaConducirController::class, 'store'])->name('licencias-conducir.store');
    Route::get('/licencias-conducir/{licencia}', [LicenciaConducirController::class, 'show'])->name('licencias-conducir.show');
    Route::get('/licencias-conducir/{licencia}/edit', [LicenciaConducirController::class, 'edit'])->name('licencias-conducir.edit');
    Route::put('/licencias-conducir/{licencia}', [LicenciaConducirController::class, 'update'])->name('licencias-conducir.update');
    Route::delete('/licencias-conducir/{licencia}', [LicenciaConducirController::class, 'destroy'])->name('licencias-conducir.destroy');
    Route::get('/licencias-conducir/{licencia}/download', [LicenciaConducirController::class, 'downloadPdf'])->name('licencias-conducir.download');
    // Licencias militares
    Route::get('/licencias-militares', [LicenciaMilitarController::class, 'index'])->name('licencias-militares.index');
    Route::get('/licencias-militares/create', [LicenciaMilitarController::class, 'create'])->name('licencias-militares.create');
    Route::post('/licencias-militares', [LicenciaMilitarController::class, 'store'])->name('licencias-militares.store');
    Route::get('/licencias-militares/{licencia}', [LicenciaMilitarController::class, 'show'])->name('licencias-militares.show');
    Route::get('/licencias-militares/{licencia}/edit', [LicenciaMilitarController::class, 'edit'])->name('licencias-militares.edit');
    Route::put('/licencias-militares/{licencia}', [LicenciaMilitarController::class, 'update'])->name('licencias-militares.update');
    Route::delete('/licencias-militares/{licencia}', [LicenciaMilitarController::class, 'destroy'])->name('licencias-militares.destroy');
    Route::get('/licencias-militares/{licencia}/download', [LicenciaMilitarController::class, 'downloadPdf'])->name('licencias-militares.download');
    //curriculums
    Route::get('/curriculums', [CurriculumController::class, 'index'])->name('curriculums.index');
    Route::get('/curriculums/create', [CurriculumController::class, 'create'])->name('curriculums.create');
    Route::post('/curriculums', [CurriculumController::class, 'store'])->name('curriculums.store');
    Route::get('/curriculums/{curriculum}', [CurriculumController::class, 'show'])->name('curriculums.show');
    Route::get('/curriculums/{curriculum}/edit', [CurriculumController::class, 'edit'])->name('curriculums.edit');
    Route::put('/curriculums/{curriculum}', [CurriculumController::class, 'update'])->name('curriculums.update');
    Route::delete('/curriculums/{curriculum}', [CurriculumController::class, 'destroy'])->name('curriculums.destroy');
    Route::get('/curriculums/{curriculum}/download', [CurriculumController::class, 'downloadPdf'])->name('curriculums.download');
    // bachilleres
    Route::get('/bachilleres', [BachillerController::class, 'index'])->name('bachilleres.index');
    Route::get('/bachilleres/create', [BachillerController::class, 'create'])->name('bachilleres.create');
    Route::post('/bachilleres', [BachillerController::class, 'store'])->name('bachilleres.store');
    Route::get('/bachilleres/{bachiller}', [BachillerController::class, 'show'])->name('bachilleres.show');
    Route::get('/bachilleres/{bachiller}/edit', [BachillerController::class, 'edit'])->name('bachilleres.edit');
    Route::put('/bachilleres/{bachiller}', [BachillerController::class, 'update'])->name('bachilleres.update');
    Route::delete('/bachilleres/{bachiller}', [BachillerController::class, 'destroy'])->name('bachilleres.destroy');
});

