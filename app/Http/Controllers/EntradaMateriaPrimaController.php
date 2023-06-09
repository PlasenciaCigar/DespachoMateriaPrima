<?php

namespace App\Http\Controllers;

use App\EntradaMateriaPrima;
use App\MateriaPrima;
use App\Vitola;
use App\Marca;
use App\Inventariobultosnorma;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Exports\DevolucionesMateriaPrima;
use Maatwebsite\Excel\Facades\Excel;
use Session;

class EntradaMateriaPrimaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $fecha = $request->fecha;
        $vitolas = Vitola::all();
        $marcas = Marca::all();
        if($fecha != null){
            $fecha=Carbon::parse($fecha)->format('Y-m-d');
            
        }else{
            $fecha = Carbon::now()->format('Y-m-d');
        }
        $procesado= DB::table('entradasprocesadas')->where('created_at','=',$fecha)->first();
        $validacionproceso= $procesado != null? true: false;
        $materiaprima = MateriaPrima::all();
        $entrada = DB::table('entrada_materia_primas')
        ->join('materia_primas', 'materia_primas.codigo', '=', 'entrada_materia_primas.codigo_materia_prima')
        ->select('entrada_materia_primas.*', 'materia_primas.Descripcion as nombre')
        ->where('entrada_materia_primas.created_at', 'LIKE', '%'.$fecha.'%')
        ->get();

        $total = EntradaMateriaPrima::where('created_at','LIKE', '%'.$fecha.'%')->sum('Libras');
        return view('rmp.entradas.entradamateriaprima')->with('entrada',$entrada)->with('total',$total)
        ->with('fecha',$fecha)->with('materiaprima',$materiaprima)->with('validacionproceso',$validacionproceso)
        ->with('marcas', $marcas)->with('vitolas', $vitolas)->withNoPagina(1);
    }

    public function validarfecha($fecha){
        $procesado= DB::table('entradasprocesadas')
        ->where('created_at', '=', $fecha)->first();
        return $procesado!=null?false:true;
    }

    
    public function store(Request $request)
    {
        if ($this->validarfecha($request->fecha)) {
        $emp = new EntradaMateriaPrima;
        $emp->codigo_materia_prima = $request->codigo;
        $emp->Libras = $request->libras;
        $emp->created_at = $request->fecha;
        $emp->observacion = $request->observacion;
        $emp->procedencia = $request->procedencia;
        $emp->save();
        return back();
        }else{
            Session::flash('flash_message', 'Fecha ya Procesada');
            return back();
        }
    }


    public function update(Request $request, EntradaMateriaPrima $entradaMateriaPrima)
    {
        $materiaprima = EntradaMateriaPrima::FindOrFail($request->id);
        $materiaprima->codigo_materia_prima = $request->codigo;
        $materiaprima->Libras = $request->libras;
        $materiaprima->observacion = $request->observacion;
        $materiaprima->procedencia = $request->procedencia;
        $materiaprima->save();
        return back();
    }

    public function destroy(Request $request)
    {
        $borrar = EntradaMateriaPrima::FindOrFail($request->id);
        $borrar->delete();
        return back();
    }


    public function procesar(Request $request)
    {
        $fecha = $request->fecha;
        $consulta= DB::table('entrada_materia_primas')->select('codigo_materia_prima',
        DB::raw('SUM(Libras) as Libras'))->where('created_at', '=', $fecha)->whereNull('desdeinventario')->groupBy('codigo_materia_prima')->get();
        foreach ($consulta as $codigoMP) {
            $materiaprima = MateriaPrima::FindOrFail($codigoMP->codigo_materia_prima);
            $existencia= $materiaprima->Libras+$codigoMP->Libras;
            $materiaprima->Libras= $existencia;
            $materiaprima->save();
        }
        DB::table('entradasprocesadas')->insert(['created_at'=>$fecha]);
        return back();
    }

    public function desaplicar(Request $request)
    {
        $fecha = $request->fecha;
        $consulta= DB::table('entrada_materia_primas')->select('codigo_materia_prima',
        DB::raw('SUM(Libras) as Libras'))->where('created_at', '=', $fecha)
        ->whereNull('desdeinventario')->groupBy('codigo_materia_prima')->get();
        foreach ($consulta as $codigoMP) {
            $materiaprima = MateriaPrima::FindOrFail($codigoMP->codigo_materia_prima);
            $existencia= $materiaprima->Libras-$codigoMP->Libras;
            $materiaprima->Libras= $existencia;
            $materiaprima->save();
        }
        DB::table('entradasprocesadas')->where('created_at', '=', $fecha)->delete();
        return back();
    }

    public function export(Request $request){
        $fecha = Carbon::parse(  $request->get("fecha1"))->format('Y-m-d');
        return (new DevolucionesMateriaPrima($fecha))
        ->download('Devoluciones Despacho.'.$fecha.'.xlsx', \Maatwebsite\Excel\Excel::XLSX);


    }

    public function EntradaBultos(Request $request){
        try {
            DB::beginTransaction();
        $fecha = $request->fecha;
        $marca = $request->marca;
        $vitola = $request->vitola;
        $obtenerbulto = DB::table('b_inv_inicials')->where('id_marca', '=', $marca)
            ->where('id_vitolas', '=', $vitola)->first();
            $valor = $request->total;
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
            DB::commit();
            return back();
            }catch (\Throwable $th) {
                DB::rollback();
                return back()->withExito('No cuenta con este producto en Stock Despacho.');
            }
    }

    function ConsultarMP($combinacion, $cantidad, $id, $marca, $vitola, $fecha){
        $detalle = DB::table('detalle_combinaciones')
        ->where('id_combinaciones', '=', $combinacion)->get();

        $nmarca = DB::table('marcas')
        ->where('id', '=', $marca)->first();

        $nvitola = DB::table('vitolas')
        ->where('id', '=', $vitola)->first();

        foreach($detalle as $val){
            $pesoReal = ($cantidad * $val->peso)/16;
            $descripcion = $cantidad .' Bultos devueltos De: '. 
            $nmarca->name.' '. $nvitola->name;
            DB::table('entrada_materia_primas')
                    ->insert(['codigo_materia_prima'=>$val->codigo_materia_prima,
                    'observacion'=>$descripcion,
                    'Libras'=>$pesoReal,'procedencia'=>'Despacho',
                    'created_at'=>$fecha]);
                }
                $update = Inventariobultosnorma::find($id);
                $update->cantidad = $update->cantidad - $cantidad;
                $update->fecha_salida = $fecha;
                $update->cant_sali = $cantidad;
                $update->save();
    }
}
