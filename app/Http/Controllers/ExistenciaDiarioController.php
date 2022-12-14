<?php

namespace App\Http\Controllers;

use App\BInvInicial;
use App\Calidad;
use App\CapaEntrega;
use App\CInvInicial;
use App\ConsumoBanda;
use App\Empleado;
use App\ExistenciaDiario;
use App\Exports\ConsumoBandaExport;
use App\Exports\ExistenciaDiarioExports;
use App\Marca;
use App\ReBulDiario;
use App\Semilla;
use App\Tamano;
use App\Vitola;
use Carbon\Carbon;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExistenciaDiarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        if ($request) {
            $query = trim($request->get("search"));

            $fecha = $request->get("fecha");
        }
            if ($fecha == null) {
                $fecha = Carbon::now()->format('l');
                $fecha = Carbon::now()->format('Y-m-d');
            } else{

                $fecha = $request->get("fecha");

            }

            $entregaCapa=DB::table("existencia_diarios")
                ->leftJoin("semillas","existencia_diarios.id_semillas","=","semillas.id")
                ->leftJoin("calidads","existencia_diarios.id_calidad","=","calidads.id")
                ->leftJoin("tamanos","existencia_diarios.id_tamano","=","tamanos.id")
                ->select("existencia_diarios.id","semillas.name as nombre_semillas",
                    "calidads.name as nombre_calidads",
                    "existencia_diarios.id_tamano","tamanos.name as nombre_tamano",
                    "existencia_diarios.id_semillas",
                    "existencia_diarios.id_calidad"
                    ,"existencia_diarios.totalinicial","existencia_diarios.pesoinicial"
                    ,"existencia_diarios.totalentrada","existencia_diarios.pesoentrada"
                    ,"existencia_diarios.totalfinal","existencia_diarios.pesofinal",
                    "existencia_diarios.totalconsumo","existencia_diarios.pesoconsumo"
                    ,"existencia_diarios.otras", "existencia_diarios.pesootras"
                    ,"existencia_diarios.onzasO"
                    ,"existencia_diarios.onzasI"
                    ,"existencia_diarios.onzasE"
                    ,"existencia_diarios.onzasF")
                ->whereDate("existencia_diarios.created_at","=" ,$fecha)
                ->orderBy("nombre_semillas")
                ->paginate(1000);
            $semilla = Semilla::all();
            $calidad = Calidad::all();
            $tamano = Tamano::all();

            if ($entregaCapa->count()>0){

            }else{
                $inve  =  DB::table('c_inv_inicials')
                    ->leftJoin("semillas","c_inv_inicials.id_semilla","=","semillas.id")
                    ->leftJoin("calidads","c_inv_inicials.id_calidad","=","calidads.id")
                    ->leftJoin("tamanos","c_inv_inicials.id_tamano","=","tamanos.id")

                    ->select(
                        "c_inv_inicials.id",
                       "semillas.name as nombre_semillas",
                        "calidads.name as nombre_calidads",
                        "c_inv_inicials.id_tamano","tamanos.name as nombre_tamano",
                        "c_inv_inicials.id_semilla",
                        "c_inv_inicials.id_calidad"
                        ,"c_inv_inicials.totalinicial"
                        ,"c_inv_inicials.pesoinicial"
                        ,"c_inv_inicials.onzasI"
                    )->get();

                $fecha1 = $request->get("fecha");
                if ($fecha1 == null) {
                    $fecha1 = Carbon::now()->format('Y-m-d');

                }else{
                    Carbon::parse($fecha1)->format('Y-m-d');

                }
                foreach ($inve as $inventario){
                    $nuevoConsumo = new ExistenciaDiario();
                    $nuevoConsumo->id_semillas = $inventario->id_semilla;
                    $nuevoConsumo->id_calidad = $inventario->id_calidad;
                    $nuevoConsumo->id_tamano = $inventario->id_tamano;
                    $nuevoConsumo->totalinicial = $inventario->totalinicial;
                    $nuevoConsumo->pesoinicial = $inventario->pesoinicial;
                    $nuevoConsumo->onzasI = $inventario->onzasI;

                    $nuevoConsumo->created_at = $fecha;
                    $nuevoConsumo->save();
                }
            }

            foreach ($entregaCapa as $entrega){
                $recibirCapa=DB::table("recibir_capas")
                ->leftJoin("semillas","recibir_capas.id_semillas","=","semillas.id")
                ->leftJoin("tamanos","recibir_capas.id_tamano","=","tamanos.id")
                ->leftJoin("calidads","recibir_capas.id_calidad","=","calidads.id")

                ->select("recibir_capas.id","tamanos.name AS nombre_tamano",
                    "recibir_capas.id_tamano",
                    "recibir_capas.id_calidad","calidads.name as nombre_calidad",
                    "recibir_capas.id_semillas","semillas.name as nombre_semillas","recibir_capas.total")
                ->where("recibir_capas.id_semillas","=",$entrega->id_semillas)
                ->where("recibir_capas.id_tamano","=",$entrega->id_tamano)
                ->where("recibir_capas.id_calidad","=",$entrega->id_calidad)
                ->whereDate("recibir_capas.created_at","=" ,$fecha)->get();



                foreach ($recibirCapa as $reci){
                    if ($entrega->totalentrada == $reci->total){
                         } else{

                    $editarCapaEntrega=ExistenciaDiario::findOrFail($entrega->id);

                    $editarCapaEntrega->totalentrada = $reci->total;
                    $editarCapaEntrega->save();

                }

                }



            }
            $entregaCapas=DB::table("existencia_diarios")
                ->leftJoin("semillas","existencia_diarios.id_semillas","=","semillas.id")
                ->leftJoin("calidads","existencia_diarios.id_calidad","=","calidads.id")
                ->leftJoin("tamanos","existencia_diarios.id_tamano","=","tamanos.id")
                ->select("existencia_diarios.id","semillas.name as nombre_semillas",
                "calidads.id as ord",
                    "calidads.name as nombre_calidads",
                    "existencia_diarios.id_tamano","tamanos.name as nombre_tamano",
                    "existencia_diarios.id_semillas",
                    "existencia_diarios.id_calidad"
                    ,"existencia_diarios.totalinicial","existencia_diarios.pesoinicial"
                    ,"existencia_diarios.totalentrada","existencia_diarios.pesoentrada"
                    ,"existencia_diarios.totalfinal","existencia_diarios.pesofinal",
                    "existencia_diarios.totalconsumo","existencia_diarios.pesoconsumo"
                    ,"existencia_diarios.otras", "existencia_diarios.pesootras"
                    ,"existencia_diarios.onzasO"
                    ,"existencia_diarios.onzasI"
                    ,"existencia_diarios.onzasE"
                    ,"existencia_diarios.onzasF")
                ->where("semillas.name","Like","%".$query."%")
                ->whereDate("existencia_diarios.created_at","=" ,$fecha)
                ->orderBy("nombre_semillas")
                ->orderBy("ord")
                ->paginate(1000);
            $semilla = Semilla::all();
            $calidad = Calidad::all();
            $tamano = Tamano::all();
            $uri = $request->path();
            return view("InventariosDiarios.ExistenciaDiario")
            ->with('fecha', $fecha)
                ->withNoPagina(1)
                ->withExistenciaDiaria($entregaCapas)
                ->withSemilla($semilla)
                ->withTamano($tamano)
                ->withCalidad($calidad);

        }

    public function store(Request $request)
    {
        $fecha =$request->input('fecha');
        if ($fecha == null){
            $fecha = Carbon::now()->format('Y-m-d');
        }
        else{
            $fecha = $request->get("fecha");

        }
        $fecha1 = Carbon::parse($fecha)->format('Y-m-d');
        $inve  =  DB::table('c_inv_inicials')
            ->leftJoin("semillas","c_inv_inicials.id_semilla","=","semillas.id")
            ->leftJoin("calidads","c_inv_inicials.id_calidad","=","calidads.id")
            ->leftJoin("tamanos","c_inv_inicials.id_tamano","=","tamanos.id")

            ->select(
                "c_inv_inicials.id",
                "semillas.name as nombre_semillas",
                "calidads.name as nombre_calidads",
                "c_inv_inicials.id_tamano","tamanos.name as nombre_tamano",
                "c_inv_inicials.id_semilla",
                "c_inv_inicials.id_calidad"
                ,"c_inv_inicials.totalinicial", "c_inv_inicials.updated_at"
            )
            ->where("c_inv_inicials.id_semilla","=",$request->input('id_semillas'))
            ->where("c_inv_inicials.id_tamano","=",$request->input("id_tamano"))
            ->where("c_inv_inicials.id_calidad","=",$request->input('id_calidad'))->get();
        if($inve->count()>0){


            $inventarioDiario=DB::table("existencia_diarios")
                ->leftJoin("semillas","existencia_diarios.id_semillas","=","semillas.id")
                ->leftJoin("calidads","existencia_diarios.id_calidad","=","calidads.id")
                ->leftJoin("tamanos","existencia_diarios.id_tamano","=","tamanos.id")
                ->select("existencia_diarios.id"
                    ,"existencia_diarios.created_at")
                ->where("existencia_diarios.id","=",$request->id)->get();

            foreach  ($inve as $inventario) {

                foreach ($inventarioDiario as $diario) {
                    $ingresada = $diario->created_at;
                }

                $actual = $inventario->updated_at;

                if (Carbon::parse($ingresada)->format('Y-m-d') >= (Carbon::parse($actual)->format('Y-m-d'))) {



                    $editarBultoEntrega = CInvInicial::findOrFail($inventario->id);
                    $editarBultoEntrega->totalinicial = $request->input("totalfinal");
                    $editarBultoEntrega->pesoinicial=(($request->input("onzasF")*($request->input("totalfinal")/50))/16);
                    $editarBultoEntrega->updated_at = Carbon::parse($ingresada)->format('Y-m-d');
                    $editarBultoEntrega->onzasI = $request->input("onzasF");

                    $editarBultoEntrega->save();
                }

            }

        }else{
            $nuevoConsumo = new CInvInicial();

            $nuevoConsumo->id_semilla = $request->input('id_semillas');
            $nuevoConsumo->id_calidad = $request->input('id_calidad');
            $nuevoConsumo->id_tamano = $request->input("id_tamano");
            $nuevoConsumo->totalinicial = $request->input("totalfinal");
           // $nuevoConsumo->totalinicial = ($request->input("totalinicial")+$request->input("totalentrada"))-$request->input("totalfinal");
            $final = $request->input("onzasF");
            $entrada =  $request->input("onzasE");
            if($final==null){
            $nuevoConsumo->pesoinicial=(($request->input("onzasI")*($request->input("totalfinal")/50))/16);
            $nuevoConsumo->onzasI = $request->input("onzasI");

            }else{
            $nuevoConsumo->pesoinicial=(($request->input("onzasF")*($request->input("totalfinal")/50))/16);
            $nuevoConsumo->onzasI = $request->input("onzasF");

            }

            $nuevoConsumo->updated_at =$fecha1;
            $nuevoConsumo->created_at =$fecha1;
            $nuevoConsumo->save();

        }
        $f = $request->input("onzasF");
        $e =  $request->input("onzasE");
        if ($f  == null ){
            $EditarInvDiario = new ExistenciaDiario();
            $EditarInvDiario->id_semillas = $request->input('id_semillas');
            $EditarInvDiario->id_calidad = $request->input('id_calidad');
            $EditarInvDiario->id_tamano = $request->input("id_tamano");
            $EditarInvDiario->totalinicial = $request->input("totalinicial");
            $EditarInvDiario->pesoinicial = (($request->input("onzasI") * ($request->input("totalinicial") / 50)) / 16);
            $EditarInvDiario->totalentrada = $request->input("totalentrada");
            $EditarInvDiario->pesoentrada = (($request->input("onzasI") * ($request->input("totalentrada") / 50)) / 16);
            $EditarInvDiario->totalfinal = $request->input("totalfinal");
            $EditarInvDiario->pesofinal = (($request->input("onzasI") * ($request->input("totalfinal") / 50)) / 16);

            $EditarInvDiario->totalconsumo = ($request->input("totalinicial")
            + $request->input("totalentrada")) - $request->input("totalfinal");

            $EditarInvDiario->pesoconsumo = $EditarInvDiario->pesoinicial
            +$EditarInvDiario->pesoentrada-$EditarInvDiario->pesofinal;

            $EditarInvDiario->onzasI = $request->input("onzasI");
            $EditarInvDiario->onzasE = $request->input("onzasI");
            $EditarInvDiario->onzasF = $request->input("onzasI");
            $EditarInvDiario->created_at =$fecha1;
            $EditarInvDiario->save();
        }else {
            $EditarInvDiario = new ExistenciaDiario();
            $EditarInvDiario->id_semillas = $request->input('id_semillas');
            $EditarInvDiario->id_calidad = $request->input('id_calidad');
            $EditarInvDiario->id_tamano = $request->input("id_tamano");
            $EditarInvDiario->totalinicial = $request->input("totalinicial");
            $EditarInvDiario->pesoinicial = (($request->input("onzasI") * ($request->input("totalinicial") / 50)) / 16);
            $EditarInvDiario->totalentrada = $request->input("totalentrada");
            $EditarInvDiario->pesoentrada = (($request->input("onzasE") * ($request->input("totalentrada") / 50)) / 16);
            $EditarInvDiario->totalfinal = $request->input("totalfinal");
            $EditarInvDiario->pesofinal = (($request->input("onzasF") * ($request->input("totalfinal") / 50)) / 16);

            $EditarInvDiario->totalconsumo = ($request->input("totalinicial")
            + $request->input("totalentrada")) - $request->input("totalfinal");

            $EditarInvDiario->pesoconsumo = $EditarInvDiario->pesoinicial
            +$EditarInvDiario->pesoentrada-$EditarInvDiario->pesofinal;

            $EditarInvDiario->onzasI = $request->input("onzasI");
            $EditarInvDiario->onzasE = $request->input("onzasE");
            $EditarInvDiario->onzasF = $request->input("onzasF");
            $EditarInvDiario->created_at =$fecha1;
            $EditarInvDiario->save();
        }

        return back();
    }


    public function edit(Request $request)
    {
        try {
            $sloot="2021/11/14";
           $crazy = (Carbon::parse($sloot)->format('Y-m-d'));

        $total = ($request->input("totalfinal"));
        if($total == null){


        }else{

            $inve  =  DB::table('c_inv_inicials')
                ->leftJoin("semillas","c_inv_inicials.id_semilla","=","semillas.id")
                ->leftJoin("calidads","c_inv_inicials.id_calidad","=","calidads.id")
                ->leftJoin("tamanos","c_inv_inicials.id_tamano","=","tamanos.id")

                ->select(
                    "c_inv_inicials.id",
                    "semillas.name as nombre_semillas",
                    "calidads.name as nombre_calidads",
                    "c_inv_inicials.updated_at",

                    "c_inv_inicials.id_tamano","tamanos.name as nombre_tamano",
                    "c_inv_inicials.id_semilla",
                    "c_inv_inicials.id_calidad"
                    ,"c_inv_inicials.totalinicial"
                )
                ->where("c_inv_inicials.id_semilla","=",$request->input('id_semillas'))
                ->where("c_inv_inicials.id_tamano","=",$request->input("id_tamano"))
                ->where("c_inv_inicials.id_calidad","=",$request->input('id_calidad'))->get();
            $inventarioDiario=DB::table("existencia_diarios")
                ->leftJoin("semillas","existencia_diarios.id_semillas","=","semillas.id")
                ->leftJoin("calidads","existencia_diarios.id_calidad","=","calidads.id")
                ->leftJoin("tamanos","existencia_diarios.id_tamano","=","tamanos.id")
                ->select("existencia_diarios.id"
                    ,"existencia_diarios.created_at")
                ->where("existencia_diarios.id","=",$request->id)->get();

            foreach  ($inve as $inventario) {

                foreach ($inventarioDiario as $diario) {
                    $ingresada = $diario->created_at;
                }
                $actual = $inventario->updated_at;
                //if (Carbon::parse($ingresada)->format('Y-m-d') <= (Carbon::parse($actual)->format('Y-m-d'))) {

                    $f = $request->input("onzasF");
                    $e =  $request->input("onzasE");
                    if ($f  ==  null) {

                        $editarBultoEntrega = CInvInicial::findOrFail($inventario->id);
                        $editarBultoEntrega->totalinicial = $request->input("totalfinal");
                        $editarBultoEntrega->pesoinicial = (($request->input("onzasF") * ($request->input("totalfinal") / 50)) / 16);
                        $editarBultoEntrega->updated_at = Carbon::parse($ingresada)->format('Y-m-d');
                        $editarBultoEntrega->onzasI = $request->input("onzasI");

                        $editarBultoEntrega->save();
                    }else{

                        $editarBultoEntrega = CInvInicial::findOrFail($inventario->id);
                        $editarBultoEntrega->totalinicial = $request->input("totalfinal");
                        $editarBultoEntrega->pesoinicial = (($request->input("onzasF") * ($request->input("totalfinal") / 50)) / 16);
                        $editarBultoEntrega->updated_at = Carbon::parse($ingresada)->format('Y-m-d');
                        $editarBultoEntrega->onzasI = $request->input("onzasF");

                        $editarBultoEntrega->save();
                    }
                //}
            }
        }
        if ( $request->input("onzasF") == null){
            $EditarInvDiario = ExistenciaDiario::findOrFail($request->id);
            $ini = $request->input("onzasI");
            $EditarInvDiario->onzasI = $request->input("onzasI");
            $EditarInvDiario->onzasE = $request->input("onzasI");
            $EditarInvDiario->onzasF = $request->input("onzasI");
            $EditarInvDiario->onzasO = $request->input("onzasI");
            $EditarInvDiario->id_semillas = $request->input('id_semillas');
            $EditarInvDiario->id_calidad = $request->input('id_calidad');
            $EditarInvDiario->id_tamano = $request->input("id_tamano");
            $EditarInvDiario->totalinicial = $request->input("totalinicial");
            $EditarInvDiario->pesoinicial = (($request->input("onzasI") * ($request->input("totalinicial") / 50)) / 16);
            $EditarInvDiario->totalentrada = $request->input("totalentrada");
            $EditarInvDiario->pesoentrada = (($request->input("onzasI") * ($request->input("totalentrada") / 50)) / 16);
            $EditarInvDiario->totalfinal = $request->input("totalfinal");
            $EditarInvDiario->pesofinal = (($request->input("onzasI") * ($request->input("totalfinal") / 50)) / 16);

            $tconsumo= ($request->input("totalinicial")
            + $request->input("totalentrada")) - $request->input("totalfinal");

            $EditarInvDiario->totalconsumo = $tconsumo - $request->input('otra');

            $tpconsumo = $EditarInvDiario->pesoinicial
            + $EditarInvDiario->pesoentrada-$EditarInvDiario->pesofinal;

            $EditarInvDiario->pesoconsumo = $tpconsumo - 
            (($request->input("onzasI") * ($request->input("otra") / 50)) / 16);

            $EditarInvDiario->otras = $request->input("otra");
            $EditarInvDiario->pesootras = (($request->input("onzasI") * 
            ($request->input("otra") / 50)) / 16);

            $EditarInvDiario->save();
        }else {
            $EditarInvDiario = ExistenciaDiario::findOrFail($request->id);
            $EditarInvDiario->id_semillas = $request->input('id_semillas');
            $EditarInvDiario->id_calidad = $request->input('id_calidad');
            $EditarInvDiario->id_tamano = $request->input("id_tamano");
            $EditarInvDiario->totalinicial = $request->input("totalinicial");
            $EditarInvDiario->pesoinicial = (($request->input("onzasI") * ($request->input("totalinicial") / 50)) / 16);
            $EditarInvDiario->totalentrada = $request->input("totalentrada");
            $EditarInvDiario->pesoentrada = (($request->input("onzasE") * ($request->input("totalentrada") / 50)) / 16);
            $EditarInvDiario->totalfinal = $request->input("totalfinal");
            $EditarInvDiario->pesofinal = (($request->input("onzasF") * ($request->input("totalfinal") / 50)) / 16);
            
            $tconsumo= ($request->input("totalinicial")
            + $request->input("totalentrada")) - $request->input("totalfinal");

            $EditarInvDiario->totalconsumo = $tconsumo - $request->input('otra');

            $tpconsumo = $EditarInvDiario->pesoinicial
            +$EditarInvDiario->pesoentrada-$EditarInvDiario->pesofinal;

            $EditarInvDiario->pesoconsumo = $tpconsumo - 
            (($request->input("pesootros") * 
            ($request->input("otra") / 50)) / 16);

            $EditarInvDiario->otras = $request->input("otra");
            $EditarInvDiario->pesootras =(($request->input("pesootros") * 
            ($request->input("otra") / 50)) / 16);

            $EditarInvDiario->onzasI = $request->input("onzasI");
            $EditarInvDiario->onzasE = $request->input("onzasE");
            $EditarInvDiario->onzasF = $request->input("onzasF");
            $EditarInvDiario->onzasO = $request->input("pesootros");
            $EditarInvDiario->save();
        }
        return back()->with('crazy', $crazy);
    }catch (ValidationException $exception){
return redirect()->route("ExistenciaDiario")->with('errores','errores')->with('id_capa_entregas',$request->input("id"))->withErrors($exception->errors());
}
        //
    }


    public function update(Request $request, ExistenciaDiario $existenciaDiario)
    {
        //
    }

    public function destroy(Request $request)
    {


        $capaentrega = $request->input('id');
        $borrar = ExistenciaDiario::findOrFail($capaentrega);

        $borrar->delete();
        return back();
    }

    public function destroyall(Request $request)
    {
        $fechita = $request->input('fecha');
        $consulta = ExistenciaDiario::where('created_at', '=', $fechita)
        ->where('totalconsumo', '=', null)->get();

        if($consulta==null){
            DB::table('existencia_diarios')->where('created_at', '=', $fechita)->delete();
            return back();
        }
        else{
            DB::table('existencia_diarios')->where('created_at', '=', $fechita)
        ->where('totalconsumo', '=', null)->delete();

        $inve  =  DB::table('c_inv_inicials')
        ->leftJoin("semillas","c_inv_inicials.id_semilla","=","semillas.id")
        ->leftJoin("calidads","c_inv_inicials.id_calidad","=","calidads.id")
        ->leftJoin("tamanos","c_inv_inicials.id_tamano","=","tamanos.id")

        ->select(
            "c_inv_inicials.id",
           "semillas.name as nombre_semillas",
            "calidads.name as nombre_calidads",
            "c_inv_inicials.id_tamano","tamanos.name as nombre_tamano",
            "c_inv_inicials.id_semilla",
            "c_inv_inicials.id_calidad"
            ,"c_inv_inicials.totalinicial"
            ,"c_inv_inicials.pesoinicial"
            ,"c_inv_inicials.onzasI"
        )->get();

        foreach ($inve as $inventario){
           $consulta = ExistenciaDiario::where('id_semillas', '=', $inventario->id_semilla)->
           where('id_calidad', '=', $inventario->id_calidad)->
           where('id_tamano', '=', $inventario->id_tamano)->
           where('created_at', '=', $fechita)->
           first();
           if($consulta==null){
            $nuevoConsumo = new ExistenciaDiario();
            $nuevoConsumo->id_semillas = $inventario->id_semilla;
            $nuevoConsumo->id_calidad = $inventario->id_calidad;
            $nuevoConsumo->id_tamano = $inventario->id_tamano;
            $nuevoConsumo->totalinicial = $inventario->totalinicial;
            $nuevoConsumo->pesoinicial = $inventario->pesoinicial;
            $nuevoConsumo->onzasI = $inventario->onzasI; 

            $nuevoConsumo->created_at = $fechita;
            $nuevoConsumo->save();
           }else{
            /*$actualizariniciar=ExistenciaDiario::findOrFail($consulta->id);

            $actualizariniciar->totalinicial = $inventario->totalinicial;
            $actualizariniciar->save();*/
           }

        }
        return back();
    }
    }

    public function limpiar(Request $request){
        $fechita = $request->get('fecha');

        $limpiar = DB::table('existencia_diarios')->
        select('existencia_diarios.id')->where('existencia_diarios.totalinicial', '=', 0)
        ->where('existencia_diarios.created_at', '=', $fechita)
        ->whereNull('existencia_diarios.totalentrada')
        ->where('existencia_diarios.totalconsumo', '=',0)
        ->where('existencia_diarios.totalfinal', '=',0)->delete();
        return back();

    }

    public function export(Request $request)
    {

        $fecha = $request->get("fecha1");

        if ($fecha = null)
            $fecha = Carbon::now()->format('Y-m-d');
        else {
            $fecha = Carbon::parse(  $request->get("fecha1"))->format('Y-m-d');

        }
        return (new ExistenciaDiarioExports($fecha))->download('Listado Inventario de Capa'.$fecha.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);

    }

    public function exportpdf(Request $request)
    {
        $fecha = $request->get("fecha1");

        if ($fecha = null)
            $fecha = Carbon::now()->format('Y-m-d');
        else {
            $fecha = Carbon::parse(  $request->get("fecha1"))->format('Y-m-d');

        }
        return (new ExistenciaDiarioExports($fecha))->download('Listado Inventario de Capa'.$fecha.'.pdf', \Maatwebsite\Excel\Excel::DOMPDF);

    }
    public function exportcvs(Request $request)
    {
        $fecha = $request->get("fecha1");

        if ($fecha = null)
            $fecha = Carbon::now()->format('Y-m-d');
        else {
            $fecha = Carbon::parse(  $request->get("fecha1"))->format('Y-m-d');

        }
        return (new ExistenciaDiarioExports($fecha))->download('Listado Inventario de Capa '.$fecha.'.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function diferencias(Request $request){
        $capa_entregas = DB::table('capa_entregas')
        ->join('semillas', 'semillas.id', 'capa_entregas.id_semilla')
        ->join('calidads', 'calidads.id', 'capa_entregas.id_calidad')
        ->selectRaw('calidads.name as calida')
        ->selectRaw('semillas.name as semilla')
        ->selectRaw('SUM(capa_entregas.total) as totalDesp')
    ->where('capa_entregas.created_at', '=', $request->fecha)
    ->groupByRaw('semillas.name, calidads.name')
    ->orderByRaw('semillas.name, calidads.name')
    ->get();

    $other = DB::table('existencia_diarios')
    ->join('semillas', 'semillas.id', 'existencia_diarios.id_semillas')
    ->join('calidads', 'calidads.id', 'existencia_diarios.id_calidad')
    ->selectRaw('calidads.name as calida')
    ->selectRaw('semillas.name as semilla')
    ->selectRaw('SUM(existencia_diarios.totalconsumo) as totalDesp')
    ->where('existencia_diarios.created_at', '=', $request->fecha)
    ->groupByRaw('semillas.name, calidads.name')
    ->orderByRaw('semillas.name, calidads.name')
    ->get();
    $existencia= [];
    #Se buscan las similitudes de semilla y calidad
    foreach($capa_entregas as $c){
        foreach($other as $o){
            if($o->calida == $c->calida && $o->semilla == $c->semilla){
                $existencia[] = ['semilla'=>$o->semilla, 'calida'=>$o->calida,
                'totalDespacho'=>$o->totalDesp, 'totalInventario'=>$c->totalDesp,
            'diferencia'=>$o->totalDesp-$c->totalDesp];
            }
        }
        }
        return response()->json($existencia);
    }
}
