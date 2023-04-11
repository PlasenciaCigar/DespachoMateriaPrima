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
        ->join('detalle_combinaciones', 'salidas_materia_primas.id_combinacion', '=', 'detalle_combinaciones.id_combinaciones')
        ->select('salidas_materia_primas.*', 'salidas_materia_primas.id as salida',
         'vitolas.name as vitola', 'marcas.name as marca',
          'vitolas.id as v_id','marcas.id as m_id',
          'combinaciones.id as combinacion', DB::raw('sum(detalle_combinaciones.peso) as totalpeso'))
        ->where('marcas.name', 'LIKE', '%'.$marcas.'%')
        ->where('salidas_materia_primas.created_at', 'LIKE', '%'.$fecha.'%')
        ->groupby('salidas_materia_primas.id')
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

    public function destroymanual(Request $request)
    {
        $delete = DB::table('salida_det_mp')->where('id', '=', $request->id)->delete();
        return back();
    }

    public function sumar(Request $salidaMateriaPrima)
    {
        if($salidaMateriaPrima->suma==null){
            $salidaMateriaPrima->suma=1;
        }
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
        $order  = array_column($consulta->toArray(), 'mp');
        $acum= [];
        foreach ($consulta as $codigoMP) {
            $pesoreal = ($codigoMP->cantidad * $codigoMP->pesos)/16;
            $index= array_search($codigoMP->mp, array_column($acum, 'codigo'));
            if($index!==false){
                $acum[$index]['peso']+= $pesoreal;
              } else {

                $acum[] = ['codigo'=>$codigoMP->mp, 'peso'=> $pesoreal];
                
              }
        }
        $validar= $this->validar($acum);
        if($validar==null){
        foreach ($acum as $value) {
            $materiaprima = MateriaPrima::FindOrFail($value['codigo']);
            DB::table('salida_det_mp')
            ->insert(['codigo_materia_prima'=>$value['codigo'],
            'peso'=>$value['peso'],'created_at'=>$fecha,
             'observacion'=>'A Despacho', 'estado'=>1]);
            $materiaprima->Libras= $materiaprima->Libras - $value['peso'];
            $materiaprima->save();
        }
        DB::table('salidasprocesadas')->insert(['created_at'=>$fecha]);
        return back();
    }else{

        $errores=collect($validar);
        Session::flash('flash_message', $errores);
        return back()->with('errores', $errores);

    }

    } 

    function validar($data){
        $value = [];
        foreach ($data as $codigoMP) {
            $materiaprima = MateriaPrima::FindOrFail($codigoMP['codigo']);
            $existencia= $materiaprima->Libras-$codigoMP['peso'];
            if($existencia<0){
                $value[]=['codigo'=>$codigoMP['codigo'],
                'falta'=>$existencia];
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
        /*
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
            $index= array_search($codigoMP->mp, array_column($acum, 'codigo'));
            if($index!==false){
                $acum[$index]['peso']+= $pesoreal;
              } else {
                $acum[] = ['codigo'=>$codigoMP->mp, 'peso'=> $pesoreal];
              }
        } */
        $acum = DB::table('salida_det_mp')->select('codigo_materia_prima', 'peso')
        ->where('created_at', '=', $fecha)->where('observacion', '=', 'A Despacho')->get();
        foreach ($acum as $value) {
            $materiaprima = MateriaPrima::FindOrFail($value->codigo_materia_prima);
            $materiaprima->Libras= $materiaprima->Libras + $value->peso;
            $materiaprima->save();
        }
        DB::table('salida_det_mp')->where('created_at', '=', $fecha)
        ->where('salida_det_mp.observacion', '=', 'A Despacho')
        ->delete();
        DB::table('salidasprocesadas')->where('created_at', '=', $fecha)->delete();
        return back();
        }

        public function versalida($fecha){
            $data = DB::table('salida_det_mp')
            ->where('salida_det_mp.created_at', '=', $fecha)
            ->where('salida_det_mp.observacion', '=', 'A Despacho')
            ->join('materia_primas', 'materia_primas.Codigo',
            'salida_det_mp.codigo_materia_prima')
            ->select('salida_det_mp.*', 'materia_primas.Descripcion')->get();
            return response()->json($data);
        }

        public function excelversalida(Request $request){
            $fecha = $request->fecha;
            return (new VerSalidaExport($fecha))
            ->download('Listado Inventario de Bultos Salida'
            .$fecha.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        }

         /* salidas de materia prima por otros conceptos */

        public function salidaMP(Request $request){
            $fecha = $request->fecha;
            if($fecha==null){
                $fecha = Carbon::now()->format('Y-m-d');
            }
            $fecha=Carbon::parse($fecha)->format('Y-m-d');
            $materiaprima = MateriaPrima::all();
            $data = DB::table('salida_det_mp')
            ->where('salida_det_mp.created_at', '=', $fecha)
            ->where('estado', '=', '0')
            ->where('salida_det_mp.observacion', '!=', 'A Despacho')
            ->join('materia_primas', 'materia_primas.Codigo',
            'salida_det_mp.codigo_materia_prima')
            ->select('salida_det_mp.*', 'materia_primas.Descripcion')
            ->get();
            $validacionproceso = false;
            $yes = DB::table('salida_det_mp')
            ->where('salida_det_mp.created_at', '=', $fecha)
            ->where('estado', '=', '1')
            ->where('salida_det_mp.observacion', '!=', 'A Despacho')
            ->join('materia_primas', 'materia_primas.Codigo',
            'salida_det_mp.codigo_materia_prima')
            ->select('salida_det_mp.*', 'materia_primas.Descripcion')
            ->get();
            if(count($yes)>0){
                $data = DB::table('salida_det_mp')
            ->where('salida_det_mp.created_at', '=', $fecha)
            ->where('estado', '=', '1')
            ->where('salida_det_mp.observacion', '!=', 'A Despacho')
            ->join('materia_primas', 'materia_primas.Codigo',
            'salida_det_mp.codigo_materia_prima')
            ->select('salida_det_mp.*', 'materia_primas.Descripcion')
            ->get();
            $validacionproceso=true;
            }
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
            }
            DB::table('salida_det_mp')
                ->where('created_at', '=', $request->fecha)
                ->where('salida_det_mp.observacion', '!=', 'A Despacho')
                ->update(['estado'=>1]);
            return back();
        }else{
            $errores=collect($array);
            Session::flash('flash_message', $errores);
            return back()->with('errores', $errores);
        }
        }

        public function desaplicardet(Request $request){
            $consulta = DB::table('salida_det_mp')
            ->select('codigo_materia_prima', DB::raw('sum(peso) as peso'))
            ->where('created_at', '=', $request->fecha)
            ->where('estado', '=', '1')
            ->where('salida_det_mp.observacion', '!=', 'A Despacho')
            ->groupBy('codigo_materia_prima')
            ->get();

            foreach ($consulta as  $value){
                $materiaprima = MateriaPrima::FindOrFail($value->codigo_materia_prima);
                $materiaprima->Libras = $materiaprima->Libras + $value->peso;
                $materiaprima->save();
            }

            DB::table('salida_det_mp')
                ->where('created_at', '=', $request->fecha)
                ->where('salida_det_mp.observacion', '!=', 'A Despacho')
                ->update(['estado'=>0]);
            return back();
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

        $diff = array_values(array_diff($ent, $sal));
    
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
