<?php

use App\Models\Bajasaltas;
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
use App\Http\Controllers\Formulario1Controller;
use App\Http\Controllers\Formulario2Controller;
use App\Http\Controllers\ForconsanguiController;
use App\Http\Controllers\PersonaDashboardController;
use App\Http\Controllers\PlanillaController;
use App\Http\Controllers\PlanillasPdfController;
use App\Http\Controllers\TxtToWordController;
use App\Http\Controllers\AuditLogsController;
use App\Http\Controllers\UnidadOrganizacionalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VacacionAdminController;
use App\Http\Controllers\VacacionEmpleadoController;
use App\Http\Controllers\AsistenciaAdminController;
use App\Http\Controllers\AsistenciaEmpleadoController;
use App\Http\Controllers\Empleado\EmpleadoDashboardController;
use App\Http\Controllers\EscalaBonoController;
use App\Http\Controllers\SalarioMinimoController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\CasHistorialBonosController;
use App\Http\Controllers\ConfiguracionSalarioMinimoController;


use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');


        Route::get('/reporte', [ReporteController::class, 'index'])->name('reportes.index');



    Route::get('/personas/show/{id}', [PersonaController::class, 'show'])->name('personas.show');
        Route::get('/personas/{id}/expediente', [PersonaController::class, 'generarExpediente'])->name('personas.expediente');
    Route::get('/personas/{id}/expediente/ver', [PersonaController::class, 'verExpediente'])->name('personas.expediente.ver');


// Rutas de reportes
Route::prefix('reportes')->group(function () {
    //Route::get('/', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('/buscar', [ReporteController::class, 'buscar'])->name('reportes.buscar');
    Route::get('/tipo', [ReporteController::class, 'tipo'])->name('reportes.tipo');
    Route::post('/filtros-avanzados', [ReporteController::class, 'filtrosAvanzados'])->name('reportes.filtros-avanzados');
    Route::get('/personal-pdf', [ReporteController::class, 'personalPDF'])->name('reportes.personal');
    Route::get('/personal-excel', [ReporteController::class, 'personalXLS'])->name('reportes.excel');
        Route::get('/{id}/historial', [PersonaController::class, 'historial'])->name('personas.historial');
});

    Route::get('/reportes/buscar', [ReporteController::class, 'buscar'])->name('reportes.buscar');
    Route::get('/reportes/tipo', [ReporteController::class, 'tipo'])->name('reportes.tipo');
    Route::get('/reportes/pdf', [ReporteController::class, 'exportarpdf'])->name('reportes.pdf');
    Route::get('/reportes/personal', [ReporteController::class, 'personalPDF'])->name('reportes.personal');
    Route::get('/reportes/excel', [ReporteController::class, 'personalXLS'])->name('reportes.excel');
    //pasivos uno
    Route::get('/pasivouno', [PasivounoController::class, 'index'])->name('pasivouno');
    Route::get('/pasivouno/buscar', [PasivounoController::class, 'buscar'])->name('pasivouno.buscar');

    // pasivo dos ======================================================
    Route::get('/pasivodos', [PasivodosController::class, 'index'])->name('pasivodos.index')->middleware('permission:ver_pasivos_dos');
    Route::get('/pasivodos/ultimo', [PasivodosController::class, 'ultimo'])->name('pasivodos.ultimo')->middleware('permission:ver_ultimo_registro_pasivos_dos');
    Route::get('/pasivodos/letra', [PasivodosController::class, 'letra'])->name('pasivodos.letra')->middleware('permission:filtrar_letra_pasivos_dos');
    Route::get('/pasivodos/buscar', [PasivodosController::class, 'buscar'])->name('pasivodos.buscar')->middleware('permission:buscar_pasivos_dos');
    Route::get('/pasivodos/traer', [PasivodosController::class, 'traer'])->name('pasivodos.traer')->middleware('permission:seleccionar_pasivos_dos');
    Route::get('/pasivodos/pdf', [PasivodosController::class, 'reportepasivos'])->name('pasivodos.pdf')->middleware('permission:generar_pdf_pasivos_dos');

    // Rutas de operaciones CRUD
    Route::post('/pasivodos/guardar', [PasivodosController::class, 'store'])->name('pasivodos.guardar')->middleware('permission:crear_pasivos_dos');
    Route::put('/pasivodos/{id}', [PasivodosController::class, 'update'])->name('pasivodos.actualizar')->middleware('permission:editar_pasivos_dos');
    Route::post('/pasivodos/eliminar/{id}', [PasivodosController::class, 'destroy'])->name('pasivodos.eliminar')->middleware('permission:eliminar_pasivos_dos');
    // ==================================================================
    //archivos
    Route::get('/archivos', [ArchivosController::class, 'index'])->name('archivos');
    Route::post('/archivos/store/{id}', [ArchivosController::class, 'store'])->name('archivos.store');
    Route::get('/archivos/buscar', [ArchivosController::class, 'buscar'])->name('archivos.buscar');
    Route::get('/archivos/formulario', [ArchivosController::class, 'formulario'])->name('archivos.formulario');
    // selecciones
    Route::delete('/seleccion/eliminar', [SeleccionController::class, 'destroy'])->name('seleccion.eliminar');
    Route::delete('/seleccion/eliminar-todo', [SeleccionController::class, 'destroyAll'])->name('seleccion.eliminar.todo');

    //puestos
    Route::post('/puesto/store', [PuestoController::class, 'store'])->name('puesto.store');
    Route::put('/puesto/update/{id}', [PuestoController::class, 'update'])->name('puesto.update');
    Route::get('/puesto', [PuestoController::class, 'index'])->name('puesto');
    Route::get('/puesto/create', [PuestoController::class, 'create'])->name('puesto.create');
    Route::get('/puesto/edit/{id}', [PuestoController::class, 'edit'])->name('edit');
    Route::delete('/puesto/{id}', [PuestoController::class, 'destroy'])->name('puesto.destroy');




// Listar unidades
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/unidades', [UnidadOrganizacionalController::class, 'index'])->name('unidades.index');

// Mostrar formulario de creación
Route::get('/unidades/crear', [UnidadOrganizacionalController::class, 'create'])->name('unidades.create');

// Guardar nueva unidad
Route::post('/unidades', [UnidadOrganizacionalController::class, 'store'])->name('unidades.store');

// Mostrar detalles de unidad
Route::get('/unidades/{unidad}', [UnidadOrganizacionalController::class, 'show'])->name('unidades.show');

// Mostrar formulario de edición
Route::get('/unidades/{unidad}/editar', [UnidadOrganizacionalController::class, 'edit'])->name('unidades.edit');

// Actualizar unidad
Route::put('/unidades/{unidad}', [UnidadOrganizacionalController::class, 'update'])->name('unidades.update');

// Eliminar unidad
Route::delete('/unidades/{unidad}', [UnidadOrganizacionalController::class, 'destroy'])->name('unidades.destroy');

// Organigrama
Route::get('/organigrama', [UnidadOrganizacionalController::class, 'arbolOrganizacional'])->name('unidades.arbol');

// Estructura completa de unidad
Route::get('/unidades/{unidad}/estructura', [UnidadOrganizacionalController::class, 'estructura'])->name('unidades.estructura');

// Desactivar unidad
Route::post('/unidades/{unidad}/desactivar', [UnidadOrganizacionalController::class, 'desactivar'])->name('unidades.desactivar');

// Reactivar unidad
Route::post('/unidades/{unidad}/reactivar', [UnidadOrganizacionalController::class, 'reactivar'])->name('unidades.reactivar');

// =============================================
// RUTAS ADMIN DE PUESTOS
// =============================================

// Listar puestos
Route::get('/admin/puestos', [PuestoController::class, 'index'])->name('puestos.index');

// Mostrar formulario de creación
Route::get('/admin/puestos/crear', [PuestoController::class, 'create'])->name('puestos.create');

// Guardar nuevo puesto
Route::post('/admin/puestos', [PuestoController::class, 'store'])->name('puestos.store');

// Mostrar detalles de puesto
Route::get('/admin/puestos/{puesto}', [PuestoController::class, 'show'])->name('puestos.show');

// Mostrar formulario de edición
Route::get('/admin/puestos/{puesto}/editar', [PuestoController::class, 'edit'])->name('puestos.edit');

// Actualizar puesto
Route::put('/admin/puestos/{puesto}', [PuestoController::class, 'update'])->name('puestos.update');

// Eliminar puesto
Route::delete('/admin/puestos/{puesto}', [PuestoController::class, 'destroy'])->name('puestos.destroy');

// Puestos vacantes
Route::get('/admin/puestos/vacantes', [PuestoController::class, 'vacantes'])->name('puestos.vacantes');

// Jefaturas
Route::get('/admin/puestos/jefaturas', [PuestoController::class, 'jefaturas'])->name('puestos.jefaturas');

// Estadísticas de puestos
Route::get('/admin/puestos/estadisticas', [PuestoController::class, 'estadisticas'])->name('puestos.estadisticas');

// Asignar jefatura
Route::post('/admin/puestos/{puesto}/asignar-jefatura', [PuestoController::class, 'asignarJefatura'])->name('puestos.asignar-jefatura');

// Quitar jefatura
Route::post('/admin/puestos/{puesto}/quitar-jefatura', [PuestoController::class, 'quitarJefatura'])->name('puestos.quitar-jefatura');

// Desactivar puesto
Route::post('/admin/puestos/{puesto}/desactivar', [PuestoController::class, 'desactivar'])->name('puestos.desactivar');

// Reactivar puesto
Route::post('/admin/puestos/{puesto}/reactivar', [PuestoController::class, 'reactivar'])->name('puestos.reactivar');



    // Gerarquia
    Route::get('/secretarias', [GerarquiaController::class, 'secretarias'])->name('secretarias');
    Route::get('/direcciones', [GerarquiaController::class, 'direcciones'])->name('direcciones');
    //Route::get('/unidades', [GerarquiaController::class, 'unidades'])->name('unidades');
    Route::get('/unidadessecre', [GerarquiaController::class, 'unidadesSecretaria'])->name('unidadessecre');
    Route::get('/areas', [GerarquiaController::class, 'areas'])->name('areas');
    Route::get('/areasdireccion', [GerarquiaController::class, 'areasDireccion'])->name('areasdireccion');
    Route::get('/areassecretaria', [GerarquiaController::class, 'areasSecretaria'])->name('areassecretaria');
    //Route::get('/puestos', [GerarquiaController::class, 'puestos'])->name('puestos');
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

    Route::get('/persona/{id}/dashboard', [PersonaDashboardController::class, 'show'])->name('persona.dashboard');
    Route::get('/persona/index', [PersonaDashboardController::class, 'index'])->name('personas.index');



    // Bachiller
    Route::get('/bachiller/create', [BachillerController::class, 'createFromDashboard'])->name('persona.bachiller.create');
    Route::get('/bachiller/{bachiller}/edit', [BachillerController::class, 'editFromDashboard'])->name('persona.bachiller.edit');

    // Formulario1
    Route::get('/formulario1/create', [Formulario1Controller::class, 'createFromDashboard'])->name('persona.formulario1.create');
    Route::get('/formulario1/{formulario1}/edit', [Formulario1Controller::class, 'editFromDashboard'])->name('persona.formulario1.edit');

    // Formulario2
    Route::get('/formulario2/create', [Formulario2Controller::class, 'createFromDashboard'])->name('persona.formulario2.create');
    Route::get('/formulario2/{formulario2}/edit', [Formulario2Controller::class, 'editFromDashboard'])->name('persona.formulario2.edit');

    // Consanguinidad
    Route::get('/consanguinidad/create', [ForconsanguiController::class, 'createFromDashboard'])->name('persona.consanguinidad.create');
    Route::get('/consanguinidad/{consanguinidad}/edit', [ForconsanguiController::class, 'editFromDashboard'])->name('persona.consanguinidad.edit');


    //altas y bajas
    Route::get('/altasbajas', [PersonaController::class, 'index'])->name('altasbajas');
    Route::post('/altasbajas/store', [BajasaltasController::class, 'store'])->name('altasbajas.store');
    Route::get('/altasbajas/buscar', [PersonaController::class, 'buscar'])->name('altasbajas.buscar');


    Route::get('/bajasaltas/{id}', [BajasaltasController::class, 'show'])->name('bajasaltas.show');

    Route::delete('/bajasaltas/{id}', [BajasaltasController::class, 'destroy'])->name('bajasaltas.destroy');


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
// Rutas para CAS
Route::get('/cas', [CasController::class, 'index'])->name('cas.index');
Route::get('/cas/crear', [CasController::class, 'create'])->name('cas.create');
Route::post('/cas', [CasController::class, 'store'])->name('cas.store');
Route::get('/cas/{id}', [CasController::class, 'show'])->name('cas.show');
Route::get('/cas/{id}/editar', [CasController::class, 'edit'])->name('cas.edit');
Route::put('/cas/{id}', [CasController::class, 'update'])->name('cas.update');
Route::delete('/cas/{id}', [CasController::class, 'destroy'])->name('cas.destroy');

// Ruta para cálculo individual de bono
Route::get('/cas/persona/{idPersona}/calcular-bono', [CasController::class, 'calcularBonoPersonaIndividual'])->name('cas.calcular-bono');

// Ruta para crear CAS con persona pre-seleccionada
Route::get('/cas/create/{idPersona}', [CasController::class, 'create'])->name('cas.create.persona');

// Rutas adicionales para CAS
//oute::get('/cas/{idPersona}/calcular-bono', [CasController::class, 'calcularBono'])->name('cas.calcular-bono');
Route::post('/cas/actualizar-alertas', [CasController::class, 'actualizarAlertas'])->name('cas.actualizar-alertas');

// Rutas para escalas de bono
Route::get('/escalas-bono', [EscalaBonoController::class, 'index'])->name('escalas-bono.index');
Route::get('/escalas-bono/{id}', [EscalaBonoController::class, 'show'])->name('escalas-bono.show');

// Rutas para salario mínimo
Route::get('/salario-minimo', [SalarioMinimoController::class, 'index'])->name('salario-minimo.index');
Route::get('/salario-minimo/crear', [SalarioMinimoController::class, 'create'])->name('salario-minimo.create');
Route::post('/salario-minimo', [SalarioMinimoController::class, 'store'])->name('salario-minimo.store');
Route::get('/salario-minimo/vigente', [SalarioMinimoController::class, 'obtenerVigente'])->name('salario-minimo.vigente');
    //usuarios


    // Rutas de Users - Protegidas===========================================================================
    // Solo Admin puede gestionar usuarios completamente
    // Rutas básicas de usuarios
    Route::get('/users', [UserController::class, 'index'])->name('users.index')->middleware('permission:ver_usuarios');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create')->middleware('permission:crear_usuarios');
    Route::post('/users', [UserController::class, 'store'])->name('users.store')->middleware('permission:crear_usuarios');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show')->middleware('permission:ver_usuarios');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('permission:editar_usuarios');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('permission:editar_usuarios');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:eliminar_usuarios');

    // Gestión de Roles de Usuario
    Route::get('/users/{user}/roles/edit', [UserController::class, 'editRoles'])->name('users.roles.edit')->middleware('permission:asignar_roles_usuarios');
    Route::put('/users/{user}/roles', [UserController::class, 'updateRoles'])->name('users.roles.update')->middleware('permission:asignar_roles_usuarios');
    Route::delete('/users/{user}/roles/{role}/remove', [UserController::class, 'removeRole'])->name('users.roles.remove')->middleware('permission:asignar_roles_usuarios');

    // Gestión de Permisos Directos de Usuario
    Route::get('/users/{user}/permissions/edit', [UserController::class, 'editPermissions'])->name('users.permissions.edit')->middleware('permission:asignar_permisos_directos_usuarios');
    Route::put('/users/{user}/permissions', [UserController::class, 'updatePermissions'])->name('users.permissions.update')->middleware('permission:asignar_permisos_directos_usuarios');
    Route::delete('/users/{user}/permissions/{permission}/remove', [UserController::class, 'removePermission'])->name('users.permissions.remove')->middleware('permission:asignar_permisos_directos_usuarios');

// Rutas de Roles - Protegidas
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index')->middleware('permission:ver_roles');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create')->middleware('permission:crear_roles');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store')->middleware('permission:crear_roles');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit')->middleware('permission:editar_roles');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update')->middleware('permission:editar_roles');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('permission:eliminar_roles');

    // Gestión de Permisos de Roles
    Route::get('/roles/{role}/permissions/edit', [RoleController::class, 'editPermissions'])->name('roles.permissions.edit')->middleware('permission:gestionar_permisos_roles');
    Route::put('/roles/{role}/permissions', [RoleController::class, 'updatePermissions'])->name('roles.permissions.update')->middleware('permission:gestionar_permisos_roles');
    //===========================================================================================================
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
    //formularios
    Route::get('formularios1/', [Formulario1Controller::class, 'index'])->name('formularios1.index');
    Route::get('formularios1/create', [Formulario1Controller::class, 'create'])->name('formularios1.create');
    Route::post('formularios1/', [Formulario1Controller::class, 'store'])->name('formularios1.store');
    Route::get('formularios1/{formulario1}', [Formulario1Controller::class, 'show'])->name('formularios1.show');
    Route::get('formularios1/{formulario1}/edit', [Formulario1Controller::class, 'edit'])->name('formularios1.edit');
    Route::put('formularios1/{formulario1}', [Formulario1Controller::class, 'update'])->name('formularios1.update');
    Route::delete('formularios1/{formulario1}', [Formulario1Controller::class, 'destroy'])->name('formularios1.destroy');
    Route::get('formularios1/{formulario1}/download', [Formulario1Controller::class, 'download'])->name('formularios1.download');

        Route::get('/formularios2', [Formulario2Controller::class, 'index'])->name('formularios2.index');
    Route::get('/create', [Formulario2Controller::class, 'create'])->name('formularios2.create');
    Route::post('/', [Formulario2Controller::class, 'store'])->name('formularios2.store');
    Route::get('/{formulario2}', [Formulario2Controller::class, 'show'])->name('formularios2.show');
    Route::get('/{formulario2}/edit', [Formulario2Controller::class, 'edit'])->name('formularios2.edit');
    Route::put('/{formulario2}', [Formulario2Controller::class, 'update'])->name('formularios2.update');
    Route::delete('/{formulario2}', [Formulario2Controller::class, 'destroy'])->name('formularios2.destroy');
    Route::get('/{formulario2}/download', [Formulario2Controller::class, 'download'])->name('formularios2.download');

    //consanguinidad
    Route::get('/consanguinidades/index', [ForconsanguiController::class, 'index'])->name('consanguinidades.index');
    Route::get('/consanguinidades/create', [ForconsanguiController::class, 'create'])->name('consanguinidades.create');
    Route::post('/consanguinidades', [ForconsanguiController::class, 'store'])->name('consanguinidades.store');
    Route::get('/consanguinidades/{consanguinidad}', [ForconsanguiController::class, 'show'])->name('consanguinidades.show');
    Route::get('/consanguinidades/{consanguinidad}/edit', [ForconsanguiController::class, 'edit'])->name('consanguinidades.edit');
    Route::put('/consanguinidades/{consanguinidad}', [ForconsanguiController::class, 'update'])->name('consanguinidades.update');
    Route::delete('/consanguinidades/{consanguinidad}', [ForconsanguiController::class, 'destroy'])->name('consanguinidades.destroy');
    Route::get('/consanguinidades/{consanguinidad}/download', [ForconsanguiController::class, 'download'])->name('consanguinidades.download');
//planillas
    Route::get('/planillas/index', [PlanillaController::class, 'index'])->name('planillas.index');
    Route::get('/planillas/dashboard', [PlanillaController::class, 'dashboard'])->name('planillas.dashboard');
    Route::post('/planillas/buscar', [PlanillaController::class, 'buscar'])->name('planillas.buscar');
    Route::post('/planillas/subir', [PlanillaController::class, 'subirPlanillas'])->name('planillas.subir');
    Route::get('/planillas/reporte/{id}', [PlanillaController::class, 'generarReporte'])->name('planillas.reporte');

    //pdf panillas
    Route::get('/planillas-pdf/index', [PlanillasPdfController::class, 'index'])->name('planillas-pdf.index');
    Route::get('/planillas-pdf/crear', [PlanillasPdfController::class, 'create'])->name('planillas-pdf.create');
    Route::post('/planillas-pdf', [PlanillasPdfController::class, 'store'])->name('planillas-pdf.store');
    Route::get('/planillas-pdf/{planilla}', [PlanillasPdfController::class, 'show'])->name('planillas-pdf.show');

    // Opcional: Si quieres agregar eliminar después
    Route::delete('/planillas-pdf/{planilla}', [PlanillasPdfController::class, 'destroy'])->name('planillas-pdf.destroy');
    Route::get('/planillas-pdf/{planilla}/edit', [PlanillasPdfController::class, 'edit'])->name('planillas-pdf.edit');
    Route::put('/planillas-pdf/{planilla}/update', [PlanillasPdfController::class, 'update'])->name('planillas-pdf.update');
    Route::delete('/planillas-pdf/{planilla}/delete', [PlanillasPdfController::class, 'destroy'])->name('planillas-pdf.destroy');

    Route::get('/planillas-pdf/{planilla}/pdf', [PlanillasPdfController::class, 'viewPdf'])->name('planillas-pdf.view.pdf');
    Route::get('/planillas-pdf/{planilla}/descargar', [PlanillasPdfController::class, 'downloadPdf'])->name('planillas-pdf.download');
    Route::get('/planillas-pdf/{planilla}/ver', [PlanillasPdfController::class, 'show'])->name('planillas-pdf.show');






Route::get('/convert/index', [TxtToWordController::class, 'showForm'])->name('convert.form');
Route::post('/convert/word', [TxtToWordController::class, 'convertTxtToWord'])->name('convert.word');
Route::post('/convert/wordsize7', [TxtToWordController::class, 'convertTxtToWordSize7'])->name('convert.size7');
Route::post('/convert-txt-simple', [TxtToWordController::class, 'convertTxtToWordSimple'])->name('convert.txt-simple');

//auditoria
    Route::get('/audit-logs/index', [AuditLogsController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/{auditLog}', [AuditLogsController::class, 'show'])->name('audit-logs.show');
    Route::get('/api/audit-logs/events', [AuditLogsController::class, 'getEvents'])->name('audit-logs.events');
    Route::get('/api/audit-logs/model-types', [AuditLogsController::class, 'getModelTypes'])->name('audit-logs.model-types');

    Route::get('/audit-logs/dashboard/auditoria', [AuditLogsController::class, 'dashboard'])->name('audit-logs.dashboard');
    Route::get('/audit-logs/user-statistics/auditoria', [AuditLogsController::class, 'userStatistics'])->name('audit-logs.user-statistics');
    Route::get('/audit-logs/user-statistics/{user}/auditoria', [AuditLogsController::class, 'userStatistics'])->name('audit-logs.user-statistics.show');
    Route::get('/audit-logs/suspicious-activities/auditoria', [AuditLogsController::class, 'suspiciousActivities'])->name('audit-logs.suspicious-activities');
    Route::get('/audit-logs/{auditLog}/auditoria', [AuditLogsController::class, 'show'])->name('audit-logs.show');

    //reportes finales ==============================
    Route::prefix('reportes')->name('reportes.')->group(function () {
    Route::get('/dashboard', [ReporteController::class, 'dashboard'])->name('dashboard');
    Route::get('/censo-laboral', [ReporteController::class, 'censoLaboral'])->name('censo-laboral');
    Route::get('/distribucion-unidades', [ReporteController::class, 'distribucionUnidades'])->name('distribucion-unidades');
    Route::get('/rotacion-personal', [ReporteController::class, 'rotacionPersonal'])->name('rotacion-personal');
    Route::get('/estado-documentacion', [ReporteController::class, 'estadoDocumentacion'])->name('estado-documentacion');
    Route::get('/dashboard/pdfs', [ReporteController::class, 'exportarDashboardPDF'])->name('dashboard-pdfs');

    //reportes para pasivo laboral

    // Pasivo Uno
    Route::get('/pasivouno/pdf', [PasivoUnoController::class, 'exportPdf'])->name('pasivouno.pdf');
    Route::get('/pasivouno/pdf/{letra}', [PasivoUnoController::class, 'exportPdfPorLetra'])->name('pasivouno.pdf.letra');
    Route::get('/pasivouno/excel', [PasivoUnoController::class, 'exportExcel'])->name('pasivouno.excel');

    // Pasivo Dos
    Route::get('/pasivodos/pdf', [PasivoDosController::class, 'exportPdf'])->name('pasivodos.pdf');
    Route::get('/pasivodos/pdf/{letra}', [PasivoDosController::class, 'exportPdfPorLetra'])->name('pasivodos.pdf.letra');
    Route::get('/pasivodos/excel', [PasivoDosController::class, 'exportExcel'])->name('pasivodos.excel');

    });




    // routes/web.php



// Rutas para administradores
    Route::get('/admin/vacaciones', [VacacionAdminController::class, 'index'])->name('admin.vacaciones.index');
    Route::get('/admin/vacaciones/{vacacion}', [VacacionAdminController::class, 'show'])->name('admin.vacaciones.show');
    Route::post('/admin/vacaciones/{vacacion}/aprobar', [VacacionAdminController::class, 'aprobar'])->name('admin.vacaciones.aprobar');
    Route::post('/admin/vacaciones/{vacacion}/rechazar', [VacacionAdminController::class, 'rechazar'])->name('admin.vacaciones.rechazar');
    Route::get('/admin/vacaciones-reporte', [VacacionAdminController::class, 'reporte'])->name('admin.vacaciones.reporte');

    // routes/web.php



// Rutas para administradores

    Route::get('/admin/asistencias', [AsistenciaAdminController::class, 'index'])->name('admin.asistencias.index');
    Route::get('/admin/asistencias/create', [AsistenciaAdminController::class, 'create'])->name('admin.asistencias.create');
    Route::post('/admin/asistencias', [AsistenciaAdminController::class, 'store'])->name('admin.asistencias.store');
    Route::get('/admin/asistencias/{asistencia}/edit', [AsistenciaAdminController::class, 'edit'])->name('admin.asistencias.edit');
    Route::put('/admin/asistencias/{asistencia}', [AsistenciaAdminController::class, 'update'])->name('admin.asistencias.update');
    Route::delete('/admin/asistencias/{asistencia}', [AsistenciaAdminController::class, 'destroy'])->name('admin.asistencias.destroy');
    Route::get('/admin/asistencias/reporte-mensual', [AsistenciaAdminController::class, 'reporteMensual'])->name('admin.asistencias.reporte-mensual');
    Route::post('/admin/asistencias/marcar-ausentes', [AsistenciaAdminController::class, 'marcarAusentes'])->name('admin.asistencias.marcar-ausentes');

// API para dispositivos biométricos
    Route::post('/api/biometrico/registro', [BiometricoController::class, 'recibirRegistro']);


    /// controller para historial bono cas


    // Historial por CAS
    Route::get('/admin/cas/historial-bonos', [CasHistorialBonosController::class, 'index'])->name('historial-bonos.index');

    // Historial por persona
    Route::get('/historial-bonos/persona/{idPersona}', [CasHistorialBonosController::class, 'porPersona'])->name('historial-bonos.por-persona');

    // Estadísticas
    Route::get('/historial-bonos/estadisticas', [CasHistorialBonosController::class, 'estadisticas'])->name('historial-bonos.estadisticas');

    // Reportes
    Route::get('/historial-bonos/reporte', [CasHistorialBonosController::class, 'reporte'])->name('historial-bonos.reporte');

    // Detalle de cambio
    Route::get('/historial-bonos/{id}', [CasHistorialBonosController::class, 'show'])->name('historial-bonos.show');

    // Formulario cambio manual
    Route::get('/historial-bonos/crear/cambio-manual', [CasHistorialBonosController::class, 'create'])->name('historial-bonos.create');
    Route::post('/historial-bonos/crear/cambio-manual', [CasHistorialBonosController::class, 'store'])->name('historial-bonos.store');

    // Recálculo forzado
    Route::get('/historial-bonos/forzar-recalculo', [CasHistorialBonosController::class, 'showForzarRecalculo'])->name('historial-bonos.show-forzar-recalculo');
    Route::post('/historial-bonos/forzar-recalculo', [CasHistorialBonosController::class, 'forzarRecalculo'])->name('historial-bonos.forzar-recalculo');

    //configuracion de salarioi minimo

    // Vista principal
    Route::get('/configuracion-salario-minimo/uno', [ConfiguracionSalarioMinimoController::class, 'index'])->name('configuracion-salario-minimo.index');

    // Crear nuevo salario mínimo
    Route::post('/configuracion-salario-minimo/uno', [ConfiguracionSalarioMinimoController::class, 'store'])->name('configuracion-salario-minimo.store');

    // Activar salario mínimo
    Route::post('/configuracion-salario-minimo/{id}/activar', [ConfiguracionSalarioMinimoController::class, 'activar'])->name('configuracion-salario-minimo.activar');

    // Eliminar salario mínimo (solo históricos, no vigentes)
    Route::delete('/configuracion-salario-minimo/{id}', [ConfiguracionSalarioMinimoController::class, 'destroy'])->name('configuracion-salario-minimo.destroy');

    // API - Obtener salario vigente
    Route::get('/configuracion-salario-minimo/vigente', [ConfiguracionSalarioMinimoController::class, 'obtenerVigente'])->name('configuracion-salario-minimo.vigente');


    Route::middleware(['auth', 'role:empleado'])->group(function () {

        // Dashboard
        Route::get('/empleado/index', [EmpleadoDashboardController::class, 'index'])->name('empleado.dashboard');

        // Perfil
        Route::get('/empleado/perfil', [EmpleadoController::class, 'miPerfil'])->name('empleado.perfil');
        Route::get('/empleado/historial/ver', [EmpleadoController::class, 'miHistorial'])->name('empleado.historial');
        Route::get('/empleado/expediente', [EmpleadoController::class, 'miExpediente'])->name('empleado.expediente');
        Route::get('/empleado/ver-expediente', [EmpleadoController::class, 'verMiExpediente'])->name('empleado.ver-expediente');

        Route::get('/empleado/perfil/edit', [PerfilController::class, 'edit'])->name('empleado.perfil.edit');
        Route::put('/empleado/perfil', [PerfilController::class, 'update'])->name('empleado.perfil.update');

        // Rutas para empleados
        Route::get('/empleado/asistencias', [AsistenciaEmpleadoController::class, 'index'])->name('empleado.asistencias.index');
        Route::post('/empleado/asistencias/marcar-entrada', [AsistenciaEmpleadoController::class, 'marcarEntrada'])->name('empleado.asistencias.marcar-entrada');
        Route::post('/empleado/asistencias/marcar-salida', [AsistenciaEmpleadoController::class, 'marcarSalida'])->name('empleado.asistencias.marcar-salida');
        Route::post('/empleado/asistencias/justificar', [AsistenciaEmpleadoController::class, 'justificarAusencia'])->name('empleado.asistencias.justificar');

        // Rutas para empleados
        Route::get('/empleado/vacaciones', [VacacionEmpleadoController::class, 'index'])->name('empleado.vacaciones.index');
        Route::get('/empleado/vacaciones/create', [VacacionEmpleadoController::class, 'create'])->name('empleado.vacaciones.create');
        Route::post('/empleado/vacaciones', [VacacionEmpleadoController::class, 'store'])->name('empleado.vacaciones.store');
        Route::get('/empleado/vacaciones/{vacacion}', [VacacionEmpleadoController::class, 'show'])->name('empleado.vacaciones.show');

        // Historial Laboral
        Route::get('/empleado/historial', [HistorialController::class, 'index'])->name('empleado.historial.index');
        Route::get('/empleado/historial/{historial}', [HistorialController::class, 'show'])->name('empleado.historial.show');

        // Redirección por defecto
        Route::get('/empleado', function () {
            return redirect()->route('dashboard');
        });
    });
});

Route::fallback(function () {
    if (auth()->check()) {
        // Si está autenticado, redirigir al dashboard
        return redirect()->route('dashboard');
    }

    // Si no está autenticado, redirigir al login
    return redirect('/login');
});
