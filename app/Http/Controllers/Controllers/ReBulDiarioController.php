<?php

namespace App\Http\Controllers;

use App\BInvInicial;
use App\BultosDevuelto;
use App\BultosSalida;
use App\Exports\EntregaBultoExport;
use App\Exports\InventarioDiarioBultosExports;
use App\Marca;
use App\ReBulDiario;
use App\Semilla;
use App\Tamano;
use App\Vitola;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReBulDiarioController extends Controller
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

            $fecha = Carbon::now()->format('Y-m-d');
        } else{


                $fecha = $request->get("fecha");

            }
            $inventarioDiario=DB::table("re_bul_diarios")
                ->leftJoin("vitolas","re_bul_diarios.id_vitolas","=","vitolas.id")
                ->leftJoin("marcas","re_bul_diarios.id_marca","=","marcas.id")
                ->select("re_bul_diarios.id",
                    "vitolas.name as nombre_vitolas",
                    "re_bul_diarios.id_vitolas",
                    "re_bul_diarios.id_marca","marcas.name as nombre_marca"
                    ,"re_bul_diarios.totalinicial","re_bul_diarios.pesoinicial"
                    ,"re_bul_diarios.totalentrada","re_bul_diarios.pesoentrada"
                    ,"re_bul_diarios.totalfinal","re_bul_diarios.pesofinal",
                    "re_bul_diarios.totalconsumo","re_bul_diarios.pesoconsumo"
                    ,"re_bul_diarios.onzas")
                ->whereDate("re_bul_diarios.created_at","=" ,$fecha)
                ->orderBy("nombre_marca")
                ->paginate(1000);
            $vitola = Vitola::all();
            $marca = Marca::all();

            if($inventarioDiario->count()>0){

            }

            else {

                $inve = DB::table('b_inv_inicials')
                    ->leftJoin("vitolas", "b_inv_inicials.id_vitolas", "=", "vitolas.id")
                    ->leftJoin("marcas", "b_inv_inicials.id_marca", "=", "marcas.id")
                    ->select(
                        "b_inv_inicials.onzas as nose",
                        "vitolas.name as nombre_vitolas",
                        "marcas.name as nombre_marca",
                        "b_inv_inicials.id_vitolas",
                        "b_inv_inicials.id_marca",
                        "b_inv_inicials.totalinicial",
                    "b_inv_inicials.pesoinicial")
                    ->orderBy("nombre_marca")->paginate(1000);

                foreach ($inve as $inventario) {

                $nuevoConsumo = new ReBulDiario();
                $nuevoConsumo->id_vitolas = $inventario->id_vitolas;
                $nuevoConsumo->id_marca = $inventario->id_marca;
                $nuevoConsumo->totalinicial = $inventario->totalinicial;
                $nuevoConsumo->pesoinicial = $inventario->pesoinicial;
                $nuevoConsumo->onzas = $inventario->nose;
                $nuevoConsumo->created_at = Carbon::parse($fecha)->format('Y-m-d');
                $nuevoConsumo->save();
            }
            }
            $bultodevuleto=DB::table("bultos_devueltos")
                ->leftJoin("vitolas","bultos_devueltos.id_vitolas","=","vitolas.id")
                ->leftJoin("marcas","bultos_devueltos.id_marca","=","marcas.id")
                ->select("bultos_devueltos.id",
                    "vitolas.name as nombre_vitolas",
                    "bultos_devueltos.id_vitolas",
                    "bultos_devueltos.id_marca","marcas.name as nombre_marca"
                    ,"bultos_devueltos.total"
                    ,"bultos_devueltos.usado","bultos_devueltos.libras","bultos_devueltos.onzas")
                ->where("bultos_devueltos.usado","=","0")
                ->whereDate("bultos_devueltos.created_at","="
                    ,Carbon::parse($fecha)->format('Y-m-d'))->get();
            if($bultodevuleto->count()>0){
                foreach ($bultodevuleto as $devuelto){

                    $inventarioDiarios=DB::table("re_bul_diarios")
                        ->leftJoin("vitolas","re_bul_diarios.id_vitolas","=","vitolas.id")
                        ->leftJoin("marcas","re_bul_diarios.id_marca","=","marcas.id")
                        ->select("re_bul_diarios.id",
                            "vitolas.name as nombre_vitolas",
                            "re_bul_diarios.id_vitolas",
                            "re_bul_diarios.id_marca","marcas.name as nombre_marca"
                            ,"re_bul_diarios.totalinicial","re_bul_diarios.pesoinicial"
                            ,"re_bul_diarios.totalentrada","re_bul_diarios.pesoentrada"
                            ,"re_bul_diarios.totalfinal","re_bul_diarios.pesofinal",
                            "re_bul_diarios.totalconsumo","re_bul_diarios.pesoconsumo"
                            ,"re_bul_diarios.onzas")
                        ->where("re_bul_diarios.id_marca","=",$devuelto->id_marca)
                        ->where("re_bul_diarios.id_vitolas","=",$devuelto->id_vitolas)
                        ->whereDate("re_bul_diarios.created_at","=" ,$fecha)
                        ->get();
                    foreach ($inventarioDiarios as $in){
                        DB::table('re_bul_diarios')->where("re_bul_diarios.id","=",$in->id)->decrement('totalinicial',$devuelto->total );
                        /*$EditarInvDiario=ReBulDiario::findOrFail($in->id);
                        $EditarInvDiario->pesoinicial=(( $EditarInvDiario->onzas*$EditarInvDiario->totalinicial)/16);
                        $EditarInvDiario->totalconsumo=($EditarInvDiario->totalinicial+$EditarInvDiario->totalentrada)
                            -$EditarInvDiario->totalfinal;
                        $EditarInvDiario->pesoconsumo=(($EditarInvDiario->onzas*$EditarInvDiario->totalconsumo)/16);


                        $EditarInvDiario->save();*/
                        $bdebuelto = BultosDevuelto::findOrFail($devuelto->id);
                        $bdebuelto->usado = true;
                        $bdebuelto->save();

                    }

            }
            }


            $inventarioDiario=DB::table("re_bul_diarios")
                ->leftJoin("vitolas","re_bul_diarios.id_vitolas","=","vitolas.id")
                ->leftJoin("marcas","re_bul_diarios.id_marca","=","marcas.id")
                ->select("re_bul_diarios.id", "re_bul_diarios.id_marca as marca",
                "re_bul_diarios.id_vitolas as vitola",
                    "vitolas.name as nombre_vitolas",
                    "re_bul_diarios.id_vitolas",
                    "re_bul_diarios.id_marca","marcas.name as nombre_marca"
                    ,"re_bul_diarios.totalinicial","re_bul_diarios.pesoinicial"
                    ,"re_bul_diarios.totalentrada","re_bul_diarios.pesoentrada"
                    ,"re_bul_diarios.totalfinal","re_bul_diarios.pesofinal",
                    "re_bul_diarios.totalconsumo","re_bul_diarios.pesoconsumo"
                    ,"re_bul_diarios.onzas")
                ->where("marcas.name","Like","%".$query."%")
                ->whereDate("re_bul_diarios.created_at","=" ,$fecha)
                ->orderBy("nombre_marca")

                //  ->whereDate("capa_entregas.created_at","=" ,Carbon::now()->format('Y-m-d'))
                ->get();

                $bultoentregados=DB::table("bultos_salidas")
                        ->leftJoin("empleados_bandas","bultos_salidas.id_empleado","=","empleados_bandas.id")
                        ->leftJoin("vitolas","bultos_salidas.id_vitolas","=","vitolas.id")
                        ->leftJoin("marcas","bultos_salidas.id_marca","=","marcas.id")

                        ->select("bultos_salidas.id", "bultos_salidas.id_marca as marca",
                        "bultos_salidas.id_vitolas as vitola",
                            "empleados_bandas.nombre AS nombre_empleado",
                            "vitolas.name as nombre_vitolas",
                            "bultos_salidas.id_empleado",
                            "bultos_salidas.id_vitolas",
                            "bultos_salidas.id_marca","marcas.name as nombre_marca"
                            ,"bultos_salidas.total")
                        ->whereDate("bultos_salidas.created_at","=" ,$fecha)->get();

                        $entradasB = DB::table("entrada_bultos")
                        ->leftJoin("marcas", "entrada_bultos.marca", "=", "marcas.id")
                        ->leftJoin("vitolas", "entrada_bultos.vitola", "=", "vitolas.id")
                        ->select("entrada_bultos.id", "marcas.name AS marcam",
                        "entrada_bultos.id", "vitolas.name AS vitolam",
                            "entrada_bultos.bultos",
                            "entrada_bultos.marca",
                            "entrada_bultos.vitola",
                            "entrada_bultos.libras",
                            "entrada_bultos.peso")
                        ->where("marcas.name", "Like", "%" . $query . "%")
                        ->whereDate("entrada_bultos.created_at", "=", Carbon::parse($fecha)->format('Y-m-d'))
                        ->orderBy("marcas.name")
                        ->paginate(1000);


                foreach($inventarioDiario as $revisarconsumo){
                    foreach($bultoentregados as $revisarenbultosalidas){
                        if($revisarconsumo->marca == $revisarenbultosalidas->marca
                        && $revisarconsumo->vitola == $revisarenbultosalidas->vitola){

                        $editarconsumo=ReBulDiario::findOrFail($revisarconsumo->id);
                        $totalizado= DB::table('bultos_salidas')
                        ->selectRaw("SUM(total) as totalmarca")->where("id_marca", "=", $revisarenbultosalidas->marca)
                        ->where("id_vitolas", "=", $revisarenbultosalidas->vitola)->where("created_at", "=", $fecha)->first();
                        $cantidad= $totalizado->totalmarca !=0 ? $totalizado->totalmarca : 0;
                        $editarconsumo->totalconsumo = $cantidad;
                        $editarconsumo->save();
                        }
                    }

                    foreach($entradasB as $revisarenbultoentradas){
                        if($revisarconsumo->marca == $revisarenbultoentradas->marca
                        && $revisarconsumo->vitola == $revisarenbultoentradas->vitola){
                        $editarconsumo=ReBulDiario::findOrFail($revisarconsumo->id);
                        $editarconsumo->totalentrada = $revisarenbultoentradas->bultos;
                        //$editarCapaEntrega->totalentrada = $reci->total;
                        $editarconsumo->save();
                        }
                    }

                }


                $inventarioDiario=DB::table("re_bul_diarios")
                ->leftJoin("vitolas","re_bul_diarios.id_vitolas","=","vitolas.id")
                ->leftJoin("marcas","re_bul_diarios.id_marca","=","marcas.id")
                ->select("re_bul_diarios.id", "re_bul_diarios.id_marca as marca",
                "re_bul_diarios.id_vitolas as vitola",
                    "vitolas.name as nombre_vitolas",
                    "re_bul_diarios.id_vitolas",
                    "re_bul_diarios.id_marca","marcas.name as nombre_marca"
                    ,"re_bul_diarios.totalinicial","re_bul_diarios.pesoinicial"
                    ,"re_bul_diarios.totalentrada","re_bul_diarios.pesoentrada"
                    ,"re_bul_diarios.totalfinal","re_bul_diarios.pesofinal",
                    "re_bul_diarios.totalconsumo","re_bul_diarios.pesoconsumo"
                    ,"re_bul_diarios.onzas")
                ->where("marcas.name","Like","%".$query."%")
                ->whereDate("re_bul_diarios.created_at","=" ,$fecha)
                ->orderBy("nombre_marca")
                ->paginate(1000);


            $vitola = Vitola::all();
            $marca = Marca::all();
            return view("InventariosDiarios.ReBulDiario")
                ->withNoPagina(1)
                ->with('fecha', $fecha)
                ->withInvDiario($inventarioDiario)
                ->withVitola($vitola)
                ->withMarca($marca);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $fecha = $request->get("fechas");
        $fecha1 = Carbon::parse($fecha)->format('Y-m-d');

        $inve = DB::table('b_inv_inicials')
            ->leftJoin("vitolas", "b_inv_inicials.id_vitolas", "=", "vitolas.id")
            ->leftJoin("marcas", "b_inv_inicials.id_marca", "=", "marcas.id")
            ->select(
                "vitolas.name as nombre_vitolas",
                "marcas.name as nombre_marca",
                "b_inv_inicials.id",
                "b_inv_inicials.updated_at",
                "b_inv_inicials.id_vitolas",
                "b_inv_inicials.updated_at",
                "b_inv_inicials.id_marca",
                "b_inv_inicials.totalinicial")
            ->where("b_inv_inicials.id_vitolas","=",$request->input('id_vitolas'))
            ->where("b_inv_inicials.id_marca","=",$request->input("id_marca"))->get();
        if($inve->count()>0) {



            $inventarioDiario=DB::table("re_bul_diarios")
                ->leftJoin("vitolas","re_bul_diarios.id_vitolas","=","vitolas.id")
                ->leftJoin("marcas","re_bul_diarios.id_marca","=","marcas.id")
                ->select("re_bul_diarios.id",
                    "re_bul_diarios.created_at")
                ->where("re_bul_diarios.id","=",$request->id)->get();


            foreach  ($inve as $inventario) {

                foreach ($inventarioDiario as $diario) {
                    $ingresada = $diario->created_at;
                }
                $actual = $inventario->updated_at;

                if (Carbon::parse($ingresada)->format('Y-m-d') >= (Carbon::parse($actual)->format('Y-m-d'))) {



                    $editarBultoEntrega = BInvInicial::findOrFail($inventario->id);
                    $editarBultoEntrega->totalinicial = ($request->input("totalinicial")+$request->input("totalentrada"))-$request->input("totalfinal");
                    $finalI=(($request->input("onzas")*$request->input("totalinicial"))/16)
                    +(($request->input("onzas")*$request->input("totalentrada"))/16)
                    -(($request->input("onzas")*$request->input("totalfinal"))/16);
                    $editarBultoEntrega->pesoinicial=$finalI;
                    $editarBultoEntrega->updated_at = Carbon::parse($ingresada)->format('Y-m-d');

                    $editarBultoEntrega->save();
                }
            }



    }else{
            $nuevoConsumo = new BInvInicial();
            $nuevoConsumo->id_vitolas=$request->input('id_vitolas');
            $nuevoConsumo->id_marca=$request->input("id_marca");
            $nuevoConsumo->totalinicial = ($request->input("totalinicial")+$request->input("totalentrada"))-$request->input("totalfinal");
            $finalI=(($request->input("onzas")*$request->input("totalinicial"))/16)
            +(($request->input("onzas")*$request->input("totalentrada"))/16)
            -(($request->input("onzas")*$request->input("totalfinal"))/16);
            $nuevoConsumo->pesoinicial=$finalI;
            $nuevoConsumo->onzas=$request->input("onzas");
            //$nuevoConsumo->pesoinicial=($request->input("pesoinicial")+$request->input("pesoentrada"))-$request->input("pesofinal");
            $nuevoConsumo->updated_at =$fecha1;
            $nuevoConsumo->created_at =$fecha1;
            $nuevoConsumo->save();
}

//el input total inicial es l consumo y asi consiguiente el peso

        $nuevoInvDiario = new ReBulDiario();
        $nuevoInvDiario->onzas=$request->input("onzas");
        $nuevoInvDiario->id_vitolas=$request->input('id_vitolas');
        $nuevoInvDiario->id_marca=$request->input("id_marca");
        $nuevoInvDiario->totalinicial=$request->input("totalinicial");
        $nuevoInvDiario->pesoinicial=(($request->input("onzas")*$request->input("totalinicial"))/16);
        $nuevoInvDiario->totalentrada=$request->input("totalentrada");
        $nuevoInvDiario->pesoentrada=(($request->input("onzas")*$request->input("totalentrada"))/16);
        $nuevoInvDiario->totalconsumo=$request->input("totalfinal");
        $nuevoInvDiario->pesoconsumo=(($request->input("onzas")*$request->input("totalfinal"))/16);
        $nuevoInvDiario->totalfinal=($request->input("totalinicial")+$request->input("totalentrada"))-($request->input("totalfinal"));
        $nuevoInvDiario->pesofinal =$finalI;
        //(($request->input("onzas")* $nuevoInvDiario->totalfinal)/16);
        $nuevoInvDiario->created_at =$fecha1;




        $nuevoInvDiario->save();
        return back();
        //return redirect()->route("InventarioDiario")->withExito("Se creó la entrega Correctamente ");

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ReBulDiario  $reBulDiario
     * @return \Illuminate\Http\Response
     */
    public function show(ReBulDiario $reBulDiario)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ReBulDiario  $reBulDiario
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {

        try{
            $this->validate($request, [

                'id_marca'=>'required',

            ]);

              $total = ($request->input("totalfinal"));
    if($total = null){


    }else{
        $inve = DB::table('b_inv_inicials')
            ->leftJoin("vitolas", "b_inv_inicials.id_vitolas", "=", "vitolas.id")
            ->leftJoin("marcas", "b_inv_inicials.id_marca", "=", "marcas.id")
            ->select(
                "vitolas.name as nombre_vitolas",
                "marcas.name as nombre_marca",
                "b_inv_inicials.id",
                "b_inv_inicials.updated_at",
                "b_inv_inicials.id_vitolas",
                "b_inv_inicials.id_marca",
                "b_inv_inicials.totalinicial",
                "b_inv_inicials.pesoinicial")
            ->where("b_inv_inicials.id_vitolas","=",$request->input('id_vitolas'))
            ->where("b_inv_inicials.id_marca","=",$request->input("id_marca"))->get();



        $inventarioDiario=DB::table("re_bul_diarios")
            ->leftJoin("vitolas","re_bul_diarios.id_vitolas","=","vitolas.id")
            ->leftJoin("marcas","re_bul_diarios.id_marca","=","marcas.id")
            ->select("re_bul_diarios.id",
                "re_bul_diarios.created_at")
            ->where("re_bul_diarios.id","=",$request->id)->get();


        foreach  ($inve as $inventario) {

            foreach ($inventarioDiario as $diario) {
                $ingresada = $diario->created_at;
            }
            $actual = $inventario->updated_at;

            //if (Carbon::parse($ingresada)->format('Y-m-d') >= (Carbon::parse($actual)->format('Y-m-d'))) {



                $editarBultoEntrega = BInvInicial::findOrFail($inventario->id);
                $editarBultoEntrega->totalinicial = ($request->input("totalinicial")+$request->input("totalentrada"))-$request->input("totalfinal");
                    $finalI=(($request->input("onzas")*$request->input("totalinicial"))/16)
                    +(($request->input("onzas")*$request->input("totalentrada"))/16)
                    -(($request->input("onzas")*$request->input("totalfinal"))/16);
                    $editarBultoEntrega->pesoinicial=$finalI;
                    $editarBultoEntrega->onzas= $request->input("onzas");
                $editarBultoEntrega->updated_at = Carbon::parse($ingresada)->format('Y-m-d');

                $editarBultoEntrega->save();
        //}
        }

    }
            $EditarInvDiario=ReBulDiario::findOrFail($request->id);
            $EditarInvDiario->onzas=$request->input("onzas");
            $EditarInvDiario->id_vitolas=$request->input('id_vitolas');
            $EditarInvDiario->id_marca=$request->input("id_marca");
            $EditarInvDiario->totalinicial=$request->input("totalinicial");
            $EditarInvDiario->pesoinicial=(($request->input("onzas")*$request->input("totalinicial"))/16);
            $EditarInvDiario->totalentrada=$request->input("totalentrada");
            $EditarInvDiario->pesoentrada=(($request->input("onzas")*$request->input("totalentrada"))/16);
            $EditarInvDiario->totalconsumo=$request->input("totalfinal");
            //$EditarInvDiario->totalconsumo=($request->input("totalinicial")+$request->input("totalentrada"))-$request->input("totalfinal");
            $EditarInvDiario->pesoconsumo=(($request->input("onzas")*$EditarInvDiario->totalconsumo)/16);
            //$EditarInvDiario->totalfinal=$request->input("totalfinal");
            $EditarInvDiario->totalfinal=($request->input("totalinicial")+$request->input("totalentrada"))-$request->input("totalfinal");
            $EditarInvDiario->pesofinal=(($request->input("onzas")*$EditarInvDiario->totalfinal)/16);
            //dice total final pero es el consumo


            $EditarInvDiario->save();
            return back()->withExito("Se edito Correctamente ");
            //return redirect()->route("InventarioDiario")->withExito("Se editó Correctamente");

        }catch (ValidationException $exception){
           // return redirect()->route("InventarioDiario")->with('errores','errores')->with('id_capa_entregas',$request->input("id"))->withErrors($exception->errors());
           return back();
        }
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ReBulDiario  $reBulDiario
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ReBulDiario $reBulDiario)
    {


        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ReBulDiario  $reBulDiario
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {


        $capaentrega = $request->input('id');
        $borrar = ReBulDiario::findOrFail($capaentrega);

        $borrar->delete();
       // return redirect()->route("InventarioDiario")->withExito("Se borró la entrega satisfactoriamente");
       return back();
    }

    public function destroyall(Request $request)
    {
        $fechita = $request->input('fecha');

        $validacion = DB::table('re_bul_diarios')->select('re_bul_diarios.id')
        ->where('re_bul_diarios.created_at', '=', $fechita)
        ->whereNull('re_bul_diarios.totalfinal')->delete();

        $inve  =  DB::table('b_inv_inicials')
        ->leftJoin("marcas","b_inv_inicials.id_marca","=","marcas.id")
        ->leftJoin("vitolas","b_inv_inicials.id_vitolas","=","vitolas.id")

        ->select(
            "b_inv_inicials.id",
            "marcas.name as marca",
            "b_inv_inicials.id_marca",
            "b_inv_inicials.id_vitolas",
            "b_inv_inicials.totalinicial",
            "b_inv_inicials.pesoinicial",
            "b_inv_inicials.onzas"

        )
        ->whereNotExists(function ($query) use($fechita){
            $query->select(DB::raw(1))->from('re_bul_diarios')->
            whereRaw('b_inv_inicials.id_marca= re_bul_diarios.id_marca')
            ->whereRaw('b_inv_inicials.id_vitolas= re_bul_diarios.id_vitolas')
            ->where('re_bul_diarios.created_at', '=', $fechita);
        })
        ->get();

        foreach ($inve as $inventario){
            $nuevoConsumo = new ReBulDiario();
            $nuevoConsumo->id_marca = $inventario->id_marca;
            $nuevoConsumo->id_vitolas = $inventario->id_vitolas;
            $nuevoConsumo->totalinicial = $inventario->totalinicial;
            $nuevoConsumo->pesoinicial = $inventario->pesoinicial;
            $nuevoConsumo->onzas = $inventario->onzas;
            $nuevoConsumo->created_at = $fechita;
            $nuevoConsumo->save();

        }
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
        return (new InventarioDiarioBultosExports($fecha))->download('Inventario Diario de Entrega Bultos'.$fecha.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);

    }

    public function exportpdf(Request $request)
    {
        $fecha = $request->get("fecha1");

        if ($fecha = null)
            $fecha = Carbon::now()->format('Y-m-d');
        else {
            $fecha = Carbon::parse(  $request->get("fecha1"))->format('Y-m-d');

        }
        return (new InventarioDiarioBultosExports($fecha))->download('Inventario Diario De Entrega Bultos '.$fecha.'.pdf', \Maatwebsite\Excel\Excel::DOMPDF);

    }
    public function exportcvs(Request $request)
    {
        $fecha = $request->get("fecha1");

        if ($fecha = null)
            $fecha = Carbon::now()->format('Y-m-d');
        else {
            $fecha = Carbon::parse(  $request->get("fecha1"))->format('Y-m-d');

        }
        return (new InventarioDiarioBultosExports($fecha))->download('Inventario Diario de Entrega Bultos'.$fecha.'.csv', \Maatwebsite\Excel\Excel::CSV);
    }


        public function calcular(Request $request){
            $fecha = $request->input('fecha');
            $inventarioDiario=DB::table("re_bul_diarios")
            ->leftJoin("vitolas","re_bul_diarios.id_vitolas","=","vitolas.id")
            ->leftJoin("marcas","re_bul_diarios.id_marca","=","marcas.id")
            ->select("re_bul_diarios.id", "re_bul_diarios.id_marca as marca",
            "re_bul_diarios.id_vitolas as vitola",
                "vitolas.name as nombre_vitolas",
                "re_bul_diarios.id_vitolas",
                "re_bul_diarios.id_marca","marcas.name as nombre_marca"
                ,"re_bul_diarios.totalinicial","re_bul_diarios.pesoinicial"
                ,"re_bul_diarios.totalentrada","re_bul_diarios.pesoentrada"
                ,"re_bul_diarios.totalfinal","re_bul_diarios.pesofinal",
                "re_bul_diarios.totalconsumo","re_bul_diarios.pesoconsumo"
                ,"re_bul_diarios.onzas")
            ->whereDate("re_bul_diarios.created_at","=" ,$fecha)
            ->orderBy("nombre_marca")
            ->get();
            foreach($inventarioDiario as $inve){

                $getid = BInvInicial::where('id_marca', $inve->id_marca)->where('id_vitolas', $inve->id_vitolas)
            ->select('id')->first();
            if($getid!=null){
                $onzasmul= $inve->onzas;
                $finalI=(($onzasmul*$inve->totalinicial)/16)
                +(($onzasmul*$inve->totalentrada)/16)
                -(($onzasmul*$inve->totalconsumo)/16);
                $editarBultoEntrega = BInvInicial::findOrFail($getid->id);
                $editarBultoEntrega->totalinicial = $inve->totalinicial + $inve->totalentrada - $inve->totalconsumo ;
                    $editarBultoEntrega->pesoinicial=$finalI;
                $editarBultoEntrega->updated_at = Carbon::parse($fecha)->format('Y-m-d');

                $editarBultoEntrega->save();
            }
        }
        foreach($inventarioDiario as $inve){

            $EditarInvDiario=ReBulDiario::findOrFail($inve->id);
            $EditarInvDiario->onzas=$inve->onzas;
            //$EditarInvDiario->totalinicial=$inve->totalinicial;
            $EditarInvDiario->pesoinicial=(($inve->onzas*$inve->totalinicial)/16);
            //$EditarInvDiario->totalentrada=$inve->totalentrada;
            $EditarInvDiario->pesoentrada=(($inve->onzas*$inve->totalentrada)/16);
            //$EditarInvDiario->totalconsumo=$inve->totalconsumo;
            $EditarInvDiario->pesoconsumo=(($inve->onzas*$inve->totalconsumo)/16);
            $EditarInvDiario->totalfinal=$inve->totalinicial + $inve->totalentrada - $inve->totalconsumo ;
            $EditarInvDiario->pesofinal=(($inve->onzas*$EditarInvDiario->totalfinal)/16);
            $EditarInvDiario->save();
        }

            return back();

    }
}
