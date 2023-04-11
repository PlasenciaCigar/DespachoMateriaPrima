<?php

namespace App\Http\Controllers;

use App\BandaInvInicial;
use App\Calidad;
use App\CInvInicial;
use App\EntradaBanda;
use App\Exports\EntradaBandaExport;
use App\Exports\EntradaBultosExport;
use App\Exports\RecepcionCapaExport;
use App\Procedencia;
use App\RecibirCapa;
use App\Semilla;
use App\Tamano;
use App\Marca;
use App\Vitola;
use App\Variedad;
use App\BInvInicial;
use Carbon\Carbon;
use Dotenv\Exception\ValidationException;
use App\EntradaBultos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;

class EntradaBultosController extends Controller
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

            if ($fecha == null){


                $fecha = Carbon::now()->format('Y-m-d');
            }
            else {
                $fecha = $request->get("fecha");

            }
            $recibirCapa = DB::table("entrada_bultos")
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
            $tamano = Vitola::all();
            $semillas = Marca::all();
            $variedad = Marca::all();
            $procedencia =Procedencia::all();

            $entregaCapass=DB::table("entrada_bultos")
                ->leftJoin("marcas", "entrada_bultos.marca", "=", "marcas.id")
                ->leftJoin("vitolas", "entrada_bultos.vitola", "=", "vitolas.id")
                ->selectRaw("SUM(bultos) as total_capa")
                ->where("marcas.name","Like","%".$query."%")
                ->whereDate("entrada_bultos.created_at","=" ,Carbon::parse($fecha)->format('Y-m-d'))
                ->get();

            return view("BulltosEntradas.EntradaBultos")
                ->withNoPagina(1)
                ->with('fecha', $fecha)
                ->withRecibirCapa($recibirCapa)
                ->withTamano($tamano)
                ->withSemilla($semillas)
                ->withTotal($entregaCapass)
                ->withVariedad($variedad)
                ->withProcedencia($procedencia);

        }
    }

    function validar($vitolas, $marcas, $fecha){
        $entrada =DB::table('entrada_bultos')
        ->where('vitola', '=', $vitolas)
        ->where('marca', '=', $marcas)
        ->where('created_at', '=', $fecha)->first();

        return $entrada==null?true:false;
    }


    public function store(Request $request)
    {
        if($this->validar( $request->input('id_vitolas'), $request->input('id_marca'),
        $request->input('fecha'))){
            $inve  =  DB::table('b_inv_inicials')
            ->leftJoin("vitolas","b_inv_inicials.id_vitolas","=","vitolas.id")
            ->leftJoin("marcas","b_inv_inicials.id_marca","=","marcas.id")
            ->select(
                "vitolas.name as nombre_vitolas",
                "marcas.name as nombre_marca")
            ->where("b_inv_inicials.id_marca","=",$request->input("id_marca"))
            ->where("b_inv_inicials.id_vitolas","=",$request->input("id_vitolas"));
        if($inve->count()>0){

        }else{
            $nuevoConsumo = new BInvInicial();
            $nuevoConsumo->id_vitolas=$request->input('id_vitolas');
            $nuevoConsumo->id_marca=$request->input("id_marca");
            $nuevoConsumo->totalinicial= '0';
            $nuevoConsumo->save();
        }

        $fechaa =$request->input('fecha');
        if ($fechaa == null)
            $fechaa = Carbon::now()->format('Y-m-d');
        else{
            $fechaa = $request->get("fecha");

        }
        $nuevoCapaEntra = new EntradaBultos();

        $nuevoCapaEntra->vitola=$request->input('id_vitolas');
        $nuevoCapaEntra->marca=$request->input("id_marca");
        $nuevoCapaEntra->bultos=$request->input('total');
        $nuevoCapaEntra->peso=$request->input("peso");
        $nuevoCapaEntra->libras= $nuevoCapaEntra->bultos * $nuevoCapaEntra->peso /16;
        $nuevoCapaEntra->created_at=$fechaa;

        $nuevoCapaEntra->save();

        //return redirect()->route("EntradaBanda")->withExito("Se creó la entrada Correctamente ");
        return back()->withExito("Se creó la entrega Correctamente ");
    } else{
        Session::flash('flash_message', 'YA EXISTE UN PRODUCTO CON ESTAS ESPECIFICACIONES');
        return back();
    }

        //
    }


    public function edit(Request $request)
    { try{
            $inve  =  DB::table('b_inv_inicials')
            ->leftJoin("vitolas","b_inv_inicials.id_vitolas","=","vitolas.id")
            ->leftJoin("marcas","b_inv_inicials.id_marca","=","marcas.id")
            ->select(
                "vitolas.name as nombre_vitolas",
                "marcas.name as nombre_marca")
            ->where("b_inv_inicials.id_marca","=",$request->input("id_marca"))
            ->where("b_inv_inicials.id_vitolas","=",$request->input("id_vitolas"));
        if($inve->count()>0){

        }else{
            $nuevoConsumo = new BInvInicial();
            $nuevoConsumo->id_vitolas=$request->input('id_vitolas');
            $nuevoConsumo->id_marca=$request->input("id_marca");
            $nuevoConsumo->totalinicial= '0';
            $nuevoConsumo->save();
        }
        $editarBandaRecibida=EntradaBultos::findOrFail($request->id);
        $editarBandaRecibida->bultos=$request->input('total');
        $editarBandaRecibida->peso= $request->input('peso');
        $editarBandaRecibida->libras = $editarBandaRecibida->bultos*$editarBandaRecibida->peso/16;
        $editarBandaRecibida->vitola=$request->input('id_vitolas');
        $editarBandaRecibida->marca=$request->input("id_marca");
        $editarBandaRecibida->save();
        //return redirect()->route("EntradaBanda")->withExito("Se editó Correctamente");
        return back()->withExito("Se edito la entrega Correctamente ");
    }catch (ValidationException $exception){
        return redirect()->route("EntradaBanda")->with('errores','errores')->with('id_capa_entregas',$request->input("id"))->withErrors($exception->errors());
    }
}


    public function destroy(Request $request)
    {

        $capaentrega = $request->input('id');
        $borrar = EntradaBultos::findOrFail($capaentrega);
        $borrar->delete();
        //return redirect()->route("EntradaBanda")->withExito("Se borró la entrega satisfactoriamente");
        return back()->withExito("Se elimino la entrega Correctamente ");
    }
    public function export(Request $request)
    {

        $fecha = $request->get("fecha1");

        if ($fecha = null)
            $fecha = Carbon::now()->format('Y-m-d');
        else {
            $fecha = Carbon::parse(  $request->get("fecha1"))->format('Y-m-d');

        }
        return (new EntradaBultosExport($fecha))->download('Listado de Bultos Recibidos'.$fecha.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);

    }

    public function exportpdf(Request $request)
    {
        $fecha = $request->get("fecha1");

        if ($fecha = null)
            $fecha = Carbon::now()->format('Y-m-d');
        else {
            $fecha = Carbon::parse(  $request->get("fecha1"))->format('Y-m-d');

        }
        return (new EntradaBultosExport($fecha))->download('Listado de Bultos Recibidos'.$fecha.'.pdf', \Maatwebsite\Excel\Excel::DOMPDF);

    }
    public function exportcvs(Request $request)
    {
        $fecha = $request->get("fecha1");

        if ($fecha = null)
            $fecha = Carbon::now()->format('Y-m-d');
        else {
            $fecha = Carbon::parse(  $request->get("fecha1"))->format('Y-m-d');

        }
        return (new EntradaBultosExport($fecha))->download('Listado de Bultos Recibidos'.$fecha.'.csv', \Maatwebsite\Excel\Excel::CSV);
    }


    public function Suma100(Request $request){


        $capaentrega = $request->get('id');

        DB::table('entrada_bultos')->where("entrada_bultos.id","=",$capaentrega)->increment('bultos', 1);
        $encontrar= ENTRADABULTOS::FindOrFail($request->get('id'));
            $encontrar->libras= $encontrar->bultos * $encontrar->peso /16;
            $encontrar->save();

        return back()->withExito("Se editó Correctamente");
        //->route("entrada_bultos")->withExito("Se editó Correctamente");

    }
    public function Sumas(Request $request){

        $incremeto =  $request->get('suma');
        $capaentrega = $request->get('id');

        DB::table('entrada_bultos')->where("entrada_bultos.id","=",$capaentrega)
            ->increment('bultos', $incremeto);

            $encontrar= ENTRADABULTOS::FindOrFail($request->get('id'));
            $encontrar->libras= $encontrar->bultos * $encontrar->peso /16;
            $encontrar->save();


        return back()->withExito("Se editó Correctamente");
        //redirect()->route("EntradaBanda")->withExito("Se editó Correctamente");

    }

    public function diferencias(Request $request){

        $salida = DB::table('salidas_materia_primas')
    ->join('combinaciones', 'combinaciones.id', '=', 'salidas_materia_primas.id_combinacion')
    ->join('b_inv_inicials', 'b_inv_inicials.id', 'combinaciones.bulto')
    ->join('vitolas', 'vitolas.id', 'b_inv_inicials.id_vitolas')
    ->join('marcas', 'marcas.id', 'b_inv_inicials.id_marca')
    ->select(
     'vitolas.name as vitola', 'marcas.name as marca',
     DB::raw('sum(salidas_materia_primas.cantidad) as totalDesp'))
     ->addSelect(DB::raw('(select sum(peso) from detalle_combinaciones where
     detalle_combinaciones.id_combinaciones = combinaciones.id) as totalpeso'))
    ->where('salidas_materia_primas.created_at', 'LIKE', '%'.$request->fecha.'%')
    ->groupbyraw('marcas.name, vitolas.name, combinaciones.id')
    ->orderByRaw('marcas.name, vitolas.name')
    ->get();


        $entrada_bultos = DB::table('entrada_bultos')
        ->join('marcas', 'marcas.id', 'entrada_bultos.marca')
        ->join('vitolas', 'vitolas.id', 'entrada_bultos.vitola')
        ->selectRaw('marcas.name as marca')
        ->selectRaw('vitolas.name as vitola')
        ->selectRaw('SUM(entrada_bultos.bultos) as totalDesp')
    ->where('entrada_bultos.created_at', '=', $request->fecha)
    ->groupByRaw('marcas.name, vitolas.name')
    ->orderByRaw('marcas.name, vitolas.name')
    ->get();

    $concatenacion1  = [];
    $concatenacion2  = [];
    foreach ($salida as $value) {
        $concatenacion1[] = ['marca'=>$value->marca.' '.$value->vitola];
    }
    foreach ($entrada_bultos as $value) {
        $concatenacion2[] = ['marca'=>$value->marca.' '.$value->vitola];
    }
    $sal  = array_column($concatenacion1, 'marca');
    $ent  = array_column($concatenacion2, 'marca');

    $diff = array_values(array_diff($sal, $ent));

    $existencia= [];
    #Se buscan las similitudes de semilla y calidad
    foreach($salida as $c){
        foreach($entrada_bultos as $o){
            if($o->marca == $c->marca && $o->vitola == $c->vitola){
                $existencia[] = ['marca'=>$o->marca, 'vitola'=>$o->vitola,
                'totalDespacho'=>$o->totalDesp, 'totalInventario'=>$c->totalDesp,
            'diferencia'=>$o->totalDesp-$c->totalDesp];
            }
        }
        }
        foreach ($diff as $value) {
            $existencia[] = ['marca'=>$value];
        }
        return response()->json($existencia);
    }
}
