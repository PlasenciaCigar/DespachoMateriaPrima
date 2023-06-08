<?php

namespace App\Http\Controllers;

use App\BInvInicial;
use App\BultosSalida;
use App\ConsumoBanda;
use App\EntradaBultos;
use App\combinaciones;
use App\Empleado;
use App\EmpleadosBanda;
use App\Exports\EntregaBultoExport;
use App\Exports\BultosSalidasMPExport;
use App\Exports\EntregaCapaExport;
use App\Marca;
use App\Vitola;
use App\Semilla;
use App\Variedad;
USE App\Inventariobultosnorma;
use App\BandaInvInicial;
use App\Tamano;
use App\Procedencia;
use Carbon\Carbon;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BultosSalidaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $modulos = trim($request->get("modulos"));
        $modulos == 0 ? $modulos ='' : $modulos;
        if ($request){
            $query = trim($request->get("search"));


            $fecha = $request->get("fecha");

            if ($fecha == null)
                $fecha = Carbon::now()->format('Y-m-d');
            else{
                $fecha = $request->get("fecha");

            }

            $bultoentrega=DB::table("bultos_salidas")
                ->leftJoin("empleados_bandas","bultos_salidas.id_empleado","=","empleados_bandas.id")
                ->leftJoin("vitolas","bultos_salidas.id_vitolas","=","vitolas.id")
                ->leftJoin("marcas","bultos_salidas.id_marca","=","marcas.id")
                ->leftJoin("semillas","bultos_salidas.id_semilla","=","semillas.id")
                ->leftJoin("variedads","bultos_salidas.id_variedad","=","variedads.id")
                ->leftJoin("tamanos","bultos_salidas.id_tamano","=","tamanos.id")
                ->leftJoin("procedencias","bultos_salidas.id_procedencia","=","procedencias.id")
                ->select("bultos_salidas.id", "semillas.id as id_semilla",
                "variedads.id as id_variedad", "tamanos.id as id_tamano",
                "procedencias.id as id_procedencia", "bultos_salidas.verificar",
                    "empleados_bandas.nombre AS nombre_empleado",
                    "semillas.name as semilla","variedads.name as variedad",
                    "tamanos.name as tamano","procedencias.name as procedencia",
                    "empleados_bandas.codigo AS codigo_empleado",
                    "vitolas.name as nombre_vitolas",
                    "bultos_salidas.id_empleado",
                    "bultos_salidas.id_vitolas",
                    "bultos_salidas.adicional",
                    "bultos_salidas.id_marca","marcas.name as nombre_marca",
                    "bultos_salidas.combinacion","bultos_salidas.total")
                ->where("empleados_bandas.codigo","Like","%".$query."%")
                ->where("empleados_bandas.salon","Like","%".$modulos."%")
                ->where("marcas.name","Like","%".$request->marca."%")
                ->whereDate("bultos_salidas.created_at","=" ,Carbon::parse($fecha)->format('Y-m-d'))
                ->orderBy("codigo_empleado")
                //  ->whereDate("capa_entregas.created_at","=" ,Carbon::now()->format('Y-m-d'))
                ->paginate(1000);
            $empleados = EmpleadosBanda::all();
            $vitola = Vitola::all();
            $marca = Marca::all();
            $semilla = Semilla::all();
            $variedad = Variedad::all();
            $tamano = Tamano::all();
            $procedencia= Procedencia::all();
            $norma= combinaciones::all();
            $entregaCapass=DB::table("bultos_salidas")
                ->leftJoin("empleados_bandas","bultos_salidas.id_empleado","=","empleados_bandas.id")
                ->leftJoin("vitolas","bultos_salidas.id_vitolas","=","vitolas.id")
                ->leftJoin("marcas","bultos_salidas.id_marca","=","marcas.id")
                ->selectRaw("SUM(total) as total_capa")
                ->where("empleados_bandas.codigo","Like","%".$query."%")
                ->whereDate("bultos_salidas.created_at","=" ,Carbon::parse($fecha)->format('Y-m-d'))
                ->get();

                $fechamostrar = Carbon::now()->format('d/m/Y H:i:A');
               $lmao = Carbon::now()->format('Y-m-d');

               $generado = DB::table('salida_despacho_mp')
               ->where('created_at', '=', $fecha)->first();
               $generado1 = DB::table('salidasprocesadas')
               ->where('created_at', '=', $fecha)->first();

            return view("BultosSalida.Bultossalida")
                ->withNoPagina(1)
                ->with('lmao', $lmao)
                ->with('generado', $generado)
                ->with('generado1', $generado1)
                ->with('fecha', $fecha)
                ->withEntregaBulto($bultoentrega)
                ->withFechamostrar($fechamostrar)
                ->withEmpleados($empleados)
                ->withTotal($entregaCapass)
                ->withVitola($vitola)
                ->withMarca($marca)
                ->withSemilla($semilla)
                ->withVariedad($variedad)
                ->withTamano($tamano)
                ->withNorma($norma)
                ->withProcedencia($procedencia);
        }


        //Autodidacta, responsable, buenas relaciones interpersonales.
        //Introvertido. me cuesta decir no.
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
        public function Peticion(Request $request){
            $frecuente = DB::select('select id_semilla, id_tamano, id_procedencia, id_variedad, count(*) as mayor 
            from bultos_salidas where id_marca= ? and id_vitolas= ? 
            group by id_semilla, id_tamano, id_procedencia, id_variedad order by mayor desc limit 1',
        [$request->marca, $request->vitola]);
            if ($frecuente!=null) {
                $arre= [];
                foreach ($frecuente as $val) {
                    $arre[] = ['semilla'=>$val->id_semilla, 'tamano'=>$val->id_tamano,
                    'procedencia'=>$val->id_procedencia, 'variedad'=>$val->id_variedad
                    ];
                }
                $dato= collect($arre)->all();
                return response()->json($dato);
            }
            else{
                return response()->json(["ok"=>true]);
            }
    
        }

    public function store(Request $request)
    {

        try{
            $this->validate($request, [
                'id_empleado'=>'required',
                'id_vitolas'=>'required',
                'id_marca'=>'required',
            ]);

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

        //para consultar si existe la marca y vitola en la tabla consumo de banda y si no existe se inserta
        $banda  =  DB::table('consumo_bandas')
            ->leftJoin("vitolas","consumo_bandas.id_vitolas","=","vitolas.id")
            ->leftJoin("marcas","consumo_bandas.id_marca","=","marcas.id")
            ->leftJoin("semillas","consumo_bandas.id_semillas","=","semillas.id")
            ->leftJoin("variedads","consumo_bandas.variedad","=","variedads.id")
            ->leftJoin("tamanos","consumo_bandas.id_tamano","=","tamanos.id")
            ->leftJoin("procedencias","consumo_bandas.procedencia","=","procedencias.id")
            ->select(
                "consumo_bandas.id",
                "vitolas.name as nombre_vitolas",
                "marcas.name as nombre_marca",
                "consumo_bandas.id_vitolas",
                "consumo_bandas.id_marca")
            ->where("consumo_bandas.id_marca","=",$request->input("id_marca"))
            ->where("consumo_bandas.id_vitolas","=",$request->input("id_vitolas"))
            ->where("consumo_bandas.id_semillas","=",$request->input("id_semilla"))
            ->where("consumo_bandas.variedad","=",$request->input("id_variedad"))
            ->where("consumo_bandas.id_tamano","=",$request->input("id_tamano"))
            ->where("consumo_bandas.procedencia","=",$request->input("id_procedencia"))
            ->whereDate("consumo_bandas.created_at","=" ,$request->input("fecha"))->paginate(1000);

        if($banda->count()>0){

            foreach ($banda as $bandas) {

                DB::table('consumo_bandas')
                    ->where("consumo_bandas.id", "=", $bandas->id)
                    ->increment('total', 100);
            }

        }else{
            $nuevoConsumo = new ConsumoBanda();
            $nuevoConsumo->id_vitolas=$request->input('id_vitolas');
            $nuevoConsumo->id_marca=$request->input("id_marca");
            $nuevoConsumo->id_semillas= $request->input("id_semilla");
            $nuevoConsumo->id_tamano= $request->input("id_tamano");
            $nuevoConsumo->variedad= $request->input("id_variedad");
            $nuevoConsumo->procedencia= $request->input("id_procedencia");
            $nuevoConsumo->created_at= $request->input('fecha');
            $nuevoConsumo->total= 100;
            $nuevoConsumo->save();
        }
//para ver si existe en la tabla intermediaria y si no la inserta

        $inve  =  DB::table('b_inv_inicials')
            ->leftJoin("vitolas","b_inv_inicials.id_vitolas","=","vitolas.id")
            ->leftJoin("marcas","b_inv_inicials.id_marca","=","marcas.id")
            ->select(
                "vitolas.name as nombre_vitolas",
                "marcas.name as nombre_marca",
                "consumo_bandas.id_vitolas",
                "consumo_bandas.id_marca")
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

        $nuevoBultoEntrega = new BultosSalida();
        $nuevoBultoEntrega->id_empleado=$request->input('id_empleado');
        $nuevoBultoEntrega->id_vitolas=$request->input('id_vitolas');
        $nuevoBultoEntrega->id_marca=$request->input("id_marca");
        $nuevoBultoEntrega->id_semilla=$request->input("id_semilla");
        $nuevoBultoEntrega->id_variedad=$request->input("id_variedad");
        $nuevoBultoEntrega->id_tamano=$request->input("id_tamano");
        $nuevoBultoEntrega->id_procedencia=$request->input("id_procedencia");
        $nuevoBultoEntrega->total=('1');
        $nuevoBultoEntrega->adicional=0;
        $nuevoBultoEntrega->combinacion=$request->combinaciones;
        $nuevoBultoEntrega->created_at=$fechaa;
        $nuevoBultoEntrega->save();

            if ($request->input('subir')== 'on') {


            $consultabulto= EntradaBultos::where('marca', $request->input("id_marca"))
            ->where('vitola', $request->input("id_vitolas"))->where('created_at', $fechaa)->select('id')->first();
            $id_entrada;
            $consultabulto==null ? $id_entrada=null : $id_entrada=$consultabulto->id;

            if ($id_entrada!=null) {
                DB::table('entrada_bultos')->where("entrada_bultos.id","=",$id_entrada)->increment('bultos', 1);
            }else{
            $nuevaentrada = new EntradaBultos();
            $nuevaentrada->peso= 0;
            $nuevaentrada->libras = 0;
            $nuevaentrada->bultos= 1;
            $nuevaentrada->marca=$request->input("id_marca");
            $nuevaentrada->vitola=$request->input('id_vitolas');
            $nuevaentrada->created_at= $fechaa;
            $nuevaentrada->save();
            }
        }

            return back()->withExito("Se creó la salida Correctamente ");
        //return redirect()->route("BultoSalida")->withExito("Se creó la entrega Correctamente ");
        }catch (ValidationException $exception){
            return redirect()->route("BultoSalida")->with('errores','errores')->with('id_capa_entregas',$request->input("id"))->withErrors($exception->errors());
        }
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\BultosSalida  $bultosSalida
     * @return \Illuminate\Http\Response
     */
    public function show(BultosSalida $bultosSalida)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\BultosSalida  $bultosSalida
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
            $editarBultoEntrega=BultosSalida::findOrFail($request->id);
            /*
            $editarBultoEntrega->id_empleado=$request->input('id_empleado');
            $editarBultoEntrega->id_vitolas=$request->input('id_vitolas');
            $editarBultoEntrega->id_marca=$request->input("id_marca");
            $editarBultoEntrega->id_semilla=$request->input("id_semilla");
            $editarBultoEntrega->id_variedad=$request->input("id_variedad");
            $editarBultoEntrega->id_tamano=$request->input("id_tamano");
            $editarBultoEntrega->id_procedencia=$request->input("id_procedencia");
            $editarBultoEntrega->id_marca=$request->input("id_marca");
            $editarBultoEntrega->total=$request->input('total');
            */
            $editarBultoEntrega->combinacion=$request->input('norma');

            $editarBultoEntrega->save();
            return back()->withExito("Se edito la salida Correctamente ");
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\BultosSalida  $bultosSalida
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\BultosSalida  $bultosSalida
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $capaentrega = $request->get('id');

        $Bulto=DB::table("bultos_salidas")
            ->leftJoin("empleados_bandas","bultos_salidas.id_empleado","=","empleados_bandas.id")
            ->leftJoin("vitolas","bultos_salidas.id_vitolas","=","vitolas.id")
            ->leftJoin("marcas","bultos_salidas.id_marca","=","marcas.id")
            ->select("bultos_salidas.id",
                "vitolas.name as nombre_vitolas",
                "bultos_salidas.id_vitolas",
                "bultos_salidas.id_semilla",
                "bultos_salidas.id_variedad",
                "bultos_salidas.id_tamano",
                "bultos_salidas.created_at",
                "bultos_salidas.id_procedencia",
                "bultos_salidas.total as totalres",
                "bultos_salidas.id_marca")
          ->where("bultos_salidas.id","=",$capaentrega)->get();
foreach ($Bulto as $bultos) {
  $banda = DB::table('consumo_bandas')
      ->leftJoin("vitolas", "consumo_bandas.id_vitolas", "=", "vitolas.id")
      ->leftJoin("marcas", "consumo_bandas.id_marca", "=", "marcas.id")
      ->leftJoin("semillas","consumo_bandas.id_semillas","=","semillas.id")
      ->leftJoin("variedads","consumo_bandas.variedad","=","variedads.id")
    ->leftJoin("tamanos","consumo_bandas.id_tamano","=","tamanos.id")
    ->leftJoin("procedencias","consumo_bandas.procedencia","=","procedencias.id")
      ->select(
          "consumo_bandas.id",
          "vitolas.name as nombre_vitolas",
          "marcas.name as nombre_marca",
          "consumo_bandas.id_vitolas",
          "consumo_bandas.id_marca")
      ->where("consumo_bandas.id_marca", "=", $bultos->id_marca)
      ->where("consumo_bandas.id_vitolas", "=", $bultos->id_vitolas)
      ->where("consumo_bandas.id_semillas","=",$bultos->id_semilla)
    ->where("consumo_bandas.variedad","=",$bultos->id_variedad)
    ->where("consumo_bandas.id_tamano","=",$bultos->id_tamano)
    ->where("consumo_bandas.procedencia","=",$bultos->id_procedencia)
      ->whereDate("consumo_bandas.created_at", "=", $bultos->created_at)->get();
foreach ($banda as $bandas)
  DB::table('consumo_bandas')->where("consumo_bandas.id","=",$bandas->id)->decrement('total', $bultos->totalres*100);
}
        $capaentrega = $request->input('id');
        $borrar = BultosSalida::findOrFail($capaentrega);

        $borrar->delete();
        return back()->withExito("Se elimino la salida Correctamente ");
       // return redirect()->route("BultoSalida")->withExito("Se borró la entrega satisfactoriamente");
        //
    }


    public function Suma(Request $request){


        $capaentrega = $request->get('id');
        $fecha = $request->get('fecha');

                $Bulto=DB::table("bultos_salidas")
                    ->leftJoin("empleados_bandas","bultos_salidas.id_empleado","=","empleados_bandas.id")
                    ->leftJoin("vitolas","bultos_salidas.id_vitolas","=","vitolas.id")
                    ->leftJoin("marcas","bultos_salidas.id_marca","=","marcas.id")
                    ->select("bultos_salidas.id",
                    "bultos_salidas.created_at",
                        "vitolas.name as nombre_vitolas",
                        "bultos_salidas.id_vitolas",
                        "bultos_salidas.id_semilla",
                        "bultos_salidas.id_variedad",
                        "bultos_salidas.id_tamano",
                        "bultos_salidas.id_procedencia",
                        "bultos_salidas.id_marca")
                  ->where("bultos_salidas.id","=",$capaentrega)->get();
      foreach ($Bulto as $bultos) {
          $banda = DB::table('consumo_bandas')
              ->leftJoin("vitolas", "consumo_bandas.id_vitolas", "=", "vitolas.id")
              ->leftJoin("marcas", "consumo_bandas.id_marca", "=", "marcas.id")
              ->leftJoin("semillas","consumo_bandas.id_semillas","=","semillas.id")
              ->leftJoin("variedads","consumo_bandas.variedad","=","variedads.id")
            ->leftJoin("tamanos","consumo_bandas.id_tamano","=","tamanos.id")
            ->leftJoin("procedencias","consumo_bandas.procedencia","=","procedencias.id")
              ->select(
                  "consumo_bandas.id",
                  "vitolas.name as nombre_vitolas",
                  "marcas.name as nombre_marca",
                  "consumo_bandas.id_vitolas",
                  "consumo_bandas.id_marca")
              ->where("consumo_bandas.id_marca", "=", $bultos->id_marca)
              ->where("consumo_bandas.id_vitolas", "=", $bultos->id_vitolas)
              ->where("consumo_bandas.id_semillas","=",$bultos->id_semilla)
            ->where("consumo_bandas.variedad","=",$bultos->id_variedad)
            ->where("consumo_bandas.id_tamano","=",$bultos->id_tamano)
            ->where("consumo_bandas.procedencia","=",$bultos->id_procedencia)
              ->whereDate("consumo_bandas.created_at", "=", $bultos->created_at)->get();

              foreach ($banda as $bandas)
          DB::table('consumo_bandas')->where("consumo_bandas.id","=",$bandas->id)->increment('total', 100);
      }

        DB::table('bultos_salidas')->where("bultos_salidas.id","=",$capaentrega)->increment('total', 1);

        $consultasalida= BultosSalida::where('id', $capaentrega)
            ->select('id_marca', 'id_vitolas', 'created_at')->first();

        if ($request->input('subir')== 'on') {
            $consultabulto= EntradaBultos::where('marca', $consultasalida->id_marca)
            ->where('vitola', $consultasalida->id_vitolas)->where('created_at', $fecha)
            ->select('id')->first();
            $id_entrada;
            $consultabulto==null ? $id_entrada=null : $id_entrada=$consultabulto->id;

            if ($id_entrada!=null) {
                DB::table('entrada_bultos')->where("entrada_bultos.id","=",$id_entrada)->increment('bultos', 1);
            }else{
            $nuevaentrada = new EntradaBultos();
            $nuevaentrada->peso= 0;
            $nuevaentrada->libras = 0;
            $nuevaentrada->bultos= 1;
            $nuevaentrada->marca=$consultasalida->id_marca;
            $nuevaentrada->vitola=$consultasalida->id_vitolas;
            $nuevaentrada->created_at= $fecha;
            $nuevaentrada->save();
            }
        }

        //return redirect()->route("BultoSalida")->withExito("Se Incremento el bulto  Correctamente");
        return back()->withExito("Se Incremento el bulto  Correctamente");
    }


    public function Resta(Request $request){


        $capaentrega = $request->get('id');
        $fecha = $request->get('fecha');

                $Bulto=DB::table("bultos_salidas")
                    ->leftJoin("empleados_bandas","bultos_salidas.id_empleado","=","empleados_bandas.id")
                    ->leftJoin("vitolas","bultos_salidas.id_vitolas","=","vitolas.id")
                    ->leftJoin("marcas","bultos_salidas.id_marca","=","marcas.id")
                    ->select("bultos_salidas.id",
                        "vitolas.name as nombre_vitolas",
                        "bultos_salidas.id_vitolas",
                        "bultos_salidas.id_semilla",
                        "bultos_salidas.id_variedad",
                        "bultos_salidas.id_tamano",
                        "bultos_salidas.id_procedencia",
                        "bultos_salidas.created_at",
                        "bultos_salidas.id_marca")
                  ->where("bultos_salidas.id","=",$capaentrega)->get();
      foreach ($Bulto as $bultos) {
          $banda = DB::table('consumo_bandas')
              ->leftJoin("vitolas", "consumo_bandas.id_vitolas", "=", "vitolas.id")
              ->leftJoin("marcas", "consumo_bandas.id_marca", "=", "marcas.id")
              ->leftJoin("semillas","consumo_bandas.id_semillas","=","semillas.id")
              ->leftJoin("variedads","consumo_bandas.variedad","=","variedads.id")
            ->leftJoin("tamanos","consumo_bandas.id_tamano","=","tamanos.id")
            ->leftJoin("procedencias","consumo_bandas.procedencia","=","procedencias.id")
              ->select(
                  "consumo_bandas.id",
                  "vitolas.name as nombre_vitolas",
                  "marcas.name as nombre_marca",
                  "consumo_bandas.id_vitolas",
                  "consumo_bandas.id_marca")
              ->where("consumo_bandas.id_marca", "=", $bultos->id_marca)
              ->where("consumo_bandas.id_vitolas", "=", $bultos->id_vitolas)
              ->where("consumo_bandas.id_semillas","=",$bultos->id_semilla)
            ->where("consumo_bandas.variedad","=",$bultos->id_variedad)
            ->where("consumo_bandas.id_tamano","=",$bultos->id_tamano)
            ->where("consumo_bandas.procedencia","=",$bultos->id_procedencia)
              ->whereDate("consumo_bandas.created_at", "=",$bultos->created_at)->get();

              foreach ($banda as $bandas)
          DB::table('consumo_bandas')->where("consumo_bandas.id","=",$bandas->id)->decrement('total', 100);
      }

        DB::table('bultos_salidas')->where("bultos_salidas.id","=",$capaentrega)->decrement('total', 1);

        $consultasalida= BultosSalida::where('id', $capaentrega)
            ->select('id_marca', 'id_vitolas', 'created_at')->first();

        if ($request->input('subir')== 'on') {
            $consultabulto= EntradaBultos::where('marca', $consultasalida->id_marca)
            ->where('vitola', $consultasalida->id_vitolas)->where('created_at', $fecha)
            ->select('id')->first();
            $id_entrada;
            $consultabulto==null ? $id_entrada=null : $id_entrada=$consultabulto->id;

            if ($id_entrada!=null) {
                DB::table('entrada_bultos')->where("entrada_bultos.id","=",$id_entrada)->decrement('bultos', 1);
            }
        }

        //return redirect()->route("BultoSalida")->withExito("Se Incremento el bulto  Correctamente");
        return back()->withExito("Se Resto el bulto  Correctamente");
    }


    public function export(Request $request)
    {

        $fecha = $request->get("fecha1");

        if ($fecha = null)
            $fecha = Carbon::now()->format('Y-m-d');
        else {
            $fecha = Carbon::parse(  $request->get("fecha1"))->format('Y-m-d');

        }
        return (new EntregaBultoExport($fecha))->download('Listado de Entrega Bultos'.$fecha.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);

    }

    public function exportpdf(Request $request)
    {
        $fecha = $request->get("fecha1");

        if ($fecha = null)
            $fecha = Carbon::now()->format('Y-m-d');
        else {
            $fecha = Carbon::parse(  $request->get("fecha1"))->format('Y-m-d');

        }
        return (new EntregaBultoExport($fecha))->download('Listado De Entrega Bultos '.$fecha.'.pdf', \Maatwebsite\Excel\Excel::DOMPDF);

    }
    public function exportcvs(Request $request)
    {
        $fecha = $request->get("fecha1");

        if ($fecha = null)
            $fecha = Carbon::now()->format('Y-m-d');
        else {
            $fecha = Carbon::parse(  $request->get("fecha1"))->format('Y-m-d');

        }
        return (new EntregaBultoExport($fecha))->download('Listado de Entrega Bultos'.$fecha.'.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    public function Suma100(Request $request){

        $capaentrega = $request->get('id');

        DB::table('bultos_salidas')->where("bultos_salidas.id","=",$capaentrega)->increment('adicional', 1);


        $Bulto=DB::table("bultos_salidas")
            ->leftJoin("empleados_bandas","bultos_salidas.id_empleado","=","empleados_bandas.id")
            ->leftJoin("vitolas","bultos_salidas.id_vitolas","=","vitolas.id")
            ->leftJoin("marcas","bultos_salidas.id_marca","=","marcas.id")
            ->select("bultos_salidas.id",
                "vitolas.name as nombre_vitolas",
                "bultos_salidas.id_vitolas",
                "bultos_salidas.id_semilla",
                "bultos_salidas.id_variedad",
                "bultos_salidas.id_tamano",
                "bultos_salidas.id_procedencia",
                "bultos_salidas.created_at",
                "bultos_salidas.id_marca")
          ->where("bultos_salidas.id","=",$capaentrega)->get();
foreach ($Bulto as $bultos) {
  $banda = DB::table('consumo_bandas')
      ->leftJoin("vitolas", "consumo_bandas.id_vitolas", "=", "vitolas.id")
      ->leftJoin("marcas", "consumo_bandas.id_marca", "=", "marcas.id")
      ->leftJoin("semillas","consumo_bandas.id_semillas","=","semillas.id")
      ->leftJoin("variedads","consumo_bandas.variedad","=","variedads.id")
    ->leftJoin("tamanos","consumo_bandas.id_tamano","=","tamanos.id")
    ->leftJoin("procedencias","consumo_bandas.procedencia","=","procedencias.id")
      ->select(
          "consumo_bandas.id",
          "vitolas.name as nombre_vitolas",
          "marcas.name as nombre_marca",
          "consumo_bandas.id_vitolas",
          "consumo_bandas.id_marca")
      ->where("consumo_bandas.id_marca", "=", $bultos->id_marca)
      ->where("consumo_bandas.id_vitolas", "=", $bultos->id_vitolas)
      ->where("consumo_bandas.id_semillas","=",$bultos->id_semilla)
    ->where("consumo_bandas.variedad","=",$bultos->id_variedad)
    ->where("consumo_bandas.id_tamano","=",$bultos->id_tamano)
    ->where("consumo_bandas.procedencia","=",$bultos->id_procedencia)
      ->whereDate("consumo_bandas.created_at", "=", $bultos->created_at)->get();
foreach ($banda as $bandas)
  DB::table('consumo_bandas')->where("consumo_bandas.id","=",$bandas->id)->increment('total', 100);
}

/*
        $capaentrega = $request->get('id');
       // DB::table('bultos_salidas')->where("consumo_bandas.id","=",$capaentrega)->increment('total', 100);
        DB::table('consumo_bandas')->where("consumo_bandas.id","=",$capaentrega)->increment('total', 100);

        */

        return back()->withExito("Se incremento Correctamente");
        //redirect()->route("BultoSalida")->withExito("Se editó Correctamente");

    }


    public function Resta100(Request $request){

        $capaentrega = $request->get('ids');
        //return $capaentrega;

        DB::table('bultos_salidas')->where("bultos_salidas.id","=",$capaentrega)->decrement('adicional', 1);


        $Bulto=DB::table("bultos_salidas")
            ->leftJoin("empleados_bandas","bultos_salidas.id_empleado","=","empleados_bandas.id")
            ->leftJoin("vitolas","bultos_salidas.id_vitolas","=","vitolas.id")
            ->leftJoin("marcas","bultos_salidas.id_marca","=","marcas.id")
            ->select("bultos_salidas.id",
                "vitolas.name as nombre_vitolas",
                "bultos_salidas.id_vitolas",
                "bultos_salidas.id_semilla",
                "bultos_salidas.id_variedad",
                "bultos_salidas.id_tamano",
                "bultos_salidas.id_procedencia",
                "bultos_salidas.created_at",
                "bultos_salidas.id_marca")
          ->where("bultos_salidas.id","=",$capaentrega)->get();
foreach ($Bulto as $bultos) {
  $banda = DB::table('consumo_bandas')
      ->leftJoin("vitolas", "consumo_bandas.id_vitolas", "=", "vitolas.id")
      ->leftJoin("marcas", "consumo_bandas.id_marca", "=", "marcas.id")
      ->leftJoin("semillas","consumo_bandas.id_semillas","=","semillas.id")
      ->leftJoin("variedads","consumo_bandas.variedad","=","variedads.id")
    ->leftJoin("tamanos","consumo_bandas.id_tamano","=","tamanos.id")
    ->leftJoin("procedencias","consumo_bandas.procedencia","=","procedencias.id")
      ->select(
          "consumo_bandas.id",
          "vitolas.name as nombre_vitolas",
          "marcas.name as nombre_marca",
          "consumo_bandas.id_vitolas",
          "consumo_bandas.id_marca")
      ->where("consumo_bandas.id_marca", "=", $bultos->id_marca)
      ->where("consumo_bandas.id_vitolas", "=", $bultos->id_vitolas)
      ->where("consumo_bandas.id_semillas","=",$bultos->id_semilla)
    ->where("consumo_bandas.variedad","=",$bultos->id_variedad)
    ->where("consumo_bandas.id_tamano","=",$bultos->id_tamano)
    ->where("consumo_bandas.procedencia","=",$bultos->id_procedencia)
      ->whereDate("consumo_bandas.created_at", "=", $bultos->created_at)->get();
foreach ($banda as $bandas)
  DB::table('consumo_bandas')->where("consumo_bandas.id","=",$bandas->id)->decrement('total', 100);
}

/*
        $capaentrega = $request->get('id');
       // DB::table('bultos_salidas')->where("consumo_bandas.id","=",$capaentrega)->increment('total', 100);
        DB::table('consumo_bandas')->where("consumo_bandas.id","=",$capaentrega)->increment('total', 100);

        */

        return back()->withExito("Se incremento Correctamente");
        //redirect()->route("BultoSalida")->withExito("Se editó Correctamente");

    }


    public function GenerarSalidaMP(Request $request){
        try {
            DB::beginTransaction();
        $fecha = $request->fecha1;
        $bultoentrega=DB::table("bultos_salidas")
        ->select(
            "bultos_salidas.id_marca as marca","bultos_salidas.id_vitolas as vitola",
            DB::raw("sum(total) as total"))
        ->whereDate("bultos_salidas.created_at","=" ,Carbon::parse($fecha)->format('Y-m-d'))
        ->distinct()->groupByRaw("bultos_salidas.id_marca, bultos_salidas.id_vitolas")->get();

        foreach($bultoentrega as $value){
            $marca =$value->marca;
            $vitola = $value->vitola;

            $obtenerbulto = DB::table('b_inv_inicials')->where('id_marca', '=', $marca)
            ->where('id_vitolas', '=', $vitola)->first();
            $valor = $value->total;
            $bulto = $obtenerbulto->id;
            while ($valor > 0) {
                $reb = DB::select('select fecha, inventariobultosnorma.id, combinacion, cantidad from inventariobultosnorma 
                inner join combinaciones on combinaciones.id = inventariobultosnorma.combinacion
                where combinaciones.bulto = ? and cantidad>0 order by fecha asc, id asc limit 1',[$bulto]);
                $operacion = $valor-$reb[0]->cantidad;
                if($operacion==0){
                    $this->ConsultarMP($reb[0]->combinacion, $reb[0]->cantidad, $reb[0]->id, $marca, $vitola, $fecha);
                    $valor = 0;
                }
                if($operacion>0){
                    $this->ConsultarMP($reb[0]->combinacion, $valor-$operacion, $reb[0]->id, $marca, $vitola, $fecha);
                    $valor = $operacion;
                }
                if($operacion<0){
                    $this->ConsultarMP($reb[0]->combinacion, $valor, $reb[0]->id, $marca, $vitola, $fecha);
                    $valor = 0;
                }
            }
            }
            DB::commit();
            return back();
            }catch (\Throwable $th) {
                DB::rollback();
                return back()->withExito('No tiene existencias que descargar');
            }
    }

    function ConsultarMP($combinacion, $cantidad, $id, $marca, $vitola, $fecha){
        $detalle = DB::table('detalle_combinaciones')
        ->where('id_combinaciones', '=', $combinacion)->get();

        foreach($detalle as $val){
            $pesoReal = ($cantidad * $val->peso)/16;
            DB::table('salida_despacho_mp')
                    ->insert(['codigo_mp'=>$val->codigo_materia_prima,
                    'marca'=>$marca, 'vitola'=>$vitola,
                    'bultos'=>$cantidad,'peso'=>$pesoReal,
                    'created_at'=>$fecha]);
                }
                $update = Inventariobultosnorma::find($id);
                $update->cantidad = $update->cantidad - $cantidad;
                $update->fecha_salida = $fecha;
                $update->cant_sali = $cantidad;
                $update->save();
    }


    public function ExcelBultosMP(Request $request){
        $fecha = $request->fecha1;

        return (new BultosSalidasMPExport($fecha))
        ->download('Entrega de Materia Prima a Salon'.$fecha.'.xlsx',
         \Maatwebsite\Excel\Excel::XLSX);
    }

    public function Verify(Request $request, $id){
        $sal = BultosSalida::Find($id);
        $sal->verificar = $request->verify;
        $sal->save();
        return back();
    }

}



