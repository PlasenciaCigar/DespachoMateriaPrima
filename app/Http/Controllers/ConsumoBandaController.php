<?php

namespace App\Http\Controllers;

use App\BandaInvInicial;
use App\ConsumoBanda;
use App\Exports\ConsumoBandaExport;
use App\Exports\ConsumoBandarExport;
use App\Marca;
use App\Procedencia;
use App\Semilla;
use App\Tamano;
use App\Variedad;
use App\Vitola;
use Carbon\Carbon;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsumoBandaController extends Controller
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
            if ($fecha == null){
                $fecha = Carbon::now()->format('Y-m-d');
        }else{
                $fecha = $request->get("fecha");

            }
            $sem = $request->semilla;

            $consumobanda=DB::table("consumo_bandas")
                ->leftJoin("vitolas","consumo_bandas.id_vitolas","=","vitolas.id")
                ->leftJoin("marcas","consumo_bandas.id_marca","=","marcas.id")
                ->leftJoin("tamanos","consumo_bandas.id_tamano","=","tamanos.id")
                ->leftJoin("semillas","consumo_bandas.id_semillas","=","semillas.id")
                ->leftJoin("variedads", "consumo_bandas.variedad", "=", "variedads.id")
                ->leftJoin("procedencias", "consumo_bandas.procedencia", "=", "procedencias.id")
                ->select("consumo_bandas.id",
                    "vitolas.name as nombre_vitolas",
                    "semillas.name as nombre_semillas",
                    "variedads.name as nombre_variedad",
                    "procedencias.name as nombre_procedencia",
                    "consumo_bandas.id_tamano",
                    "tamanos.name as nombre_tamano",
                    "consumo_bandas.id_vitolas",
                    "consumo_bandas.id_semillas",
                    "consumo_bandas.id_marca","marcas.name as nombre_marca"
                    ,"consumo_bandas.total"
                    ,"consumo_bandas.onzas"
                    ,"consumo_bandas.libras"
                , "consumo_bandas.variedad" ,"consumo_bandas.procedencia")
                ->where("marcas.name","Like","%".$query."%")
                ->where("semillas.name","Like","%".$sem."%")
                ->whereDate("consumo_bandas.created_at","=" ,Carbon::parse($fecha)->format('Y-m-d'))
                ->orderBy("nombre_marca")
                ->paginate(1000);
            $vitola = Vitola::all();
            $marca = Marca::all();
            $tamano = Tamano::all();
            $semilla = Semilla::all();
        $variedad = Variedad::all();
        $procedencia =Procedencia::all();

        $entregaCapass=DB::table("consumo_bandas")
            ->leftJoin("vitolas","consumo_bandas.id_vitolas","=","vitolas.id")
            ->leftJoin("marcas","consumo_bandas.id_marca","=","marcas.id")
            ->leftJoin("tamanos","consumo_bandas.id_tamano","=","tamanos.id")
            ->leftJoin("semillas","consumo_bandas.id_semillas","=","semillas.id")
            ->leftJoin("variedads", "consumo_bandas.variedad", "=", "variedads.id")
            ->leftJoin("procedencias", "consumo_bandas.procedencia", "=", "procedencias.id")
            ->selectRaw("SUM(total) as total_capa")
            ->where("marcas.name","Like","%".$query."%")
            ->where("semillas.name","Like","%".$sem."%")
            ->whereDate("consumo_bandas.created_at","=" ,Carbon::parse($fecha)->format('Y-m-d'))
            ->get();

            return view("ConsumoBanda.ConsumoBanda")
                ->withNoPagina(1)
                ->withConsumoBanda($consumobanda)
                ->withTamano($tamano)
                ->withVitola($vitola)
                ->withTotal($entregaCapass)
                ->with('fecha', $fecha)
                ->withSemilla($semilla)
                ->withMarca($marca)
        ->withVariedad($variedad)
        ->withProcedencia($procedencia);

    }
        //


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

        $inve  =  DB::table('banda_inv_inicials')
            ->leftJoin("semillas","banda_inv_inicials.id_semilla","=","semillas.id")
            ->leftJoin("tamanos","banda_inv_inicials.id_tamano","=","tamanos.id")
            ->leftJoin("variedads", "banda_inv_inicials.id_variedad", "=", "variedads.id")
            ->leftJoin("procedencias", "banda_inv_inicials.id_procedencia", "=", "procedencias.id")

            ->select(
                "banda_inv_inicials.id")
            ->where("banda_inv_inicials.id_semilla","=",$request->input("id_semillas"))
            ->where("banda_inv_inicials.id_variedad","=",$request->input("id_variedad"))
            ->where("banda_inv_inicials.id_procedencia","=",$request->input("id_procedencia"))

            ->where("banda_inv_inicials.id_tamano","=",$request->input("id_tamano"))->get();
        if($inve->count()>0){
        }else{
            $nuevoConsumo = new BandaInvInicial();
            $nuevoConsumo->id_semilla=$request->input('id_semillas');
            $nuevoConsumo->id_tamano=$request->input("id_tamano");
            $nuevoConsumo->totalinicial= '0';
            $nuevoConsumo->id_variedad= $request->input("id_variedad");
            $nuevoConsumo->id_procedencia= $request->input("id_procedencia");
            $nuevoConsumo->save();
        }
        try{
            $this->validate($request, [
                'id_vitolas'=>'required',
                'id_marca'=>'required',
                'id_semillas'=>'required',
                'id_tamano'=>'required|integer',
                'onzas'=>'required',
                'total'=>'required'
            ]);
        $fecha = $request->get("fecha");
        $fecha1 = Carbon::parse($fecha)->format('Y-m-d');

            $fechaa =$request->input('fecha');
            if ($fechaa == null)
                $fechaa = Carbon::now()->format('Y-m-d');
            else{
                $fechaa = $request->get("fecha");

            }

        $nuevoConsumoBanda = new ConsumoBanda();
        $nuevoConsumoBanda->id_vitolas=$request->input('id_vitolas');
        $nuevoConsumoBanda->id_semillas=$request->input('id_semillas');
        $nuevoConsumoBanda->id_marca=$request->input("id_marca");
        $nuevoConsumoBanda->id_tamano=$request->input("id_tamano");
        $nuevoConsumoBanda->total=$request->input('total');
        $nuevoConsumoBanda->onzas=$request->input('onzas');
        $nuevoConsumoBanda->created_at=$fechaa;
        $nuevoConsumoBanda->libras=  ($request->input("total")/100 * $request->input('onzas')/16);
            $nuevoConsumoBanda->variedad=$request->input('id_variedad');
            $nuevoConsumoBanda->procedencia=$request->input('id_procedencia');

            $nuevoConsumoBanda->save();
        return back()->withExito("Se creó la entrega Correctamente ");
        }catch (ValidationException $exception){
            return redirect()->route("ConsumoBanda")->with('errores','errores')->with('id_capa_entregas',$request->input("id"))->withErrors($exception->errors());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ConsumoBanda  $consumoBanda
     * @return \Illuminate\Http\Response
     */
    public function show(ConsumoBanda $consumoBanda)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ConsumoBanda  $consumoBanda
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $inve  =  DB::table('banda_inv_inicials')
            ->leftJoin("semillas","banda_inv_inicials.id_semilla","=","semillas.id")
            ->leftJoin("tamanos","banda_inv_inicials.id_tamano","=","tamanos.id")
            ->leftJoin("variedads", "banda_inv_inicials.id_variedad", "=", "variedads.id")
            ->leftJoin("procedencias", "banda_inv_inicials.id_procedencia", "=", "procedencias.id")

            ->select(
                "banda_inv_inicials.id")
            ->where("banda_inv_inicials.id_semilla","=",$request->input("id_semillas"))
            ->where("banda_inv_inicials.id_variedad","=",$request->input("id_variedad"))
            ->where("banda_inv_inicials.id_procedencia","=",$request->input("id_procedencia"))
            ->where("banda_inv_inicials.id_tamano","=",$request->input("id_tamano"))->get();
        if($inve->count()>0){
        }else{
            $nuevoConsumo = new BandaInvInicial();
            $nuevoConsumo->id_semilla=$request->input('id_semillas');
            $nuevoConsumo->id_tamano=$request->input("id_tamano");
            $nuevoConsumo->totalinicial= '0';
            $nuevoConsumo->id_variedad= $request->input("id_variedad");
            $nuevoConsumo->id_procedencia= $request->input("id_procedencia");

            $nuevoConsumo->save();
        }
        try{
            $this->validate($request, [
                'id_vitolas'=>'required',
                'id_marca'=>'required',
                'id_semillas'=>'required',
                'id_tamano'=>'required|integer',
                'onzas'=>'required',
                'total'=>'required'
            ]);
            $editarConsumoBanda=ConsumoBanda::findOrFail($request->id);
            $editarConsumoBanda->id_vitolas=$request->input('id_vitolas');
            $editarConsumoBanda->id_semillas=$request->input('id_semillas');
            $editarConsumoBanda->id_marca=$request->input("id_marca");
            $editarConsumoBanda->id_tamano=$request->input("id_tamano");
            $editarConsumoBanda->total=$request->input('total');
            $editarConsumoBanda->onzas=$request->input('onzas');
            $editarConsumoBanda->libras=  ($request->input("total")/100 * $request->input('onzas')/16);

            $editarConsumoBanda->variedad=$request->input('id_variedad');
            $editarConsumoBanda->procedencia=$request->input('id_procedencia');

            $editarConsumoBanda->save();
            return back()->withExito("Se edito Correctamente ");

        }catch (ValidationException $exception){
            return redirect()->route("ConsumoBanda")->with('errores','errores')->with('id_capa_entregas',$request->input("id"))->withErrors($exception->errors());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ConsumoBanda  $consumoBanda
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ConsumoBanda $consumoBanda)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ConsumoBanda  $consumoBanda
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {

        $capaentrega = $request->input('id');
        $borrar = ConsumoBanda::findOrFail($capaentrega);
        $borrar->delete();
        return back()->withExito("Se elimino Correctamente ");
    }
    public function export(Request $request)
    {

        $fecha = $request->get("fecha1");

        if ($fecha = null)
            $fecha = Carbon::now()->format('Y-m-d');
        else {
            $fecha = Carbon::parse(  $request->get("fecha1"))->format('Y-m-d');

        }
        return (new ConsumoBandaExport($fecha))->download('Listado Consumo De Banda '.$fecha.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);

    }

    public function exportbandas(Request $request)
    {
        $fecha = Carbon::parse($request->fecha1)->format('Y-m-d');
        return (new ConsumoBandarExport($fecha))
        ->download('Listado Consumo De Banda '.$fecha.'.xlsx',
        \Maatwebsite\Excel\Excel::XLSX);

    }

    public function exportpdf(Request $request)
    {
        $fecha = $request->get("fecha1");

        if ($fecha = null)
            $fecha = Carbon::now()->format('Y-m-d');
        else {
            $fecha = Carbon::parse(  $request->get("fecha1"))->format('Y-m-d');

        }
        return (new ConsumoBandaExport($fecha))->download('Listado Consumo De Banda '.$fecha.'.pdf', \Maatwebsite\Excel\Excel::DOMPDF);

    }
    public function exportcvs(Request $request)
    {
        $fecha = $request->get("fecha1");

        if ($fecha = null)
            $fecha = Carbon::now()->format('Y-m-d');
        else {
            $fecha = Carbon::parse(  $request->get("fecha1"))->format('Y-m-d');

        }
        return (new ConsumoBandaExport($fecha))->download('Listado Consumo De Banda '.$fecha.'.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function Suma100(Request $request){


        $capaentrega = $request->get('id');

        DB::table('consumo_bandas')->where("consumo_bandas.id","=",$capaentrega)->increment('total', 100);

        return back()->withExito("Se incremento Correctamente ");

    }
    public function Sumas(Request $request){

        $incremeto =  $request->get('suma');
        $capaentrega = $request->get('id');

        DB::table('consumo_bandas')->where("consumo_bandas.id","=",$capaentrega)
            ->increment('total', $incremeto);
            return back()->withExito("Se aumento Correctamente ");
    }

    public function calcular(Request $request){
        $fecha = $request->input("fecha");

        $inventariobanda = DB::table('inventario_bandas')
        ->leftJoin('semillas', 'semillas.id', '=', 'inventario_bandas.id_semillas')
        ->leftjoin('tamanos', 'tamanos.id', '=', 'inventario_bandas.id_tamano')
        ->leftjoin('variedads', 'variedads.id', '=', 'inventario_bandas.id_variedad')
        ->leftjoin('procedencias', 'procedencias.id', '=', 'inventario_bandas.id_procedencia')
        ->select('inventario_bandas.id_semillas as sem', 'inventario_bandas.id_tamano as tam',
         'inventario_bandas.id_variedad as var', 'inventario_bandas.id_procedencia as pro', 'inventario_bandas.pesoconsumo as pes')
         ->where('inventario_bandas.created_at', '=', $fecha)->get();

         foreach($inventariobanda as $inventario){

            $totalbanda = ConsumoBanda::where('id_semillas', $inventario->sem)->where('id_tamano', $inventario->tam)
            ->where('variedad', $inventario->var)->where('procedencia', $inventario->pro)
            ->where('created_at', $fecha)->sum('total');

            $ddd = ConsumoBanda::where('id_semillas', $inventario->sem)->where('id_tamano', $inventario->tam)
            ->where('variedad', $inventario->var)->where('procedencia', $inventario->pro)
            ->where('created_at', $fecha)->select('id')->first();

            $ddd= DB::table('consumo_bandas')
            ->select('id as other', 'total')
            ->where('id_semillas', '=', $inventario->sem)
            ->where('variedad', '=', $inventario->var)
            ->where('id_tamano', '=', $inventario->tam)
            ->where('procedencia', '=', $inventario->pro)
            ->where('created_at', '=', $fecha)->get();

            foreach($ddd as $dddd){
            $editarConsumoBanda=ConsumoBanda::findOrFail($dddd->other);
            $prom=  $inventario->pes/($totalbanda/100);
            $editarConsumoBanda->libras=  ($dddd->total/100)*$prom;
            $editarConsumoBanda->save();
            }

        }
        return back();

    }

    function comparativo(Request $request){
        $consumo = DB::table('consumo_bandas')
        ->select(DB::raw('concat(id_semillas, id_tamano, procedencia, variedad) as conca'),
        DB::raw('concat(semillas.name, " ", variedads.name, tamanos.name, procedencias.name) as name_conca'),
         DB::raw('sum(total) as cantidad'))
         ->join('semillas', 'semillas.id', 'consumo_bandas.id_semillas')
         ->join('tamanos', 'tamanos.id', 'consumo_bandas.id_tamano')
         ->join('procedencias', 'procedencias.id', 'consumo_bandas.procedencia')
         ->join('variedads', 'variedads.id', 'consumo_bandas.variedad')
         ->where('consumo_bandas.created_at', 'LIKE', '%'.$request->fecha.'%')
        ->groupByRaw('id_semillas, id_tamano, procedencia, variedad')->get();

        $inventario = DB::table('inventario_bandas')->select(DB::raw('sum(totalconsumo) as cantidad'),
        DB::raw('concat(id_semillas, id_tamano, id_procedencia, id_variedad) as conca'),
        DB::raw('concat(semillas.name, " ", variedads.name, tamanos.name, procedencias.name) as name_conca'))
        ->join('semillas', 'semillas.id', 'inventario_bandas.id_semillas')
        ->join('tamanos', 'tamanos.id', 'inventario_bandas.id_tamano')
        ->join('procedencias', 'procedencias.id', 'inventario_bandas.id_procedencia')
        ->join('variedads', 'variedads.id', 'inventario_bandas.id_variedad')
        ->where('inventario_bandas.created_at', 'LIKE', '%'.$request->fecha.'%')
        ->groupByRaw('id_semillas, id_tamano, id_procedencia, id_variedad')
        ->get();
       
        foreach($consumo as $o){
            $arreglo = array_column($inventario->toArray(), 'conca');
            $res = array_search($o->conca, $arreglo);
            if($res!==false){
                $existencia[] = ['nombre'=>$o->name_conca,
                'inv'=>$inventario[$res]->cantidad, 'con'=>$o->cantidad,
                'diferencia'=>$o->cantidad-$inventario[$res]->cantidad];
            }else{
                $existencia[] = ['nombre'=>$o->name_conca,
                'inv'=>0, 'con'=>$o->cantidad,
                'diferencia'=>$o->cantidad];
            }
        }
        foreach($inventario as $o){
            $res = array_search($o->conca, array_column($consumo->toArray(), 'conca'));
            if($res!==false){
                
            }else{
                $existencia[] = ['nombre'=>$o->name_conca,
                'inv'=>$o->cantidad, 'con'=>0,
                'diferencia'=>$o->cantidad];
            }
        }
        return response()->json($existencia);

    }
}
