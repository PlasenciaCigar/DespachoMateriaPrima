<?php

namespace App\Http\Controllers;

use App\Models\Textura;
use Illuminate\Http\Request;

class TexturaController extends Controller
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
            $categoria="codigo_textura";
        }
        $caracteres = $request->get('busqueda');
        $Textura=Textura::where("$categoria", 'like', "%$caracteres%")->get();
        return view('Textura.TexturaMostrar', compact('Textura'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('Textura.Textura'); //
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
            'codigo_textura' => 'required|unique:texturas,codigo_textura|max:10',
            'nombre_textura' => 'required|max:25',
            'descripcion_textura' => 'required|max:255',
        ]);

    $Textura = new Textura();
    $Textura->codigo_textura = $request->input('codigo_textura');
    $Textura->nombre_textura = $request->input('nombre_textura');
    $Textura->descripcion_textura = $request->input('descripcion_textura');
    $Textura->save();

    return redirect('/Textura/index');   //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Textura  $textura
     * @return \Illuminate\Http\Response
     */
    public function show($codigo_textura)
    {
        $Textura=Textura::where('codigo_textura', '=',$codigo_textura)->first();
        return view('Textura.Textura')->with('Textura', $Textura);  //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Textura  $textura
     * @return \Illuminate\Http\Response
     */
    public function edit(Textura $textura)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Textura  $textura
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $Textura)
    {
        $Textur= Textura::where('codigo_textura', '=', $Textura)->first();
        $this->validate($request, [
            'codigo_textura' => 'required|max:10',
            'nombre_textura' => 'required|max:25',
            'descripcion_textura' => 'required|max:255',
        ]);
    //$Textura->codigo_textura = $request->input('codigo_textura');
    $Textur->nombre_textura = $request->input('nombre_textura');
    $Textur->descripcion_textura = $request->input('descripcion_textura');
    $Textur->save();

    return redirect('/Textura/index');  //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Textura  $textura
     * @return \Illuminate\Http\Response
     */
    public function destroy($Textura)
    {
        try {
            $Textura =Textura:: where ('codigo_textura','=', $Textura)->first();
             $Textura->delete();
             return redirect('/Textura/index')->with('Eliminar', 'Ok.'); //
            }
             catch (\Throwable $th) {
                return redirect('/Textura/index')->with('Eliminar', 'No.');
        }//
    }
}
