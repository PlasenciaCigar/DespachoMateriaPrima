<?php

namespace App\Http\Controllers;

use App\EntradaMateriaPrima;
use App\MateriaPrima;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
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
        ->with('fecha',$fecha)->with('materiaprima',$materiaprima)->with('validacionproceso',$validacionproceso)->withNoPagina(1);
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

    /**
     * Display the specified resource.
     *
     * @param  \App\EntradaMateriaPrima  $entradaMateriaPrima
     * @return \Illuminate\Http\Response
     */
    public function show(EntradaMateriaPrima $entradaMateriaPrima)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\EntradaMateriaPrima  $entradaMateriaPrima
     * @return \Illuminate\Http\Response
     */
    public function edit(EntradaMateriaPrima $entradaMateriaPrima)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\EntradaMateriaPrima  $entradaMateriaPrima
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\EntradaMateriaPrima  $entradaMateriaPrima
     * @return \Illuminate\Http\Response
     */
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
}
