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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
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
        ->GroupBy('combinaciones.id')->orderBy('combinaciones.id')
        ->get();

        return view('rmp.Salidas.combinaciones')->with('combinaciones',$combinaciones)->withNoPagina(1)
        ->with('materiaprima',$materiaprima)->with('marca',$marca)->with('vitola',$vitola);
    }

    public function verdetalle(Request $request, $comb)
    {
        $codigo = $request->id_combinacion;
        $combinaciones= DB::table('detalle_combinaciones')
        ->join('combinaciones', 'combinaciones.id', '=', 'detalle_combinaciones.id_combinaciones')
        ->select('detalle_combinaciones.codigo_materia_prima', 'detalle_combinaciones.peso', 'detalle_combinaciones.id_combinaciones')
        ->where('detalle_combinaciones.id_combinaciones', '=', $comb)
        ->get();
        //$response['data'] = $combinaciones;

        return response()->json($combinaciones); 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $combinacion = DB::table('combinaciones')->get();
        $materiaprima = MateriaPrima::all();
        $marca = Marca::all();
        $vitola = Vitola::all();
        return view('rmp.Salidas.combinacion')->with('combinacion',$combinacion)->with('materiaprima',$materiaprima)
        ->with('marca',$marca)->with('vitola',$vitola);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $bulto = DB::table('b_inv_inicials')->select('id')
        ->where('id_vitolas', '=',$request->vitola)->where('id_marca','=',$request->marca)->first();
        $combinaciones= new combinaciones();
        $combinaciones->bulto= $bulto->id;
        $combinaciones->save();
        $id= $combinaciones->id;
        $this->firstdetalle($id, $request->codigo_materia_prima, $request->peso);
        return response()->json($combinaciones);
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
        DB::table('detalle_combinaciones')->insert(
            array(
                   'id_combinaciones'     =>   $request->id_combinaciones, 
                   'codigo_materia_prima'   =>   $request->codigo_materia_prima,
                   'peso'   =>   $request->peso
            )
       );
        return response()->json($request->id_combinaciones);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\combinaciones  $combinaciones
     * @return \Illuminate\Http\Response
     */
    public function show(combinaciones $combinaciones)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\combinaciones  $combinaciones
     * @return \Illuminate\Http\Response
     */
    public function edit(combinaciones $combinaciones)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\combinaciones  $combinaciones
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, combinaciones $combinaciones)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\combinaciones  $combinaciones
     * @return \Illuminate\Http\Response
     */
    public function destroy(combinaciones $combinaciones)
    {
        //
    }
}
