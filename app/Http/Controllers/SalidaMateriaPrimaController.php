<?php

namespace App\Http\Controllers;

use App\SalidaMateriaPrima;
use App\combinaciones;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Session;
use App\MateriaPrima;
use App\BInvInicial;
use App\Vitola;
use App\Marca;
use App\Exports\VerSalidaExport;
use Maatwebsite\Excel\Facades\Excel;
class SalidaMateriaPrimaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $fecha = $request->fecha;
        $marcas = $request->marca;

        if($fecha != null){
            $fecha=Carbon::parse($fecha)->format('Y-m-d');
            
        }else{
            $fecha = Carbon::now()->format('Y-m-d');
        }
        $procesado= DB::table('salidasprocesadas')
        ->where('created_at','=',$fecha)->first();
        $validacionproceso= $procesado != null? true: false;

        $materiaprima = MateriaPrima::all();
        $marca = Marca::all();
        $vitola = Vitola::all();
        $combinacion = combinaciones::all();
        $detalle = DB::table('detalle_combinaciones')
        ->select('codigo_materia_prima')->get();
        
        $salida = DB::table('salidas_materia_primas')
        ->join('combinaciones', 'combinaciones.id', '=', 'salidas_materia_primas.id_combinacion')
        ->join('b_inv_inicials', 'b_inv_inicials.id', 'combinaciones.bulto')
        ->join('vitolas', 'vitolas.id', 'b_inv_inicials.id_vitolas')
        ->join('marcas', 'marcas.id', 'b_inv_inicials.id_marca')
        ->select('salidas_materia_primas.*', 'salidas_materia_primas.id as salida',
         'vitolas.name as vitola', 'marcas.name as marca',
          'vitolas.id as v_id','marcas.id as m_id',
          'combinaciones.id as combinacion')
        ->where('marcas.name', 'LIKE', '%'.$marcas.'%')
        ->where('salidas_materia_primas.created_at', 'LIKE', '%'.$fecha.'%')
        ->get();
        $arre= [];
        foreach ($combinacion as $combinaciones) {
            $arre[] = ['Id'=>$combinaciones->id,
                    'Codigo'=>$this->ConsultarDetalle($combinaciones->id)
                
            ];
        }
        $total = SalidaMateriaPrima::where('created_at','LIKE', '%'.$fecha.'%')->sum('cantidad');
        $dato= collect($arre)->all();
        //return $dato;
        return view('rmp.Salidas.salidasbultos')->with('salida',$salida)->with('total',$total)
        ->with('fecha',$fecha)->with('materiaprima',$materiaprima)->with('validacionproceso',$validacionproceso)
        ->withNoPagina(1)->with('marca',$marca)->with('vitola',$vitola)->with('dato',$dato);
    }

    public function Peticion(Request $request){
        $bulto = BInvInicial::where('id_marca', '=', $request->marca)
        ->where('id_vitolas', '=', $request->vitola)->first();
        if ($bulto!=null) {
            $combinacion = combinaciones::where('bulto', '=', $bulto->id)
            ->orderBy('id', 'desc')->get();
            $arre= [];
            foreach ($combinacion as $combinaciones) {
                $arre[] = ['Id'=>$combinaciones->id,
                        'Codigo'=>$this->ConsultarDetalle($combinaciones->id)
                    
                ];
            }
            $dato= collect($arre)->all();
            return response()->json($dato);
        }
        else{
            return response()->json(["ok"=>true]);
        }

    }

    public function ConsultarDetalle($combinacion){
        $res = DB::table('detalle_combinaciones')
        ->join('materia_primas', 'materia_primas.Codigo',
         'detalle_combinaciones.codigo_materia_prima')
         ->join('combinaciones', 'combinaciones.id',
         'detalle_combinaciones.id_combinaciones')
        ->select('materia_primas.Descripcion',
         'detalle_combinaciones.peso', 'detalle_combinaciones.id_combinaciones')
        ->where('id_combinaciones', '=', $combinacion)->get();
        return $res;

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function ver($comb)
    {
        $combinaciones= DB::table('detalle_combinaciones')
        ->join('combinaciones', 'combinaciones.id', '=', 'detalle_combinaciones.id_combinaciones')
        ->join('materia_primas', 'materia_primas.Codigo', 'detalle_combinaciones.codigo_materia_prima')
        ->select('detalle_combinaciones.codigo_materia_prima',
        'materia_primas.Descripcion',
         'detalle_combinaciones.peso', 'detalle_combinaciones.id_combinaciones')
        ->where('detalle_combinaciones.id_combinaciones', '=', $comb)
        ->get();
        return response()->json($combinaciones);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function validarfecha($fecha){
        $procesado= DB::table('salidasprocesadas')
        ->where('created_at', '=', $fecha)->first();
        return $procesado!=null?false:true;
    }
    public function store(Request $request)
    {
        if ($this->validarfecha($request->fecha)) {
            $emp = new SalidaMateriaPrima;
            $emp->created_at = $request->fecha;
            $emp->id_combinacion = $request->combinacion;
            $emp->cantidad = $request->cantidad;
            $emp->save();
            return back();
            }else{
                Session::flash('flash_message', 'Fecha Ya Procesada');
                return back();
            }
    }

    public function update(Request $request)
    {
        $materiaprima = SalidaMateriaPrima::FindOrFail($request->salida);
        $materiaprima->id_combinacion = $request->combinacion;
        $materiaprima->cantidad = $request->cantidad;
        $materiaprima->save();
        return back();
    }

    public function destroy(Request $request)
    {
        $delete = SalidaMateriaPrima::findOrFail($request->id_salida);
        $delete->delete();
        return back();
    }

    public function sumar(Request $salidaMateriaPrima)
    {
        $query = DB::table('salidas_materia_primas')
        ->where('id', '=', $salidaMateriaPrima->id)
        ->increment('cantidad', $salidaMateriaPrima->suma);
        return back();
    }

    public function procesar(Request $request)
    {
        $fecha = $request->fecha;
        $consulta= DB::table('salidas_materia_primas')
        ->join('combinaciones', 'combinaciones.id', 'salidas_materia_primas.id_combinacion')
        ->join('detalle_combinaciones', 'detalle_combinaciones.id_combinaciones', 'combinaciones.id')
        ->select('detalle_combinaciones.codigo_materia_prima as mp',
        'detalle_combinaciones.peso as pesos', 'salidas_materia_primas.cantidad')
        ->where('salidas_materia_primas.created_at', '=',$fecha)
        ->get();
        $acum= [];
        $order  = array_column($consulta->toArray(), 'mp');
        foreach ($consulta as $codigoMP) {
            $pesoreal = ($codigoMP->cantidad * $codigoMP->pesos)/16;
            if(array_search($codigoMP->mp, array_column($acum, 'codigo'))){
                $index= array_search($codigoMP->mp, array_column($acum, 'codigo'));
                $acum[$index]['peso']+= $pesoreal;
              } else {
                $acum[] = ['codigo'=>$codigoMP->mp, 'peso'=> $pesoreal];
              }
        }
        if($this->validar($acum)){
        foreach ($acum as $value) {
            $materiaprima = MateriaPrima::FindOrFail($value['codigo']);
            DB::table('salida_det_mp')
            ->insert(['codigo_materia_prima'=>$value['codigo'],
            'peso'=>$value['peso'],'created_at'=>$fecha,
             'observacion'=>'A Despacho']);
            $materiaprima->Libras= $materiaprima->Libras - $value['peso'];
            $materiaprima->save();
        }
        DB::table('salidasprocesadas')->insert(['created_at'=>$fecha]);
        return back();
    }else{
        Session::flash('flash_message', 'No hay suficiente existencia');
        return back();
    }

    } 

    function validar($data){
        $value = true;
        foreach ($data as $codigoMP) {
            $materiaprima = MateriaPrima::FindOrFail($codigoMP['codigo']);
            $existencia= $materiaprima->Libras-$codigoMP['peso'];
            if($existencia<0){
                $value=false;
            }
        }
        return $value;
    }

    function validarSalida($data){
        $value = [];
        foreach ($data as $codigoMP) {
            $materiaprima = MateriaPrima::FindOrFail($codigoMP->codigo_materia_prima);
            $existencia= $materiaprima->Libras-$codigoMP->peso;
            if($existencia < 0){
                $value[]=['codigo'=>$codigoMP->codigo_materia_prima,
                'falta'=>$existencia];
            }
        }
        return $value;
    }



        public function desaplicar(Request $request)
        {
        $fecha = $request->fecha;
        $consulta= DB::table('salidas_materia_primas')
        ->join('combinaciones', 'combinaciones.id', 'salidas_materia_primas.id_combinacion')
        ->join('detalle_combinaciones', 'detalle_combinaciones.id_combinaciones', 'combinaciones.id')
        ->select('detalle_combinaciones.codigo_materia_prima as mp',
        'detalle_combinaciones.peso as pesos', 'salidas_materia_primas.cantidad')
        ->where('salidas_materia_primas.created_at', '=',$fecha)
        ->get();
        $acum= [];
        foreach ($consulta as $codigoMP) {
            $pesoreal = ($codigoMP->cantidad * $codigoMP->pesos)/16;
            if(array_search($codigoMP->mp, array_column($acum, 'codigo'))){
                $index= array_search($codigoMP->mp, array_column($acum, 'codigo'));
                $acum[$index]['peso']+= $pesoreal;
              } else {
                $acum[] = ['codigo'=>$codigoMP->mp, 'peso'=> $pesoreal];
              }
        }

        foreach ($acum as $value) {
            $materiaprima = MateriaPrima::FindOrFail($value['codigo']);
            $materiaprima->Libras= $materiaprima->Libras + $value['peso'];
            $materiaprima->save();
        }


        DB::table('salida_det_mp')->where('created_at', '=', $fecha)->delete();
        DB::table('salidasprocesadas')->where('created_at', '=', $fecha)->delete();
        return back();
        }

        public function versalida($fecha){
            $data = DB::table('salida_det_mp')
            ->where('salida_det_mp.created_at', '=', $fecha)
            ->join('materia_primas', 'materia_primas.Codigo',
            'salida_det_mp.codigo_materia_prima')
            ->select('salida_det_mp.*', 'materia_primas.Descripcion')->get();
            return response()->json($data);
        }

        public function excelversalida(Request $request){
            $fecha = $request->fecha;
            return (new VerSalidaExport($fecha))
            ->download('Listado Inventario de Capa'
            .$fecha.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        }

         /* salidas de materia prima por otros conceptos */

        public function salidaMP(Request $request){
            $fecha = $request->fecha;
            if($fecha==null){
                $fecha = Carbon::now()->format('Y-m-d');
            }
            $fecha=Carbon::parse($fecha)->format('Y-m-d');
            $validacionproceso=false;
            $materiaprima = MateriaPrima::all();
            $data = DB::table('salida_det_mp')
            ->where('salida_det_mp.created_at', '=', $fecha)
            ->where('estado', '=', '0')
            ->join('materia_primas', 'materia_primas.Codigo',
            'salida_det_mp.codigo_materia_prima')
            ->select('salida_det_mp.*', 'materia_primas.Descripcion')
            ->get();
            return view('rmp.Salidas.salidamateriaprima')
            ->with('fecha', $fecha)->with('data',$data)
            ->with('validacionproceso',$validacionproceso)
            ->with('materiaprima', $materiaprima)
            ->withNoPagina(1);
        }

        public function salidaMPStore(Request $request){
            DB::table('salida_det_mp')
            ->insert(['codigo_materia_prima'=>$request->codigo,
            'peso'=>$request->libras,'created_at'=>$request->fecha,
             'observacion'=>$request->tipo, 'estado'=>0]);
             return back();
        }

        public function salidaMPUpdate(Request $request){
            DB::table('salida_det_mp')
            ->where('id', '=', $request->id)
            ->update(['codigo_materia_prima'=>$request->codigo,
            'peso'=>$request->libras,
             'observacion'=>$request->tipo]);
             return back();
        }

        public function procesardet(Request $request){
            $consulta = DB::table('salida_det_mp')
            ->select('codigo_materia_prima', DB::raw('sum(peso) as peso'))
            ->where('created_at', '=', $request->fecha)
            ->where('estado', '=', '0')
            ->groupBy('codigo_materia_prima')
            ->get();
            $array = $this->validarSalida($consulta);
            if($array==null){
            foreach ($consulta as  $value){
                $materiaprima = MateriaPrima::FindOrFail($value->codigo_materia_prima);
                $materiaprima->Libras = $materiaprima->Libras - $value->peso;
                $materiaprima->save();
                DB::table('salida_det_mp')
                ->where('id', '=', $value->id)
                ->update(['estado'=>1]);
                return back();
            }
        }else{
            $errores=collect($array);
            Session::flash('flash_message', $errores);
            return back()->with('errores', $errores);
        }
        }

}
