<?php

namespace App\Http\Controllers;

use App\BandaInvInicial;
use App\Calidad;
use App\CInvInicial;
use App\ExistenciaDiario;
use App\Exports\ExistenciaDiarioExports;
use App\Exports\InventarioBandaExport;
use App\InventarioBanda;
use App\Procedencia;
use App\Semilla;
use App\Tamano;
use App\Variedad;
use Carbon\Carbon;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventarioBandaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request){
            $query = trim($request->get("search"));

            $fecha = $request->get("fecha");

            if ($fecha == null) {
                $fecha = Carbon::now()->format('l');
                /*
                if ($fecha == 'Monday') {
                    $fecha = Carbon::now()->subDays(2)->format('Y-m-d');
                    $entregaCapa1=DB::table("capa_entregas")
                        ->leftJoin("empleados","capa_entregas.id_empleado","=","empleados.id")
                        ->leftJoin("vitolas","capa_entregas.id_vitolas","=","vitolas.id")
                        ->leftJoin("semillas","capa_entregas.id_semilla","=","semillas.id")
                        ->leftJoin("marcas","capa_entregas.id_marca","=","marcas.id")
                        ->leftJoin("calidads","capa_entregas.id_calidad","=","calidads.id")


                        ->select("capa_entregas.id","empleados.nombre AS nombre_empleado",
                            "vitolas.name as nombre_vitolas","semillas.name as nombre_semillas",
                            "calidads.name as nombre_calidads",
                            "capa_entregas.id_empleado",
                            "capa_entregas.id_vitolas",
                            "capa_entregas.id_semilla",
                            "capa_entregas.id_calidad",
                            "capa_entregas.id_marca","marcas.name as nombre_marca"
                            ,"capa_entregas.total")
                        ->whereDate("capa_entregas.created_at","=" ,$fecha)
                        ->get();
                    if ($entregaCapa1->count()>0){

                    }
                    else{
                        $fecha = Carbon::now()->subDays(3)->format('Y-m-d');
                    }

                } else {
                    $fecha = Carbon::now()->subDay()->format('Y-m-d');*/
                   $fecha = Carbon::now()->format('Y-m-d');
                //}
            } else{

                $fecha = $request->get("fecha");

            }

            $entregaCapa=DB::table("inventario_bandas")
                ->leftJoin("semillas","inventario_bandas.id_semillas","=","semillas.id")
                ->leftJoin("variedads", "inventario_bandas.id_variedad", "=", "variedads.id")
                ->leftJoin("procedencias", "inventario_bandas.id_procedencia", "=", "procedencias.id")
                ->leftJoin("tamanos","inventario_bandas.id_tamano","=","tamanos.id")
                ->select("inventario_bandas.id","semillas.name as nombre_semillas",
                    "inventario_bandas.id_tamano","tamanos.name as nombre_tamano",
                    "inventario_bandas.id_semillas",
                    "inventario_bandas.id_variedad", "variedads.name as nombre_variedad",
                    "inventario_bandas.id_procedencia", "procedencias.name as nombre_procedencia"
                    ,"inventario_bandas.totalinicial","inventario_bandas.pesoinicial"
                    ,"inventario_bandas.totalentrada","inventario_bandas.pesoentrada"
                    ,"inventario_bandas.totalfinal","inventario_bandas.pesofinal",
                    "inventario_bandas.totalconsumo","inventario_bandas.pesoconsumo"
                    ,"inventario_bandas.pesobanda")
                ->where("semillas.name","Like","%".$query."%")
                ->whereDate("inventario_bandas.created_at","=" ,$fecha)
                ->orderBy("nombre_semillas")
                //  ->whereDate("capa_entregas.created_at","=" ,Carbon::now()->format('Y-m-d'))
                ->paginate(1000);
            $semilla = Semilla::all();
            $tamano = Tamano::all();

            if ($entregaCapa->count()>0){

            }else{
                $inve  =  DB::table('banda_inv_inicials')
                    ->leftJoin("semillas","banda_inv_inicials.id_semilla","=","semillas.id")
                    ->leftJoin("tamanos","banda_inv_inicials.id_tamano","=","tamanos.id")
                    ->leftJoin("variedads", "banda_inv_inicials.id_variedad", "=", "variedads.id")
                    ->leftJoin("procedencias", "banda_inv_inicials.id_procedencia", "=", "procedencias.id")

                    ->select(
                        "banda_inv_inicials.id",
                        "semillas.name as nombre_semillas",
                        "banda_inv_inicials.id_tamano","tamanos.name as nombre_tamano",
                        "banda_inv_inicials.id_variedad", "variedads.name as nombre_variedad",
                        "banda_inv_inicials.id_procedencia", "procedencias.name as nombre_procedencia",
                        "banda_inv_inicials.id_semilla"
                        ,"banda_inv_inicials.totalinicial"
                        ,"banda_inv_inicials.pesoinicial"
                        ,"banda_inv_inicials.onzasI"

                    )->get();

                $fecha1 = $request->get("fecha");
                if ($fecha1 == null) {
                    $fecha1 = Carbon::now()->format('Y-m-d');

                }else{
                    Carbon::parse($fecha1)->format('Y-m-d');

                }
                foreach ($inve as $inventario){
                    $nuevoConsumo = new InventarioBanda();
                    $nuevoConsumo->id_semillas = $inventario->id_semilla;
                    $nuevoConsumo->id_tamano = $inventario->id_tamano;
                    $nuevoConsumo->totalinicial = $inventario->totalinicial;
                    $nuevoConsumo->pesoinicial = $inventario->pesoinicial;
                    $nuevoConsumo->id_variedad = $inventario->id_variedad;
                    $nuevoConsumo->id_procedencia = $inventario->id_procedencia;
                    $nuevoConsumo->created_at = $fecha;
                    $nuevoConsumo->save();
                }
            }

            $entregaCapa=DB::table("inventario_bandas")
                ->leftJoin("semillas","inventario_bandas.id_semillas","=","semillas.id")
                ->leftJoin("variedads", "inventario_bandas.id_variedad", "=", "variedads.id")
                ->leftJoin("procedencias", "inventario_bandas.id_procedencia", "=", "procedencias.id")
                ->leftJoin("tamanos","inventario_bandas.id_tamano","=","tamanos.id")
                ->select("inventario_bandas.id","semillas.name as nombre_semillas",
                    "inventario_bandas.id_tamano","tamanos.name as nombre_tamano",
                    "inventario_bandas.id_semillas",
                    "inventario_bandas.id_variedad", "variedads.name as nombre_variedad",
                    "inventario_bandas.id_procedencia", "procedencias.name as nombre_procedencia"
                    ,"inventario_bandas.totalinicial","inventario_bandas.pesoinicial"
                    ,"inventario_bandas.totalentrada","inventario_bandas.pesoentrada"
                    ,"inventario_bandas.totalfinal","inventario_bandas.pesofinal",
                    "inventario_bandas.totalconsumo","inventario_bandas.pesoconsumo"
                    ,"inventario_bandas.pesobanda")
                ->where("semillas.name","Like","%".$query."%")
                ->whereDate("inventario_bandas.created_at","=" ,$fecha)
                ->orderBy("nombre_semillas")
                //  ->whereDate("capa_entregas.created_at","=" ,Carbon::now()->format('Y-m-d'))
                ->paginate(1000);

            foreach ($entregaCapa as $entrega){
                $recibirCapa = DB::table("entrada_bandas")
                    ->leftJoin("semillas", "entrada_bandas.id_semilla", "=", "semillas.id")
                    ->leftJoin("tamanos", "entrada_bandas.id_tamano", "=", "tamanos.id")
                    ->leftJoin("variedads", "entrada_bandas.id_variedad", "=", "variedads.id")
                    ->leftJoin("procedencias", "entrada_bandas.id_procedencia", "=", "procedencias.id")
                    ->selectRaw(
                        /*"entrada_bandas.id", "tamanos.name AS nombre_tamano",
                    "entrada_bandas.peso",*/
                    "SUM(entrada_bandas.total) as total")->selectRaw("SUM(entrada_bandas.libras) as libras")
                    /*
                        "entrada_bandas.id_tamano",
                        "entrada_bandas.origen",
                        "entrada_bandas.id_semilla", "semillas.name as nombre_semillas",
                        "entrada_bandas.id_variedad", "variedads.name as nombre_variedad",
                        "entrada_bandas.id_procedencia", "procedencias.name as nombre_procedencia"
                        , "entrada_bandas.total" )*/
                    ->where("entrada_bandas.id_semilla","=",$entrega->id_semillas)
                    ->where("entrada_bandas.id_variedad","=",$entrega->id_variedad)
                    ->where("entrada_bandas.id_procedencia","=",$entrega->id_procedencia)
                    ->where("entrada_bandas.id_tamano","=",$entrega->id_tamano)
                    ->whereDate("entrada_bandas.created_at","=" ,$fecha)->get();
                    //return $recibirCapa;

                foreach ($recibirCapa as $reci){
                    if($reci->total!=0){
                    $pesoprom = ($reci->libras/$reci->total)*16*100;
                        $editarCapaEntrega=InventarioBanda::findOrFail($entrega->id);
                        $editarCapaEntrega->totalentrada = $reci->total;
                        $editarCapaEntrega->pesoentrada = $reci->libras;
                        $editarCapaEntrega->save();
                    }

                }
            }


            $entregaCapa=DB::table("inventario_bandas")
                ->leftJoin("semillas","inventario_bandas.id_semillas","=","semillas.id")
                ->leftJoin("variedads", "inventario_bandas.id_variedad", "=", "variedads.id")
                ->leftJoin("procedencias", "inventario_bandas.id_procedencia", "=", "procedencias.id")
                ->leftJoin("tamanos","inventario_bandas.id_tamano","=","tamanos.id")
                ->select("inventario_bandas.id","semillas.name as nombre_semillas",
                    "inventario_bandas.id_tamano","tamanos.name as nombre_tamano",
                    "inventario_bandas.id_semillas",
                    "inventario_bandas.id_variedad", "variedads.name as nombre_variedad",
                    "inventario_bandas.id_procedencia", "procedencias.name as nombre_procedencia"
                    ,"inventario_bandas.totalinicial","inventario_bandas.pesoinicial"
                    ,"inventario_bandas.totalentrada","inventario_bandas.pesoentrada"
                    ,"inventario_bandas.totalfinal","inventario_bandas.pesofinal",
                    "inventario_bandas.totalconsumo","inventario_bandas.pesoconsumo"
                    ,"inventario_bandas.pesobanda")
                ->where("semillas.name","Like","%".$query."%")
                ->whereDate("inventario_bandas.created_at","=" ,$fecha)
                ->orderBy("nombre_semillas")
                //  ->whereDate("capa_entregas.created_at","=" ,Carbon::now()->format('Y-m-d'))
                ->paginate(1000);

            //PARA REGISTRAR EL CONSUMO EN EL INVENTARIO DE BANDA DE FORMA AUTOMATICA
            foreach ($entregaCapa as $entrega){
                $recibirCapa = DB::table("consumo_bandas")
                    ->leftJoin("semillas", "consumo_bandas.id_semillas", "=", "semillas.id")
                    ->leftJoin("tamanos", "consumo_bandas.id_tamano", "=", "tamanos.id")
                    ->leftJoin("variedads", "consumo_bandas.variedad", "=", "variedads.id")
                    ->leftJoin("procedencias", "consumo_bandas.procedencia", "=", "procedencias.id")
                    ->selectRaw('SUM(consumo_bandas.total) as con')
                    ->where("consumo_bandas.id_semillas","=",$entrega->id_semillas)
                    ->where("consumo_bandas.variedad","=",$entrega->id_variedad)
                    ->where("consumo_bandas.procedencia","=",$entrega->id_procedencia)
                    ->where("consumo_bandas.id_tamano","=",$entrega->id_tamano)
                    ->whereDate("consumo_bandas.created_at","=" ,$fecha)
                    //->groupBy('con')
                    ->get();
                    //return $recibirCapa;


                /*foreach ($recibirCapa as $reci){
                    if($reci->con!=null){
                        $editarCapaEntrega=InventarioBanda::findOrFail($entrega->id);
                        $editarCapaEntrega->totalconsumo = $reci->con;
                        $final = ($entrega->totalinicial + $entrega->totalentrada) - $editarCapaEntrega->totalconsumo;
                        $editarCapaEntrega->totalfinal = $final;
                       // $editarCapaEntrega->totalconsumo = $reci->total;
                        $editarCapaEntrega->save();

                    }
                }*/

                /*foreach ($recibirCapa as $reci){
                        $final = ($entrega->totalinicial + $entrega->totalentrada) - $entrega->totalconsumo;
                        $editarCapaEntrega=InventarioBanda::findOrFail($entrega->id);
                        $editarCapaEntrega->totalfinal = $final;
                        //$editarCapaEntrega->totalfinal = $final;
                        $editarCapaEntrega->save();

                }*/



            }


            $entregaCapa=DB::table("inventario_bandas")
                ->leftJoin("semillas","inventario_bandas.id_semillas","=","semillas.id")
                ->leftJoin("variedads", "inventario_bandas.id_variedad", "=", "variedads.id")
                ->leftJoin("procedencias", "inventario_bandas.id_procedencia", "=", "procedencias.id")
                ->leftJoin("tamanos","inventario_bandas.id_tamano","=","tamanos.id")
                ->select("inventario_bandas.id","semillas.name as nombre_semillas",
                    "inventario_bandas.id_tamano","tamanos.name as nombre_tamano",
                    "inventario_bandas.id_semillas",
                    "inventario_bandas.id_variedad", "variedads.name as nombre_variedad",
                    "inventario_bandas.id_procedencia", "procedencias.name as nombre_procedencia"
                    ,"inventario_bandas.totalinicial","inventario_bandas.pesoinicial"
                    ,"inventario_bandas.totalentrada","inventario_bandas.pesoentrada"
                    ,"inventario_bandas.totalfinal","inventario_bandas.pesofinal",
                    "inventario_bandas.totalconsumo","inventario_bandas.pesoconsumo"
                    ,"inventario_bandas.pesobanda")
                ->where("semillas.name","Like","%".$query."%")
                ->whereDate("inventario_bandas.created_at","=" ,$fecha)
                ->orderBy("nombre_semillas")->orderBy("nombre_tamano")->orderBy("nombre_variedad")
                //  ->whereDate("capa_entregas.created_at","=" ,Carbon::now()->format('Y-m-d'))
                ->paginate(1000);
            $semilla = Semilla::all();
            $calidad = Calidad::all();
            $tamano = Tamano::all();
            $variedad = Variedad::all();
            $procedencia =Procedencia::all();
            return view("InventariosDiarios.InventarioBanda")
                ->withNoPagina(1)
                ->with('fecha', $fecha)
                ->withExistenciaDiaria($entregaCapa)
                ->withSemilla($semilla)
                ->withTamano($tamano)
                ->withCalidad($calidad)
                ->withVariedad($variedad)
                ->withProcedencia($procedencia);

        }
    }



    public function store(Request $request)
    {

        $fecha = $request->get("fecha");
        $fecha1 = Carbon::parse($fecha)->format('Y-m-d');
        $inve  = DB::table("banda_inv_inicials")
        ->leftJoin("semillas", "banda_inv_inicials.id_semilla", "=", "semillas.id")
        ->leftJoin("tamanos", "banda_inv_inicials.id_tamano", "=", "tamanos.id")
        ->leftJoin("variedads", "banda_inv_inicials.id_variedad", "=", "variedads.id")
        ->leftJoin("procedencias", "banda_inv_inicials.id_procedencia", "=", "procedencias.id")

            ->select("banda_inv_inicials.id", "tamanos.name AS nombre_tamano",
            "banda_inv_inicials.id_tamano",
                "banda_inv_inicials.updated_at",
            "banda_inv_inicials.id_semilla", "semillas.name as nombre_semillas",
                "banda_inv_inicials.id_variedad", "variedads.name as nombre_variedad",
                "banda_inv_inicials.id_procedencia", "procedencias.name as nombre_procedencia"
            , "banda_inv_inicials.totalinicial" )
        ->where("banda_inv_inicials.id_semilla","=",$request->input('id_semillas'))

            ->where("banda_inv_inicials.id_variedad","=",$request->input('id_variedad'))
            ->where("banda_inv_inicials.id_procedencia","=",$request->input('id_procedencia'))
            ->where("banda_inv_inicials.id_tamano","=",$request->input("id_tamano"))
            ->where("banda_inv_inicials.id_semilla","=",$request->input("id_semilla"))->get();
        if($inve->count()>0){


            $inventarioDiario=DB::table("inventario_bandas")
                ->leftJoin("semillas","inventario_bandas.id_semillas","=","semillas.id")

                ->leftJoin("variedads", "inventario_bandas.id_variedad", "=", "variedads.id")
                ->leftJoin("procedencias", "inventario_bandas.id_procedencia", "=", "procedencias.id")
                ->leftJoin("tamanos","inventario_bandas.id_tamano","=","tamanos.id")
                ->select("inventario_bandas.id"
                    ,"inventario_bandas.created_at")
                ->where("inventario_bandas.id","=",$request->id)->get();

            foreach  ($inve as $inventario) {

                foreach ($inventarioDiario as $diario) {
                    $ingresada = $diario->created_at;
                }
                $actual = $inventario->updated_at;

                if (Carbon::parse($ingresada)->format('Y-m-d') >= (Carbon::parse($actual)->format('Y-m-d'))) {

                    $totales = $request->input("totalinicial")+$request->input("totalentrada")-$request->input("totalfinal");
                    $libraIni= ($request->input("pesoinicial")/100)*$request->input("totalinicial")/16;
                    $libraEnt= ($request->input("pesoentrada")/100)*$request->input("totalentrada")/16;
                    $libraCon= ($request->input("pesofinal")/100)*$request->input("totalfinal")/16;
                    $totalLibras=($libraIni+$libraEnt)-$libraCon;
                    $resultado = (($totalLibras/$totales)*16)*100;
                    $editarBultoEntrega = BandaInvInicial::findOrFail($inventario->id);
                    $editarBultoEntrega->totalinicial = ($request->input("totalinicial")+$request->input("totalentrada"))-$request->input("totalfinal");
                    $editarBultoEntrega->pesoinicial = $resultado;
                    //$editarBultoEntrega->pesoinicial=($request->input("pesoinicial")+$request->input("pesoentrada"))-$request->input("pesofinal");
                    $editarBultoEntrega->updated_at = Carbon::parse($ingresada)->format('Y-m-d');
                    $editarBultoEntrega->id_variedad= $request->input("id_variedad");
                    $editarBultoEntrega->id_procedencia= $request->input("id_procedencia");

                    $editarBultoEntrega->save();
                }
            }


        }else{
            $totales = $request->input("totalinicial")+$request->input("totalentrada")-$request->input("totalfinal");
                    $libraIni= ($request->input("pesoinicial")/100)*$request->input("totalinicial")/16;
                    $libraEnt= ($request->input("pesoentrada")/100)*$request->input("totalentrada")/16;
                    $libraCon= ($request->input("pesofinal")/100)*$request->input("totalfinal")/16;
                    $totalLibras=($libraIni+$libraEnt)-$libraCon;
                    $resultado = (($totalLibras/$totales)*16)*100;
            $nuevoConsumo = new BandaInvInicial();

            $nuevoConsumo->id_semilla = $request->input('id_semillas');
            $nuevoConsumo->id_tamano = $request->input("id_tamano");
            $nuevoConsumo->totalinicial = ($request->input("totalinicial")+$request->input("totalentrada"))-$request->input("totalfinal");
            $nuevoConsumo->pesoinicial= $resultado;
            //$nuevoConsumo->pesoinicial= ($request->input("pesoinicial")+$request->input("pesoentrada"))-$request->input("pesofinal");
            $nuevoConsumo->id_variedad= $request->input("id_variedad");
            $nuevoConsumo->id_procedencia= $request->input("id_procedencia");
            $nuevoConsumo->updated_at =$fecha1;
            $nuevoConsumo->created_at =$fecha1;
            $nuevoConsumo->save();
        }
        //el total fina es el consumo, no se cambio el name por time
        $nuevoInvDiario = new InventarioBanda();
        $nuevoInvDiario->id_semillas=$request->input('id_semillas');
        $nuevoInvDiario->id_variedad= $request->input("id_variedad");
        $nuevoInvDiario->id_procedencia= $request->input("id_procedencia");
        $nuevoInvDiario->id_tamano=$request->input("id_tamano");
        $nuevoInvDiario->totalinicial=$request->input("totalinicial");
        $nuevoInvDiario->pesoinicial=$request->input("pesoinicial")/ ($request->input("totalinicial")/100)*16;
        $libraI= ($nuevoInvDiario->pesoinicial/100)*$nuevoInvDiario->totalinicial/16;
        $nuevoInvDiario->totalentrada=$request->input("totalentrada");
        $nuevoInvDiario->pesoentrada=$request->input("pesoentrada")/ ($request->input("totalentrada")/100)*16;
        $libraE= ($nuevoInvDiario->pesoentrada/100)*$nuevoInvDiario->totalentrada/16;
        $nuevoInvDiario->totalconsumo=$request->input("totalfinal");
        $nuevoInvDiario->pesoconsumo=$request->input("pesofinal")/ ($request->input("totalfinal")/100)*16;
        $libraC= ($nuevoInvDiario->pesoconsumo/100)*$nuevoInvDiario->totalconsumo/16;
        $nuevoInvDiario->totalfinal=($request->input("totalinicial")+$request->input("totalentrada"))-$request->input("totalfinal");
        $totalfinalcalculo =( $nuevoInvDiario->totalinicial+$nuevoInvDiario->totalentrada)- $nuevoInvDiario->totalconsumo;
        $totalLibras=($libraI+$libraE)-$libraC;
        $totaltotales=$totalfinalcalculo;
        $resultado = (($totalLibras/$totaltotales)*16)*100;
        $nuevoInvDiario->pesofinal = $resultado;

        $nuevoInvDiario->created_at =$fecha1;





        $nuevoInvDiario->save();

       // return redirect()->route("InventarioBanda")->withExito("Se creó la entrega Correctamente ");
       return back()->withExito("Se creó la entrega Correctamente ");

        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\InventarioBanda  $inventarioBanda
     * @return \Illuminate\Http\Response
     */
    public function show(InventarioBanda $inventarioBanda)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\InventarioBanda  $inventarioBanda
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        try {



        $total = ($request->input("totalfinal"));

            $inve  = DB::table("banda_inv_inicials")
                ->leftJoin("semillas", "banda_inv_inicials.id_semilla", "=", "semillas.id")
                ->leftJoin("variedads", "banda_inv_inicials.id_variedad", "=", "variedads.id")
                ->leftJoin("procedencias", "banda_inv_inicials.id_procedencia", "=", "procedencias.id")
                ->leftJoin("tamanos", "banda_inv_inicials.id_tamano", "=", "tamanos.id")
                ->select("banda_inv_inicials.id", "tamanos.name AS nombre_tamano",
                    "banda_inv_inicials.id_tamano"
                    ,"banda_inv_inicials.updated_at",
                    "banda_inv_inicials.id_semilla", "semillas.name as nombre_semillas"
                    , "banda_inv_inicials.totalinicial" , "banda_inv_inicials.id_variedad")
                ->where("banda_inv_inicials.id_semilla","=",$request->input('id_semillas'))
                ->where("banda_inv_inicials.id_variedad","=",$request->input("id_variedad"))
                ->where("banda_inv_inicials.id_procedencia","=",$request->input("id_procedencia"))
                ->where("banda_inv_inicials.id_tamano","=",$request->input("id_tamano"))
                ->where("banda_inv_inicials.id_variedad","=",$request->input("id_variedad"))->get();

                  $inventarioDiario=DB::table("inventario_bandas")
                ->leftJoin("semillas","inventario_bandas.id_semillas","=","semillas.id")

                ->leftJoin("variedads", "inventario_bandas.id_variedad", "=", "variedads.id")
                ->leftJoin("procedencias", "inventario_bandas.id_procedencia", "=", "procedencias.id")
                ->leftJoin("tamanos","inventario_bandas.id_tamano","=","tamanos.id")
                ->select("inventario_bandas.id"
                    ,"inventario_bandas.created_at")
                ->where("inventario_bandas.id","=",$request->id)->get();

            foreach  ($inve as $inventario) {

                foreach ($inventarioDiario as $diario) {
                    $ingresada = $diario->created_at;
                }
                $actual = $inventario->updated_at;

               // if (Carbon::parse($ingresada)->format('Y-m-d') >= (Carbon::parse($actual)->format('Y-m-d'))) {
                    $totales = $request->input("totalinicial")+$request->input("totalentrada")-$request->input("totalfinal");
                    $libraIni= ($request->input("pesoinicial"));///100)*$request->input("totalinicial")/16;
                    $libraEnt= ($request->input("pesoentrada"));///100)*$request->input("totalentrada")/16;
                    $libraCon= ($request->input("pesofinal"));///100)*$request->input("totalfinal")/16;
                    $totalLibras=($libraIni+$libraEnt)-$libraCon;
                   // $resultado = (($totalLibras/$totales)*16)*100;

                    $editarBultoEntrega = BandaInvInicial::findOrFail($inventario->id);
                    $editarBultoEntrega->totalinicial = $request->input('totalfinal');
                    //($request->input("totalinicial")+$request->input("totalentrada"))-$request->input("totalfinal");
                    $editarBultoEntrega->pesoinicial=$request->input('pesofinal');
                    //resultado;
                    //($request->input("pesoinicial")+$request->input("pesoentrada"))-$request->input("pesofinal");
                    $editarBultoEntrega->id_semilla = $request->input('id_semillas');
                    $editarBultoEntrega->id_tamano = $request->input("id_tamano");
                    $editarBultoEntrega->updated_at = Carbon::parse($ingresada)->format('Y-m-d');
                    $editarBultoEntrega->id_variedad= $request->input("id_variedad");
                    $editarBultoEntrega->id_procedencia= $request->input("id_procedencia");

                    $editarBultoEntrega->save();
               // }
            }
        //El input que se llama pesofinal y totalfinal ahora son consumo
        // no se le cambio el nombre por cuestion de tiempo

            $nuevoInvDiario= InventarioBanda::findOrFail($request->id);
            $nuevoInvDiario->id_semillas=$request->input('id_semillas');
            $nuevoInvDiario->id_variedad= $request->input("id_variedad");
            $nuevoInvDiario->id_procedencia= $request->input("id_procedencia");
            $nuevoInvDiario->id_tamano=$request->input("id_tamano");
            $nuevoInvDiario->totalinicial=$request->input("totalinicial");
            $nuevoInvDiario->pesoinicial=$request->input("pesoinicial");//($request->input("totalinicial")/100)*16;
            //$libraI= ($nuevoInvDiario->pesoinicial/100)*$nuevoInvDiario->totalinicial/16;
            $nuevoInvDiario->totalentrada=$request->input("totalentrada");
            $nuevoInvDiario->pesoentrada=$request->input("pesoentrada");//($request->input("totalentrada")/100)*16;
           // $libraE= ($nuevoInvDiario->pesoentrada/100)*$nuevoInvDiario->totalentrada/16;


            $nuevoInvDiario->totalfinal=$request->input("totalfinal");
            $nuevoInvDiario->pesofinal = $request->input("pesofinal");//($request->input('totalfinal')/100)*16;

            $nuevoInvDiario->pesoconsumo= $request->input("pesoinicial")+$request->input("pesoentrada")-$request->input("pesofinal");
            //$request->input("pesofinal")/($request->input("totalfinal")/100)*16;
            $nuevoInvDiario->totalconsumo= ($request->input("totalinicial")+$request->input("totalentrada"))-$request->input("totalfinal");;


            /*$libraC= ($nuevoInvDiario->pesofinal/100)*$nuevoInvDiario->totalconsumo/16;
            $totalfinalcalculo =( $nuevoInvDiario->totalinicial+$nuevoInvDiario->totalentrada)- $nuevoInvDiario->totalconsumo;
            $totalLibras=($libraI+$libraE)-$libraC;
            $totaltotales=$totalfinalcalculo;
            $resultado = (($totalLibras/$totaltotales)*16)*100;
            //return $resultado;*/



            $nuevoInvDiario->save();
            return back()->withExito("Se creó la entrega Correctamente ");
       // return redirect()->route("InventarioBanda")->withExito("Se editó Correctamente");

    }catch (ValidationException $exception){
        return redirect()->route("InventarioBanda")->with('errores','errores')->with('id_capa_entregas',$request->input("id"))->withErrors($exception->errors());
    }
        //
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\InventarioBanda  $inventarioBanda
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, InventarioBanda $inventarioBanda)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\InventarioBanda  $inventarioBanda
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $capaentrega = $request->input('id');
        $borrar = InventarioBanda::findOrFail($capaentrega);

        $borrar->delete();
        return back()->withExito("Se borro Correctamente ");
        //return redirect()->route("InventarioBanda")->withExito("Se borró la entrega satisfactoriamente");
    }


    public function destroyall(Request $request)
    {
        $fechita = $request->input('fecha');
        $validacion = DB::table('inventario_bandas')->select('inventario_bandas.id')
        ->where('inventario_bandas.created_at', '=', $fechita)
        ->whereNull('inventario_bandas.pesoconsumo')->delete();
        $inve  =  DB::table('banda_inv_inicials')
        ->leftJoin("semillas","banda_inv_inicials.id_semilla","=","semillas.id")
        ->leftJoin("tamanos","banda_inv_inicials.id_tamano","=","tamanos.id")
        ->leftJoin("variedads", "banda_inv_inicials.id_variedad", "=", "variedads.id")
        ->leftJoin("procedencias", "banda_inv_inicials.id_procedencia", "=", "procedencias.id")

        ->select(
            "banda_inv_inicials.id",
            "semillas.name as nombre_semillas",
            "banda_inv_inicials.id_tamano","tamanos.name as nombre_tamano",
            "banda_inv_inicials.id_variedad", "variedads.name as nombre_variedad",
            "banda_inv_inicials.id_procedencia", "procedencias.name as nombre_procedencia",
            "banda_inv_inicials.id_semilla"
            ,"banda_inv_inicials.totalinicial"
            ,"banda_inv_inicials.pesoinicial"
            ,"banda_inv_inicials.onzasI"

        )
        ->whereNotExists(function ($query) use($fechita){
            $query->select(DB::raw(1))->from('inventario_bandas')->
            whereRaw('banda_inv_inicials.id_semilla= inventario_bandas.id_semillas')
            ->whereRaw('banda_inv_inicials.id_tamano= inventario_bandas.id_tamano')
            ->whereRaw('banda_inv_inicials.id_variedad= inventario_bandas.id_variedad')
            ->whereRaw('banda_inv_inicials.id_procedencia= inventario_bandas.id_procedencia')
            ->where('inventario_bandas.created_at', '=', $fechita);
        })
        ->get();

        foreach($inve as $inventario){
            $nuevoConsumo = new InventarioBanda();
                    $nuevoConsumo->id_semillas = $inventario->id_semilla;
                    $nuevoConsumo->id_tamano = $inventario->id_tamano;
                    $nuevoConsumo->totalinicial = $inventario->totalinicial;
                    $nuevoConsumo->pesoinicial = $inventario->pesoinicial;
                    $nuevoConsumo->id_variedad = $inventario->id_variedad;
                    $nuevoConsumo->id_procedencia = $inventario->id_procedencia;
                    $nuevoConsumo->created_at = $fechita;
                    $nuevoConsumo->save();


        }
        return back()->withExito("Se actualizo Correctamente");
    }


    public function export(Request $request)
{

    $fecha = $request->get("fecha1");

    if ($fecha = null)
        $fecha = Carbon::now()->format('Y-m-d');
    else {
        $fecha = Carbon::parse(  $request->get("fecha1"))->format('Y-m-d');

    }
    return (new InventarioBandaExport($fecha))->download('Listado Inventario de Banda'.$fecha.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);

}

    public function exportpdf(Request $request)
    {
        $fecha = $request->get("fecha1");

        if ($fecha = null)
            $fecha = Carbon::now()->format('Y-m-d');
        else {
            $fecha = Carbon::parse(  $request->get("fecha1"))->format('Y-m-d');

        }
        return (new InventarioBandaExport($fecha))->download('Listado Inventario de Banda'.$fecha.'.pdf', \Maatwebsite\Excel\Excel::DOMPDF);

    }
    public function exportcvs(Request $request)
    {
        $fecha = $request->get("fecha1");

        if ($fecha = null)
            $fecha = Carbon::now()->format('Y-m-d');
        else {
            $fecha = Carbon::parse(  $request->get("fecha1"))->format('Y-m-d');

        }
        return (new InventarioBandaExport($fecha))->download('Listado Inventario de Banda '.$fecha.'.csv', \Maatwebsite\Excel\Excel::CSV);
    }
}
