<?php
    
namespace App\Http\Controllers;

use App\Models\Finca;
use Illuminate\Http\Request;

class FincaController extends Controller
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
            $categoria="codigo_finca";
        }
        $caracteres = $request->get('busqueda');
        $Finca = Finca::where("$categoria", 'like', "%$caracteres%")->get();
     return view ('Finca.fincamost', compact('Finca')); 
     
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('Finca.finca');
        
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
            'codigo_finca' => 'required|unique:fincas,codigo_finca|max:10',
            'nombre_finca' => 'required|max:25',
            'descripcion_finca' =>'required|max:255',
        ]);

    $Finca = new Finca;
    $Finca->codigo_finca = $request->input('codigo_finca');
    $Finca->nombre_finca = $request->input('nombre_finca');
    $Finca->descripcion_finca = $request->input('descripcion_finca');
    $Finca->save();

    return redirect('/fincas/index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Finca  $finca
     * @return \Illuminate\Http\Response
     */
    public function show($finca)
    {
        $fincas = Finca::where('codigo_finca', '=', $finca)->first();
        return view('Finca.finca')->with('fincas',$fincas);
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Finca  $finca
     * @return \Illuminate\Http\Response
     */
    public function edit(Finca $finca)
    {    
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Finca  $finca
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $fincas)
    {
        $finca =finca:: where ('codigo_finca','=', $fincas)->first();
        $this->validate($request, [
            'codigo_finca' => 'required|max:10',
            'nombre_finca' => 'required|max:25',
            'descripcion_finca' =>'required|max:255',
        ]);

    $finca->codigo_finca = $request->input('codigo_finca');
    $finca->nombre_finca = $request->input('nombre_finca');
    $finca->descripcion_finca = $request->input('descripcion_finca');
    $finca->save(); 
    return redirect('/fincas/index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Finca  $finca
     * @return \Illuminate\Http\Response
     */
    public function destroy( $finca)
    {

        try {
        $finca =Finca:: where ('codigo_finca','=', $finca)->first();
        $finca->delete();
        return redirect('/fincas/index')->with('Eliminar', 'Ok.');
      
} 
catch (\Throwable $th) {
    return redirect('/fincas/index')->with('Eliminar', 'No.');
      }
    }
  }