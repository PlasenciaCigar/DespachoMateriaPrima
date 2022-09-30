<?php

namespace App\Http\Controllers;

use App\Models\tipoclase;
use Illuminate\Http\Request;

class TipoclaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categoria = $request->get('filtro');
        if($categoria==null){
            $categoria="codigo_clase";
        }
        $caracteres = $request->get('busqueda');
        $tipoclase=tipoclase::where("$categoria", 'like', "%$caracteres%")->get();
        return view('tipoclase.tipoclasemostrar', compact('tipoclase'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('tipoclase.tipoclase');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'codigo_clase' => 'required|unique:tipoclases,codigo_clase|max:10',
            'nombre_clase' => 'required|max:25',
            'descripcion_clase' => 'required|max:255',
        ]);

    $tipoclase = new tipoclase;
    $tipoclase->codigo_clase = $request->input('codigo_clase');
    $tipoclase->nombre_clase = $request->input('nombre_clase');
    $tipoclase->descripcion_clase = $request->input('descripcion_clase');
    $tipoclase->save();

    return redirect('/tipoclase/index'); //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\tipoclase  $tipoclase
     * @return \Illuminate\Http\Response
     */
    public function show($codigo_clase)
    {
       $tipoclase=tipoclase::where('codigo_clase', '=',$codigo_clase)->first();
       return view('tipoclase.tipoclase')->with('tipoclase', $tipoclase); //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\tipoclase  $tipoclase
     * @return \Illuminate\Http\Response
     */
    public function edit(tipoclase $tipoclase)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\tipoclase  $tipoclase
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,  $tipoclases)
    {
        $tipoclase= tipoclase::where('codigo_clase', '=', $tipoclases)->first();
        $this->validate($request, [
            'codigo_clase' => 'required|max:10',
            'nombre_clase' => 'required|max:25',
            'descripcion_clase' => 'required|max:255',
        ]);
    $tipoclase->codigo_clase = $request->input('codigo_clase');
    $tipoclase->nombre_clase = $request->input('nombre_clase');
    $tipoclase->descripcion_clase = $request->input('descripcion_clase');
    $tipoclase->save();

    return redirect('/tipoclase/index'); //
    }
  //
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\tipoclase  $tipoclase
     * @return \Illuminate\Http\Response
     */
    public function destroy($tipoclase)
    {
        try {
        $tipoclase =tipoclase:: where ('codigo_clase','=', $tipoclase)->first();
         $tipoclase->delete();
         return redirect('/tipoclase/index')->with('Eliminar', 'Ok.'); //
        }
         catch (\Throwable $th) {
            return redirect('/tipoclase/index')->with('Eliminar', 'No.');
              }
        }
}