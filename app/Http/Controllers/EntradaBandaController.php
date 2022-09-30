<?php

namespace App\Http\Controllers;

use App\BandaInvInicial;
use App\Calidad;
use App\CInvInicial;
use App\EntradaBanda;
use App\Exports\EntradaBandaExport;
use App\Exports\RecepcionCapaExport;
use App\Procedencia;
use App\RecibirCapa;
use App\Semilla;
use App\Tamano;
use App\Variedad;
use Carbon\Carbon;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;

class EntradaBandaController extends Controller
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

            if ($fecha == null)
                $fecha = Carbon::now()->format('Y-m-d');
            else {
                $fecha = $request->get("fecha");

            }
            $recibirCapa = DB::table("entrada_bandas")
                ->leftJoin("semillas", "entrada_bandas.id_semilla", "=", "semillas.id")
                ->leftJoin("tamanos", "entrada_bandas.id_tamano", "=", "tamanos.id")
                ->leftJoin("variedads", "entrada_bandas.id_variedad", "=", "variedads.id")
                ->leftJoin("procedencias", "entrada_bandas.id_procedencia", "=", "procedencias.id")
                ->select("entrada_bandas.id", "tamanos.name AS nombre_tamano",
                    "entrada_bandas.id_tamano",
                    "entrada_bandas.origen",
                    "entrada_bandas.id_semilla", "semillas.name as nombre_semillas",
                 "entrada_bandas.id_variedad", "variedads.name as nombre_variedad",
                    "entrada_bandas.id_procedencia", "procedencias.name as nombre_procedencia"
                    , "entrada_bandas.total", "entrada_bandas.peso", "entrada_bandas.libras" )
                ->where("semillas.name", "Like", "%" . $query . "%")
                ->whereDate("entrada_bandas.created_at", "=", Carbon::parse($fecha)->format('Y-m-d'))
                ->orderBy("nombre_semillas")
                ->paginate(1000);
            $tamano = Tamano::all();
            $semillas = Semilla::all();
            $variedad = Variedad::all();
            $procedencia =Procedencia::all();

            $entregaCapass=DB::table("entrada_bandas")
                ->leftJoin("semillas", "entrada_bandas.id_semilla", "=", "semillas.id")
                ->leftJoin("tamanos", "entrada_bandas.id_tamano", "=", "tamanos.id")
                ->leftJoin("variedads", "entrada_bandas.id_variedad", "=", "variedads.id")
                ->leftJoin("procedencias", "entrada_bandas.id_procedencia", "=", "procedencias.id")
                ->selectRaw("SUM(total) as total_capa")
                ->where("semillas.name","Like","%".$query."%")
                ->whereDate("entrada_bandas.created_at","=" ,Carbon::parse($fecha)->format('Y-m-d'))
                ->get();

            return view("ConsumoBanda.EntradaBanda")
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

    function validar($semilla, $tamano, $variedad, $procedencia, $fecha, $origen){
        $entrada =DB::table('entrada_bandas')
        ->where('id_semilla', '=', $semilla)
        ->where('id_tamano', '=', $tamano)
        ->where('origen', '=', $origen)
        ->where('id_variedad', '=', $variedad)
        ->where('id_procedencia', '=', $procedencia)
        ->where('created_at', '=', $fecha)->first();

        return $entrada==null?true:false;
    }


    public function store(Request $request)
    {
        /*if($this->validar($request->input('id_semilla'), $request->input('id_tamano'), $request->input('id_variedad'),
        $request->input('id_procedencia'), $request->input('fecha'), $request->input('origen'))){*/
        $inve  =  DB::table('banda_inv_inicials')
            ->leftJoin("semillas","banda_inv_inicials.id_semilla","=","semillas.id")
            ->leftJoin("tamanos","banda_inv_inicials.id_tamano","=","tamanos.id")
            ->leftJoin("variedads", "banda_inv_inicials.id_variedad", "=", "variedads.id")
            ->leftJoin("procedencias", "banda_inv_inicials.id_procedencia", "=", "procedencias.id")

            ->select(
                "banda_inv_inicials.id")
            ->where("banda_inv_inicials.id_semilla","=",$request->input("id_semilla"))
            ->where("banda_inv_inicials.id_variedad","=",$request->input("id_variedad"))
            ->where("banda_inv_inicials.id_procedencia","=",$request->input("id_procedencia"))
            ->where("banda_inv_inicials.id_tamano","=",$request->input("id_tamano"))->get();
           // return $inve;
        if($inve->count()>0){
        }else{
            $nuevoConsumo = new BandaInvInicial();
            $nuevoConsumo->id_semilla=$request->input('id_semilla');
            $nuevoConsumo->id_tamano=$request->input("id_tamano");
            $nuevoConsumo->totalinicial= '0';
            $nuevoConsumo->id_variedad= $request->input("id_variedad");
            $nuevoConsumo->id_procedencia= $request->input("id_procedencia");
            $nuevoConsumo->save();
        }

        $fechaa =$request->input('fecha');
        if ($fechaa == null)
            $fechaa = Carbon::now()->format('Y-m-d');
        else{
            $fechaa = $request->get("fecha");

        }
        $nuevoCapaEntra = new EntradaBanda();

        $nuevoCapaEntra->id_tamano=$request->input('id_tamano');
        $nuevoCapaEntra->id_semilla=$request->input("id_semilla");
        $nuevoCapaEntra->id_variedad=$request->input("id_variedad");
        $nuevoCapaEntra->id_procedencia=$request->input('id_procedencia');
        $nuevoCapaEntra->total=$request->input('total');
        $nuevoCapaEntra->libras=$request->input("peso");
        $nuevoCapaEntra->peso= $request->input("peso") / ($request->input('total')/100)*16;
        //$nuevoCapaEntra->libras= ($request->input('total')/100 * $request->input("peso")/16);
        $nuevoCapaEntra->origen=$request->input('origen');
        $nuevoCapaEntra->created_at=$fechaa;

        $nuevoCapaEntra->save();

        //return redirect()->route("EntradaBanda")->withExito("Se creó la entrada Correctamente ");
        return back()->withExito("Se creó la entrega Correctamente ");
    /*} else{
        Session::flash('flash_message', 'YA EXISTE UN PRODUCTO CON ESTAS ESPECIFICACIONES');
        return back();
    }*/

        //
    }


    public function edit(Request $request)
    { try{


        $inve  =  DB::table('banda_inv_inicials')
            ->leftJoin("semillas","banda_inv_inicials.id_semilla","=","semillas.id")
            ->leftJoin("tamanos","banda_inv_inicials.id_tamano","=","tamanos.id")
            ->leftJoin("variedads", "banda_inv_inicials.id_variedad", "=", "variedads.id")
            ->leftJoin("procedencias", "banda_inv_inicials.id_procedencia", "=", "procedencias.id")
            ->select(
                "banda_inv_inicials.id")
            ->where("banda_inv_inicials.id_semilla","=",$request->input("id_semilla"))
            ->where("banda_inv_inicials.id_variedad","=",$request->input("id_variedad"))
            ->where("banda_inv_inicials.id_procedencia","=",$request->input("id_procedencia"))
            ->where("banda_inv_inicials.id_tamano","=",$request->input("id_tamano"))->get();
            //return $inve;
        if($inve->count()>0){
        }else{
            $nuevoConsumo = new BandaInvInicial();
            $nuevoConsumo->id_semilla=$request->input('id_semilla');
            $nuevoConsumo->id_tamano=$request->input("id_tamano");
            $nuevoConsumo->totalinicial= '0';
            $nuevoConsumo->id_variedad= $request->input("id_variedad");
            $nuevoConsumo->id_procedencia= $request->input("id_procedencia");
            $nuevoConsumo->save();
        }
        $editarBandaRecibida=EntradaBanda::findOrFail($request->id);
        $editarBandaRecibida->id_tamano=$request->input('id_tamano');
        $editarBandaRecibida->id_semilla=$request->input("id_semilla");
        $editarBandaRecibida->id_variedad=$request->input("id_variedad");
        $editarBandaRecibida->id_procedencia=$request->input('id_procedencia');
        $editarBandaRecibida->total=$request->input('total');
        $editarBandaRecibida->libras= $request->input('peso');
        //$editarBandaRecibida->libras= $request->input('total')/100 * $editarBandaRecibida->peso/16;
        $editarBandaRecibida->peso= $request->input("peso") / ($request->input('total')/100)*16;
        $editarBandaRecibida->origen=$request->input('origen');
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
        $borrar = EntradaBanda::findOrFail($capaentrega);
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
        return (new EntradaBandaExport($fecha))->download('Listado de Banda Recibida'.$fecha.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);

    }

    public function exportpdf(Request $request)
    {
        $fecha = $request->get("fecha1");

        if ($fecha = null)
            $fecha = Carbon::now()->format('Y-m-d');
        else {
            $fecha = Carbon::parse(  $request->get("fecha1"))->format('Y-m-d');

        }
        return (new EntradaBandaExport($fecha))->download('Listado de Banda Recibida'.$fecha.'.pdf', \Maatwebsite\Excel\Excel::DOMPDF);

    }
    public function exportcvs(Request $request)
    {
        $fecha = $request->get("fecha1");

        if ($fecha = null)
            $fecha = Carbon::now()->format('Y-m-d');
        else {
            $fecha = Carbon::parse(  $request->get("fecha1"))->format('Y-m-d');

        }
        return (new EntradaBandaExport($fecha))->download('Listado de Banda Recibida'.$fecha.'.csv', \Maatwebsite\Excel\Excel::CSV);
    }


    public function Suma100(Request $request){


        $capaentrega = $request->get('id');

        DB::table('entrada_bandas')->where("entrada_bandas.id","=",$capaentrega)->increment('total', 100);
        $encontrar= ENTRADABANDA::FindOrFail($request->get('id'));
            $encontrar->libras= $encontrar->total/100 * $encontrar->peso /16;
            $encontrar->save();


        return redirect()->route("EntradaBanda")->withExito("Se editó Correctamente");

    }
    public function Sumas(Request $request){

        $incremeto =  $request->get('suma');
        $capaentrega = $request->get('id');

        DB::table('entrada_bandas')->where("entrada_bandas.id","=",$capaentrega)
            ->increment('total', $incremeto);

            $encontrar= ENTRADABANDA::FindOrFail($request->get('id'));
            $encontrar->libras= $encontrar->total/100 * $encontrar->peso /16;
            $encontrar->save();

        return redirect()->route("EntradaBanda")->withExito("Se editó Correctamente");

    }
}
