<?php

namespace App\Http\Controllers;

use App\combinaciones;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\MateriaPrima;
use App\Marca;
use App\Vitola;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Session;

class CombinacionesController extends Controller
{


    public function index(Request $request)
    {
        $materiaprima = MateriaPrima::all();
        $marca = Marca::all();
        $vitola = Vitola::all();
        $bultos=0;
        $combinaciones= DB::table('combinaciones')
        ->join('b_inv_inicials', 'b_inv_inicials.id', '=', 'combinaciones.bulto')
        ->join('detalle_combinaciones', 'detalle_combinaciones.id_combinaciones', '=', 'combinaciones.id')
        ->join('materia_primas', 'materia_primas.Codigo', '=', 'detalle_combinaciones.codigo_materia_prima')
        ->join('marcas', 'marcas.id', '=', 'b_inv_inicials.id_marca')
        ->join('vitolas', 'vitolas.id', '=', 'b_inv_inicials.id_vitolas')
        ->select('combinaciones.id','vitolas.name', 'marcas.name as marca', 'b_inv_inicials.id_vitolas',
        'b_inv_inicials.id_marca')
        ->where('marcas.name', 'like', '%'.$request->marca.'%')
        ->where('vitolas.name', 'like', '%'.$request->vitola.'%')
        ->GroupBy('combinaciones.id')->orderBy('marcas.name')
        ->get();

        return view('rmp.Salidas.combinaciones')->with('combinaciones',$combinaciones)->withNoPagina(1)
        ->with('materiaprima',$materiaprima)->with('marca',$marca)->with('vitola',$vitola);
    }


    function consultaCombinaciones($comb){
        $combinaciones= DB::table('detalle_combinaciones')
        ->join('combinaciones', 'combinaciones.id', '=', 'detalle_combinaciones.id_combinaciones')
        ->join('materia_primas','materia_primas.Codigo', 
        'detalle_combinaciones.codigo_materia_prima')
        ->select('detalle_combinaciones.codigo_materia_prima',
         'detalle_combinaciones.peso', 'detalle_combinaciones.id_combinaciones'
         ,'materia_primas.Descripcion', 'detalle_combinaciones.id as com')
        ->where('detalle_combinaciones.id_combinaciones', '=', $comb)
        ->get();
        return $combinaciones;
    }

    public function verdetalle(Request $request, $comb)
    {
        $combinaciones = $this->consultaCombinaciones($comb);
        return response()->json($combinaciones); 
    }
    public function create()
    {
        $combinacion = DB::table('combinaciones')->get();
        $materiaprima = MateriaPrima::all();
        $marca = Marca::all();
        $vitola = Vitola::all();
        return view('rmp.Salidas.combinacion')->with('combinacion',$combinacion)->with('materiaprima',$materiaprima)
        ->with('marca',$marca)->with('vitola',$vitola);
    }

    public function store(Request $request)
    {
        $bulto = DB::table('b_inv_inicials')->select('id')
        ->where('id_vitolas', '=',$request->vitola)
        ->where('id_marca','=',$request->marca)->first();
        if ($bulto!=null) {
            $combinaciones= new combinaciones();
            $combinaciones->bulto= $bulto->id;
            $combinaciones->save();
            $id= $combinaciones->id;
    
            $this->firstdetalle($id, $request->codigo_materia_prima, 
            $request->peso);
            
            $consulta = $this->consultaCombinaciones($combinaciones->id);
            return response()->json([$combinaciones, $consulta]);
        }else{
            return response()->json(['errors'=>true]);
        }
    }

    public function firstdetalle($id, $materiaprima, $peso)
    {
        DB::table('detalle_combinaciones')->insert(
            array(
                   'id_combinaciones'     =>   $id, 
                   'codigo_materia_prima'   =>   $materiaprima,
                   'peso'   =>   $peso
            )
       );
    }

    public function storedetalle(Request $request)
    {
        $id = $request->id_combinaciones;
        DB::table('detalle_combinaciones')->insert(
            array(
                   'id_combinaciones'     =>   $request->id_combinaciones, 
                   'codigo_materia_prima'   =>   $request->codigo_materia_prima,
                   'peso'   =>   $request->peso
            )
       );
       $consulta = $this->consultaCombinaciones($id);
        return response()->json([$id, $consulta]);
    }

    public function destroy(Request $request)
    {
        $id = $request->id_com_bo;
        $deletedetalle = DB::table('detalle_combinaciones')
        ->where('id_combinaciones', '=', $id)->delete();
        $delete = combinaciones::FindOrFail($id);
        $delete->delete();
        return back();
    }

    public function destroydetalle($codigo)
    {
        $delete = DB::table('detalle_combinaciones')
        ->where('id', '=', $codigo)->delete();
        return response()->json($codigo);

    }
}
