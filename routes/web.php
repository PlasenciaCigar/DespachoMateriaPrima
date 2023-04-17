<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();

Route::group(["middleware" => "auth"], function () {

    Route::get('/', function () {
        return view('index');


    })->name("index");

    Route::get('/SinAcceso', function () {
        return view('Alerts.SinAcceso');

    });

    Route::get('/prnpriview','PrintEmpleadoController@prnpriview')->name("imprimirEmpleado");

//--------------------------------------------mildware de Capa------------------------------------------------------
    //--------------------------------------------Mildware de Capa------------------------------------------------------
    Route::group(['middleware' => 'capa'], function () {

        Route::get('/DesCapa', 'DesCapa@index')->name('DesCapa.index');

        //--------------------------------------------Empleados ROUTES-------------------------------------------------------->
        Route::get("/empleados","EmpleadoController@index")->name("empleados");
        Route::post("/empleado/nuevo","EmpleadoController@storeEmpleado")->name("nuevoEmpleado");
        Route::put("/empleados/editar","EmpleadoController@editarEmpleado")->name("editarempleado");
        Route::delete("/empleado/borrar","EmpleadoController@borrarEmpleado")->name("borrarEmpleado");
        //--------------------------------------------EmpleadosExportar ROUTES-------------------------------------------------------->

        Route::get('/empleados/export', 'EmpleadosExportController@export')->name("exportarEmpleado");
        Route::get('/empleados/exportPDF', 'EmpleadosExportController@exportpdf')->name("exportarEmpleadopdf");
        Route::get('/empleados/exportCVS', 'EmpleadosExportController@exportcvs')->name("exportarEmpleadocvs");


        //--------------------------------------------CapaEntrega ROUTES------------------------------------------------------
        Route::post("/CapaEntrega/nuevo","CapaEntregaController@StoreEntrega")->name("nuevoCapaEntrega");
        Route::get("/CapaEntrega", "CapaEntregaController@index")->name("CapaEntrega");//Muestra el servicio de las empresas
        Route::put("/CapaEntrega/editar","CapaEntregaController@editCapaEntrega")->name("editarCapaEntrega");
        Route::delete("/CapaEntrega/borrar","CapaEntregaController@destroy")->name("borrarCapaEntrega");

        //--------------------------------------------CapaEntregaExportar ROUTES-------------------------------------------------------->
        Route::post('/CapaEntrega/export', 'CapaEntregaController@export')->name("exportarcapaentrega");
        Route::get('/CapaEntrega/export', 'CapaEntregaController@export')->name("exportarcapaentrega");
        Route::post('/CapaEntregamarca/export', 'CapaEntregaController@CalcularPeso')->name("exportarcapaentregamarca");
        Route::get('/CapaEntregamarca/export', 'CapaEntregaController@CalcularPeso')->name("exportarcapaentregamarca");
        Route::post('/CapaEntrega/exportPDF', 'CapaEntregaController@exportpdf')->name("exportarcapaentregapdf");
        Route::get('/CapaEntrega/exportPDF', 'CapaEntregaController@exportpdf')->name("exportarcapaentregapdf");
        Route::post('/CapaEntrega/exportCVS', 'CapaEntregaController@exportcvs')->name("exportarcapaentregaacvs");
        Route::get('/CapaEntrega/exportCVS', 'CapaEntregaController@exportcvs')->name("exportarcapaentregacvs");
        //--------------------------------------------CapaEntrega ROUTES------------------------------------------------------>
        Route::put("/CapaEntrega/25","CapaEntregaController@Suma25")->name("Suma25CapaEntrega");
        Route::put("/CapaEntrega/50","CapaEntregaController@Suma50")->name("Suma50CapaEntrega");
        Route::put("/CapaEntrega/suma","CapaEntregaController@Sumas")->name("SumasCapaEntrega");

        //--------------------------------------------Recibir Capa ROUTES-------------------------------------------------------->
        Route::get("/RecepcionCapa","RecibirCapaController@index")->name("RecepcionCapa");
        Route::post("/RecepcionCapa/nuevo","RecibirCapaController@storeRecepcionCapa")->name("nuevaRecepcionCapa");
        Route::put("/RecepcionCapa/editar","RecibirCapaController@editarRecepcionCapa")->name("editarRecepcionCapa");
        Route::delete("/RecepcionCapa/borrar","RecibirCapaController@borrarRecepcionCapa")->name("borrarRecepcionCapa");
        //--------------------------------------------RECIBIRCAPAExportar ROUTES-------------------------------------------------------->
        Route::post('/RecepcionCapa/export', 'RecibirCapaController@export')->name("exportarRecibircapa");
        Route::get('/RecepcionCapa/export', 'RecibirCapaController@export')->name("exportarRecibircapa");
        Route::post('/RecepcionCapa/exportPDF', 'RecibirCapaController@exportpdf')->name("exportarRecibircapapdf");
        Route::get('/RecepcionCapa/exportPDF', 'RecibirCapaController@exportpdf')->name("exportarRecibircapapdf");
        Route::post('/RecepcionCapa/exportCVS', 'RecibirCapaController@exportcvs')->name("exportarRecibircapacvs");
        Route::get('/RecepcionCapa/exportCVS', 'RecibirCapaController@exportcvs')->name("exportarRecibircapacvs");
        //--------------------------------------------Sumar Capa ROUTES-------------------------------------------------------->
        Route::put('/RecepcionCapa/200', 'RecibirCapaController@Suma200')->name("sumar200Recibircapa");
        Route::put('/RecepcionCapa/50', 'RecibirCapaController@Suma50')->name("sumar50Recibircapa");
        Route::put('/RecepcionCapa/sumar', 'RecibirCapaController@Sumas')->name("sumarRecibircapa");
        //--------------------------------------------PESOS ROUTES-------------------------------------------------------->
        Route::get("/peso","PesoController@index")->name("peso");
        Route::post("/peso/nuevo","PesoController@StorePeso")->name("nuevopeso");
        Route::put("/peso/editar","PesoController@editarPeso")->name("editarpeso");
        Route::delete("/peso/borrar","PesoController@destroy")->name("borrarpeso");


        //--------------------------------------------Marca ROUTES-------------------------------------------------------->
        Route::get("/marcas","MarcaController@index")->name("marcas");
        Route::post("/marcas/nuevo","MarcaController@storeMarca")->name("nuevaMarca");
        Route::put("/marcas/editar","MarcaController@editarMarca")->name("editarMarca");
        Route::delete("/marcas/borrar","MarcaController@borrarMarca")->name("borrarMarca");
        //--------------------------------------------Semilla ROUTES-------------------------------------------------------->
        Route::get("/semillas","SemillaController@index")->name("semillas");
        Route::post("/semillas/nuevo","SemillaController@storeSemilla")->name("nuevasemillas");
        Route::put("/semillas/editar","SemillaController@editarSemilla")->name("editarsemillas");
        Route::delete("/semillas/borrar","SemillaController@borrarSemilla")->name("borrarsemillas");

        //--------------------------------------------vitola ROUTES-------------------------------------------------------->
        Route::get("/vitola","VitolaController@index1")->name("vitola");
        Route::post("/vitola/nuevo","VitolaController@storeVitola1")->name("vitolanueva");
        Route::put("/vitola/editar","VitolaController@editarVitola1")->name("vitolaeditar");
        Route::delete("/vitola/borrar","VitolaController@borrarVitola1")->name("vitolaBorrar");

        //--------------------------------------------vitola ROUTES-------------------------------------------------------->
        Route::get("/ExistenciaDiario","ExistenciaDiarioController@index")->name("ExistenciaDiario");
        Route::post("/ExistenciaDiario/nuevo","ExistenciaDiarioController@store")->name("ExistenciaDiarionuevo");
        Route::put("/ExistenciaDiario/editar","ExistenciaDiarioController@edit")->name("ExistenciaDiarioeditar");
        Route::delete("/ExistenciaDiario/borrar","ExistenciaDiarioController@destroy")->name("ExistenciaDiarioborrar");
        Route::delete("/ExistenciaDiarioa/borrarall","ExistenciaDiarioController@destroyall")->name("ExistenciaDiarioborrarall");
        Route::delete("/ExistenciaDiarioa/limpiar","ExistenciaDiarioController@limpiar")->name("ExistenciaDiariolimpiar");

        //--------------------------------------------Existencia Diario xportar ROUTES-------------------------------------------------------->
        Route::post('/ExistenciaDiario/export', 'ExistenciaDiarioController@export')->name("exportarExistenciaDiario");
        Route::get('/ExistenciaDiario/export', 'ExistenciaDiarioController@export')->name("exportarExistenciaDiario");
        Route::post('/ExistenciaDiario/exportPDF', 'ExistenciaDiarioController@exportpdf')->name("exportarExistenciaDiariopdf");
        Route::get('/ExistenciaDiario/exportPDF', 'ExistenciaDiarioController@exportpdf')->name("exportarExistenciaDiariopdf");
        Route::post('/ExistenciaDiario/exportCVS', 'ExistenciaDiarioController@exportcvs')->name("exportarExistenciaDiariocvs");
        Route::get('/ExistenciaDiario/exportCVS', 'ExistenciaDiarioController@exportcvs')->name("exportarExistenciaDiariocvs");

//--------------------------------------------Inventario Inicial Capa ROUTES-------------------------------------------------------->
        Route::get('/InventarioInicialCapa', 'CInvInicialController@index')->name("InventarioInicialCapa");
        Route::delete("/InventarioInicialCapa/borrar","CInvInicialController@destroy")->name("InventarioInicialcapaborrar");

        //--------------------------------------------Resumen  Capa ROUTES-------------------------------------------------------->
        Route::get('/ResumenDiario', 'ResumenCapaController@index')->name("ResumenDiario");
        //--------------------------------------------Resumen de capa exportar ROUTES-------------------------------------------------------->
        Route::post('/ResumenDiario/export', 'ResumenCapaController@export')->name("exportarResumenDiario");
        Route::get('/ResumenDiario/export', 'ResumenCapaController@export')->name("exportarResumenDiario");
        Route::post('/ResumenDiario/exportPDF', 'ResumenCapaController@exportpdf')->name("exportarResumenDiariopdf");
        Route::get('/ResumenDiario/exportPDF', 'ResumenCapaController@exportpdf')->name("exportarResumenDiariopdf");
        Route::post('/ResumenDiario/exportCVS', 'ResumenCapaController@exportcvs')->name("exportarResumenDiariocvs");
        Route::get('/ResumenDiario/exportCVS', 'ResumenCapaController@exportcvs')->name("exportarResumenDiariocvs");

        //--------------------------------------------Resumen  Capa ROUTES-------------------------------------------------------->
        Route::get('/ResumenMensual', 'EntradaCapaMensualController@index')->name("ResumenMensual");

    });
//--------------------------------------------mildaware Despacho de tripa y banda------------------------------------------------------
//--------------------------------------------mildaware Despacho de tripa y bandaS------------------------------------------------------

    Route::group(['middleware' => 'banda'], function () {
        Route::get('/DesBanda', 'DesBanda@index')->name('DesBanda.index');


        //--------------------------------------------Empleados BANDA ROUTES-------------------------------------------------------->
        Route::get("/empleadosBanda", "EmpleadoBandaController@index")->name("empleadosBanda");
        Route::post("/empleadoBanda/nuevo", "EmpleadoBandaController@storeEmpleado")->name("nuevoEmpleadoBanda");
        Route::put("/empleadoBanda/editar", "EmpleadoBandaController@editarEmpleado")->name("editarempleadoBanda");
        Route::delete("/empleadoBanda/borrar", "EmpleadoBandaController@borrarEmpleado")->name("borrarEmpleadoBanda");

        //--------------------------------------------Marca BANDA ROUTES-------------------------------------------------------->
        Route::get("/marcasBanda", "MarcaBandaController@index")->name("marcasBanda");
        Route::post("/marcasBanda/nuevo", "MarcaBandaController@storeMarca")->name("nuevaMarcaBanda");
        Route::put("/marcasBanda/editar", "MarcaBandaController@editarMarca")->name("editarMarcaBanda");
        Route::delete("/marcasBanda/borrar", "MarcaBandaController@borrarMarca")->name("borrarMarcaBanda");

        //--------------------------------------------Semilla  BANDA ROUTES-------------------------------------------------------->
        Route::get("/semillasBanda", "SemillaController@index1")->name("semillasBanda");
        Route::post("/semillasBanda/nuevo", "SemillaController@storeSemilla1")->name("nuevasemillasBanda");
        Route::put("/semillasBanda/editar", "SemillaController@editarSemilla1")->name("editarsemillasBanda");
        Route::delete("/semillasBanda/borrar", "SemillaController@borrarSemilla1")->name("borrarsemillasBanda");

        //--------------------------------------------vitola  BANDA ROUTES-------------------------------------------------------->
        Route::get("/vitolaBanda", "VitolaController@index")->name("vitolaBanda");
        Route::post("/vitolaBanda/nuevo", "VitolaController@storeVitola")->name("vitolanuevaBanda");
        Route::put("/vitolaBanda/editar", "VitolaController@editarVitola")->name("vitolaeditarBanda");
        Route::delete("/vitolaBanda/borrar", "VitolaController@borrarVitola")->name("vitolaBorrarBanda");
        //--------------------------------------------Bulto  Tripa ROUTES-------------------------------------------------------->
        Route::get("/BultoSalida", "BultosSalidaController@index")->name("BultoSalida");
        Route::post("/BultoSalida/nuevo", "BultosSalidaController@store")->name("BultoSalidanueva");
        Route::put("/BultoSalida/editar", "BultosSalidaController@edit")->name("BultoSalidaeditar");
        Route::delete("/BultoSalida/borrar", "BultosSalidaController@destroy")->name("BultoSalidaborrar");
        //--------------------------------------------Sumar Bultos ROUTES-------------------------------------------------------->
        Route::put('/BultosSalida/1', 'BultosSalidaController@Suma')->name("sumarBulto");
        Route::put('/BultosSalida/2', 'BultosSalidaController@Resta')->name("restarBulto");

        //--------------------------------------------CapaEntregaExportar ROUTES-------------------------------------------------------->
        Route::post('/BultoEntrega/export', 'BultosSalidaController@export')->name("exportarbultoentrega");
        Route::get('/BultoEntrega/export', 'BultosSalidaController@export')->name("exportarbultoentrega");
        Route::post('/BultoEntrega/exportPDF', 'BultosSalidaController@exportpdf')->name("exportarbultoentregapdf");
        Route::get('/BultoEntrega/exportPDF', 'BultosSalidaController@exportpdf')->name("exportarbultoentregapdf");
        Route::post('/BultoEntrega/exportCVS', 'BultosSalidaController@exportcvs')->name("exportarbultoentregaacvs");
        Route::get('/BultoEntrega/exportCVS', 'BultosSalidaController@exportcvs')->name("exportarbultoentregacvs");
        Route::put('/EntradaBandas/50', 'BultosSalidaController@Suma100')->name("sumar100BandaBulto");
        Route::put('/EntradaBandas/500', 'BultosSalidaController@Resta100')->name("restar100BandaBulto");

        //Exportar materia prima en salida despacho a Salon.

        Route::post('/BultoEntrega/generar', 'BultosSalidaController@GenerarSalidaMP')->name("generarbultosmp");
        Route::get('/BultoEntrega/generar', 'BultosSalidaController@GenerarSalidaMP')->name("generarbultosmp");

        Route::post('/BultoEntrega/mp', 'BultosSalidaController@ExcelBultosMP')->name("exportarbultoentregamp");
        Route::get('/BultoEntrega/mp', 'BultosSalidaController@ExcelBultosMP')->name("exportarbultoentregamp");

        //--------------------------------------------Bulto  Tripa  Devueltos ROUTES-------------------------------------------------------->
        Route::get("/BultoDevuelto", "BultosDevueltoController@index")->name("BultoDevuelto");
        Route::post("/BultoDevuelto/nuevo", "BultosDevueltoController@store")->name("BultoDevueltonueva");
        Route::put("/BultoDevuelto/editar", "BultosDevueltoController@edit")->name("BultoDevueltoeditar");
        Route::delete("/BultoDevuelto/borrar", "BultosDevueltoController@destroy")->name("BultoDevueltoborrar");
        //--------------------------------------------Bulto Devuelto ROUTES-------------------------------------------------------->
        Route::post('/BultoDevuelto/export', 'BultosDevueltoController@export')->name("exportarbultodevuelto");
        Route::get('/BultoDevuelto/export', 'BultosDevueltoController@export')->name("exportarbultodevuelto");
        Route::post('/BultoDevuelto/exportPDF', 'BultosDevueltoController@exportpdf')->name("exportarbultodevueltopdf");
        Route::get('/BultoDevuelto/exportPDF', 'BultosDevueltoController@exportpdf')->name("exportarbultodevueltopdf");
        Route::post('/BultoDevuelto/exportCVS', 'BultosDevueltoController@exportcvs')->name("exportarbultodevueltocvs");
        Route::get('/BultoDevuelto/exportCVS', 'BultosDevueltoController@exportcvs')->name("exportarbultodevueltocvs");
//--------------------------------------------Consumo De Banda ROUTES-------------------------------------------------------->
        Route::get('/ConsumoBanda', 'ConsumoBandaController@index')->name("ConsumoBanda");
        Route::post("/ConsumoBanda/nuevo", "ConsumoBandaController@store")->name("ConsumoBandanueva");
        Route::put("/ConsumoBanda/editar", "ConsumoBandaController@edit")->name("ConsumoBandaeditar");
        Route::delete("/ConsumoBanda/borrar", "ConsumoBandaController@destroy")->name("ConsumoBandaborrar");
        Route::post("/ConsumoBanda/recalcular", "ConsumoBandaController@calcular")->name("recalcularconsumobanda");
        //--------------------------------------------Consumo De Bnada Exportar ROUTES-------------------------------------------------------->
        Route::post('/ConsumoBanda/export', 'ConsumoBandaController@export')->name("exportarconsumobanda");
        Route::get('/ConsumoBanda/export', 'ConsumoBandaController@export')->name("exportarconsumobanda");
        Route::post('/ConsumoBanda/exportPDF', 'ConsumoBandaController@exportpdf')->name("exportarconsumobandapdf");
        Route::get('/ConsumoBanda/exportPDF', 'ConsumoBandaController@exportpdf')->name("exportarconsumobandapdf");
        Route::post('/ConsumoBanda/exportCVS', 'ConsumoBandaController@exportcvs')->name("exportarconsumobandacvs");
        Route::get('/ConsumoBanda/exportCVS', 'ConsumoBandaController@exportcvs')->name("exportarconsumobandacvs");
        Route::post('/ConsumoBanda/exportmarcabanda', 'ConsumoBandaController@exportbandas')->name("exportbandas");
        Route::get('/ConsumoBanda/exportmarcabanda', 'ConsumoBandaController@exportbandas')->name("exportbandas");
        //--------------------------------------------Inventario Diario  ROUTES-------------------------------------------------------->
        Route::get('/InventarioDiario', 'ReBulDiarioController@index')->name("InventarioDiario");
        Route::post("/InventarioDiario/nuevo", "ReBulDiarioController@store")->name("InventarioDiarionuevo");
        Route::put("/InventarioDiario/editar", "ReBulDiarioController@edit")->name("InventarioDiarioeditar");
        Route::delete("/InventarioDiario/borrar", "ReBulDiarioController@destroy")->name("InventarioDiarioborrar");
        Route::delete("/InventarioDiario/borrarall", "ReBulDiarioController@destroyall")->name("InventarioDiarioborrarall");
        Route::delete("/InventarioDiario/limpiarall", "ReBulDiarioController@limpiarnulos")->name("InventarioDiariolimpiarall");
        Route::post("/InventarioDiario/recalcularinventario", "ReBulDiarioController@calcular")->name("calcularinventario");
        Route::get("/InventarioDiario/diferencias/rmp", "ReBulDiarioController@diferencias")->name("diferenciaspesosrmp");

        //--------------------------------------------Registro Diario Bultos Exportar ROUTES-------------------------------------------------------->
        Route::post('/InventarioDiario/export', 'ReBulDiarioController@export')->name("exportarbultoentregadiario");
        Route::get('/InventarioDiario/export', 'ReBulDiarioController@export')->name("exportarbultoentregadiario");
        Route::post('/InventarioDiario/exportPDF', 'ReBulDiarioController@exportpdf')->name("exportarbultoentregapdfdiario");
        Route::get('/InventarioDiario/exportPDF', 'ReBulDiarioController@exportpdf')->name("exportarbultoentregapdfdiario");
        Route::post('/InventarioDiario/exportCVS', 'ReBulDiarioController@exportcvs')->name("exportarbultoentregaacvsdiario");
        Route::get('/InventarioDiario/exportCVS', 'ReBulDiarioController@exportcvs')->name("exportarbultoentregacvsdiario");

//--------------------------------------------Inventario Inicial Bultos ROUTES-------------------------------------------------------->
        Route::get('/InventarioInicial', 'BInvInicialController@index')->name("InventarioInicial");
        Route::delete("/InventarioInicial/borrar","BInvInicialController@destroy")->name("InventarioInicialborrar");
        //--------------------------------------------Sumar Banda ROUTES-------------------------------------------------------->
        Route::put('/EntregaBanda/50', 'ConsumoBandaController@Suma100')->name("sumar100EntregaBanda");
        Route::put('/EntregaBanda/sumar', 'ConsumoBandaController@Sumas')->name("sumarEntregaBanda");

        //--------------------------------------------Entrada De Banda  ROUTES-------------------------------------------------------->
        Route::get('/EntradaBanda', 'EntradaBandaController@index')->name("EntradaBanda");
        Route::post("/EntradaBanda/nuevo", "EntradaBandaController@store")->name("EntradaBandanuevo");
        Route::put("/EntradaBanda/editar", "EntradaBandaController@edit")->name("EntradaBandaeditar");
        Route::delete("/EntradaBanda/borrar", "EntradaBandaController@destroy")->name("EntradaBandaborrar");
        //--------------------------------------------Sumar Entrada Banda ROUTES-------------------------------------------------------->
        Route::put('/EntradaBanda/50', 'EntradaBandaController@Suma100')->name("sumar100EntradaBanda");
        Route::put('/EntradaBanda/sumar', 'EntradaBandaController@Sumas')->name("sumarEntredaBanda");

        //--------------------------------------------Entrada Banda Exportar ROUTES-------------------------------------------------------->
        Route::post('/EntradaBanda/export', 'EntradaBandaController@export')->name("exportarEntradaBanda");
        Route::get('/EntradaBanda/export', 'EntradaBandaController@export')->name("exportarEntradaBanda");
        Route::post('/EntradaBanda/exportPDF', 'EntradaBandaController@exportpdf')->name("exportarEntradaBandapdf");
        Route::get('/EntradaBanda/exportPDF', 'EntradaBandaController@exportpdf')->name("exportarEntradaBandapdf");
        Route::post('/EntradaBanda/exportCVS', 'EntradaBandaController@exportcvs')->name("exportarEntradaBandacvs");
        Route::get('/EntradaBanda/exportCVS', 'EntradaBandaController@exportcvs')->name("exportarEntradaBandacvs");



                //--------------------------------------------Entrada De Bultos  ROUTES-------------------------------------------------------->
                Route::get('/EntradaBultos', 'EntradaBultosController@index')->name("EntradaBultos");
                Route::post("/EntradaBultos/nuevo", "EntradaBultosController@store")->name("EntradaBultosnuevo");
                Route::put("/EntradaBultos/editar", "EntradaBultosController@edit")->name("EntradaBultoseditar");
                Route::delete("/EntradaBultos/borrar", "EntradaBultosController@destroy")->name("EntradaBultosborrar");
                //--------------------------------------------Sumar Entrada Bultos ROUTES-------------------------------------------------------->
                Route::put('/EntradaBultos/50', 'EntradaBultosController@Suma100')->name("sumar100EntradaBultos");
                Route::put('/EntradaBultos/sumar', 'EntradaBultosController@Sumas')->name("sumarEntredaBultos");

                //--------------------------------------------Entrada Bultos Exportar ROUTES-------------------------------------------------------->
                Route::post('/EntradaBultos/export', 'EntradaBultosController@export')->name("exportarEntradaBultos");
                Route::get('/EntradaBultos/export', 'EntradaBultosController@export')->name("exportarEntradaBultos");
                Route::post('/EntradaBultos/exportPDF', 'EntradaBultosController@exportpdf')->name("exportarEntradaBultospdf");
                Route::get('/EntradaBultos/exportPDF', 'EntradaBultosController@exportpdf')->name("exportarEntradaBultospdf");
                Route::post('/EntradaBultos/exportCVS', 'EntradaBultosController@exportcvs')->name("exportarEntradaBultoscvs");
                Route::get('/EntradaBultos/exportCVS', 'EntradaBultosController@exportcvs')->name("exportarEntradaBultoscvs");
                Route::get('/EntradaBultos/diferencias', 'EntradaBultosController@diferencias')->name("EntradaBDiff");



        //--------------------------------------------Inventario De Banda  ROUTES-------------------------------------------------------->
        Route::get('/InventarioBanda', 'InventarioBandaController@index')->name("InventarioBanda");
        Route::post("/InventarioBanda/nuevo", "InventarioBandaController@store")->name("InventarioBandanuevo");
        Route::put("/InventarioBanda/editar", "InventarioBandaController@edit")->name("InventarioBandaeditar");
        Route::delete("/InventarioBanda/borrar", "InventarioBandaController@destroy")->name("InventarioBandaborrar");
        Route::delete("/InventarioBanda/borrarall", "InventarioBandaController@destroyall")->name("InventarioBandaborrarall");
        //--------------------------------------------Registro Diario Bultos Exportar ROUTES-------------------------------------------------------->
        Route::post('/InventarioBanda/export', 'InventarioBandaController@export')->name("exportarInventarioBanda");
        Route::get('/InventarioBanda/export', 'InventarioBandaController@export')->name("exportarInventarioBanda");
        Route::post('/InventarioBanda/exportPDF', 'InventarioBandaController@exportpdf')->name("exportarInventarioBandapdf");
        Route::get('/InventarioBanda/exportPDF', 'InventarioBandaController@exportpdf')->name("exportarInventarioBandapdf");
        Route::post('/InventarioBanda/exportCVS', 'InventarioBandaController@exportcvs')->name("exportarInventarioBandacvs");
        Route::get('/InventarioBanda/exportCVS', 'InventarioBandaController@exportcvs')->name("exportarInventarioBandacvs");
        //--------------------------------------------Procedencia ROUTES-------------------------------------------------------->
        Route::get("/Procedencia","ProcedenciaController@index")->name("Procedencia");
        Route::post("/Procedencia/nuevo","ProcedenciaController@storeMarca")->name("nuevaProcedencia");
        Route::put("/Procedencia/editar","ProcedenciaController@editarMarca")->name("editarProcedencia");
        Route::delete("/Procedencia/borrar","ProcedenciaController@borrarMarca")->name("borrarProcedencia");
        //--------------------------------------------variedad ROUTES-------------------------------------------------------->
        Route::get("/variedad","VariedadController@index")->name("variedad");
        Route::post("/variedad/nuevo","VariedadController@storeMarca")->name("nuevavariedad");
        Route::put("/variedad/editar","VariedadController@editarMarca")->name("editarvariedad");
        Route::delete("/variedad/borrar","VariedadController@borrarMarca")->name("borrarvariedad");
    });

    //INVENTARIO MATERIA PRIMA.
    Route::get("/rmp/ligas", 'MateriaPrimaController@index')->name('ligas');
    Route::post("/rmp/ligasstore", 'MateriaPrimaController@store')->name('ligastore');
    Route::put("/rmp/ligasupdate", 'MateriaPrimaController@update')->name('ligasupdate');
    Route::delete("/rmp/ligasdelete", 'MateriaPrimaController@destroy')->name('ligasdelete');
    Route::post("/rmp/exportarligas", 'MateriaPrimaController@export')->name('ligasexport');
    Route::get("/rmp/importarmateriaprima", 'MateriaPrimaController@importMP')->name('importmp');


    //ENTRADA DE MATERIA PRIMA RMP.
    Route::get("/rmp/entrada", 'EntradaMateriaPrimaController@index')->name('entradarmp');
    Route::post("/rmp/entradastore", 'EntradaMateriaPrimaController@store')->name('storeentradarmp');
    Route::put("/rmp/entradaupdate", 'EntradaMateriaPrimaController@update')->name('updateentradarmp');
    Route::delete("/rmp/entradadelete", 'EntradaMateriaPrimaController@destroy')->name('deleteentradarmp');
    Route::post("/rmp/excelligas", 'EntradaMateriaPrimaController@export')->name('exportentradarmp');
    Route::post("/rmp/procesarentrada", 'EntradaMateriaPrimaController@procesar')->name('procesarentrada');
    Route::post("/rmp/desaplicarentrada", 'EntradaMateriaPrimaController@desaplicar')->name('desaplicarentrada');

    //SALIDA DE MATERIA PRIMA RMP.
    Route::get("/rmp/salida", 'SalidaMateriaPrimaController@index')->name('salidarmp');
    Route::post("/rmp/salidastore", 'SalidaMateriaPrimaController@store')->name('storesalidarmp');
    Route::put("/rmp/salidaupdate", 'SalidaMateriaPrimaController@update')->name('updatesalidarmp');
    Route::delete("/rmp/salidadelete", 'SalidaMateriaPrimaController@destroy')->name('deletesalidarmp');
    Route::delete("/rmp/salidadeletemanual", 'SalidaMateriaPrimaController@destroymanual')->name('deletesalidarmpmanual');
    Route::post("/rmp/excelsalida", 'SalidaMateriaPrimaController@export')->name('exportsalidarmp');
    Route::post("/rmp/procesarsalida", 'SalidaMateriaPrimaController@procesar')->name('procesarsalida');
    Route::post("/rmp/desaplicarsalida", 'SalidaMateriaPrimaController@desaplicar')->name('desaplicarsalida');
    Route::get("/rmp/versalidaprevio/{fecha}", 'SalidaMateriaPrimaController@versalidadpreviamp')->name('salidaprevio');
    Route::post("/rmp/desaplicarsalidas", 'SalidaMateriaPrimaController@desaplicardet')->name('desaplicarsaldet');
    Route::post("/rmp/peticion", 'SalidaMateriaPrimaController@Peticion')->name('peticion');
    Route::get("/rmp/peticion", 'SalidaMateriaPrimaController@Peticion')->name('peticion');
    Route::put("/rmp/salida/sumar", 'SalidaMateriaPrimaController@sumar')->name('sumarsalidaMP');
    Route::get("/rmp/vercombinacion/{id}", 'SalidaMateriaPrimaController@ver')->name('vercombinacion');
    Route::post("/rmp/vercombinacion/{id}", 'SalidaMateriaPrimaController@ver')->name('vercombinacion');
    Route::post("/rmp/versalida/{fecha}", 'SalidaMateriaPrimaController@versalida')->name('versalidas');
    Route::get("/rmp/versalida/{fecha}", 'SalidaMateriaPrimaController@versalida')->name('versalidas');
    Route::post("/rmp/versalidaexcel", 'SalidaMateriaPrimaController@excelversalida')->name('excelversalidas');
    Route::get("/rmp/versalidaexcel", 'SalidaMateriaPrimaController@excelversalida')->name('excelversalidas');
    Route::get("/rmp/salidamanual", 'SalidaMateriaPrimaController@salidaMP')->name('salidaMP');
    Route::post("/rmp/storesalidamanual", 'SalidaMateriaPrimaController@salidaMPStore')->name('salidaMPstoremanual');
    Route::put("/rmp/updatesalidamanual", 'SalidaMateriaPrimaController@salidaMPUpdate')->name('salidaMPupdatemanual');
    Route::post("/rmp/aplicarsalidamanual", 'SalidaMateriaPrimaController@procesardet')->name('aplicarMPmanual');
    Route::put("/rmp/desaplicarsalidamanual", 'SalidaMateriaPrimaController@salidaMPUpdate')->name('desaplicarMPmanual');
    Route::get("/rmp/mostrardiferencias", 'SalidaMateriaPrimaController@diferencias')->name('diferenciasrmp');
    Route::post("/rmp/generarMarcasaut", 'SalidaMateriaPrimaController@generarRegistros')->name('generarMarcasAut');
    Route::post("/rmp/salida/detallada/export", 'SalidaMateriaPrimaController@Salidadetallada')->name('reportesalidabultodetalladoexport');
    Route::post("/rmp/verify/{id}", 'SalidaMateriaPrimaController@Verify')->name('rmpverify');


    Route::get("/rmp/diferencias", 'ExistenciaDiarioController@diferencias')->name('rmpdiff');
    //KARDEX    
    Route::get("/rmp/kardex/{codigo}", 'Kardex@index')->name('kardexparametro');

    //Combinaciones Materia Prima.
    Route::get("/rmp/combinaciones", 'CombinacionesController@index')->name('combinaciones');
    Route::get("/rmp/combinaciones/nuevo", 'CombinacionesController@create')->name('combinacionuevo');
    Route::post("/rmp/combinaciones/store", 'CombinacionesController@store')->name('combinacionestore');
    Route::get("/rmp/combinaciones/store", 'CombinacionesController@store')->name('combinacionestore');
    Route::post("/detallecombinacion", 'CombinacionesController@storedetalle')->name('combinacionestoredetalle');
    Route::get("/detallecombinacion", 'CombinacionesController@storedetalle')->name('combinacionestoredetalle');
    Route::post("/detallecombinacionver/{comb}", 'CombinacionesController@verdetalle')->name('vercombinaciones');
    Route::get("/detallecombinacionver/{comb}", 'CombinacionesController@verdetalle')->name('vercombinaciones');
    Route::get("/detalledelete/{id}", 'CombinacionesController@destroydetalle')->name('daletedetalle');
    Route::post("/detalledelete/{id}", 'CombinacionesController@destroydetalle')->name('daletedetalle');
    Route::delete("/detallecombinaciondestroy", 'CombinacionesController@destroy')->name('daletecombinaciones');
    //--------------------------------------------mildaware Admin------------------------------------------------------
    //--------------------------------------------mildaware Admin------------------------------------------------------

    Route::group(['middleware' => 'admin'], function () {
        Route::get('/registro', function () {
            return view('auth.registro');})->name('registro');

        Route::post('/Usuario/registrar', 'Usuario@agregarUsuario')->name('registrarUsuario');
    });
});
