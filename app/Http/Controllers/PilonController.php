<?php

namespace App\Http\Controllers;

use App\Models\Pilon;
use App\Models\Detalle_pilon;
use App\Models\Detalle_dato_pilon;
use App\Models\Finca;
use App\Models\Textura;
use App\Models\Ubicacion;
use App\Models\tipoclase;
use App\Models\Procedencia;
use App\Models\Variedad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\UserInterface;
use App\Models\User;
use DateTime;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class PilonController extends Controller
{ 
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function indexGerentes(Request $request)
    {
       // $pilon = Pilon::all();
       $categoria = $request->get('filtro');
       $region = $request->get('filtro1');
       if($categoria==null){
           $categoria="codigo_pilon";
       }
       if($region==null){
        $region="Gualiqueme";
    }
       $procedencias = DB::table('procedencias')->select('nombre')
       ->OrderByRaw('nombre desc')->get();

       $caracteres = $request->get('busqueda');
       $suc= Auth::user()->sucursal;
       $now=Carbon::now();
       $now = $now->format('d-m-Y');

       if($categoria=="contenido"){
        $nuevo= explode('*', $caracteres);
        $first = $nuevo[0];
        $second = $nuevo[1];
        $three = $nuevo[2];
        $pilon = DB::table('pilons')
        ->join('procedencias', 'procedencias.id','=',
         'pilons.sucursal_id')
         ->join('ubicacions', 'ubicacions.id', '=', 'pilons.ubicacion')
         ->join('detalle_pilons', 'detalle_pilons.pilon_id', '=', 'pilons.id')
         ->join('tipoclases', 'tipoclases.codigo_clase', '=', 'detalle_pilons.codigo_clase')
         ->join('variedads', 'variedads.codigo_variedad', '=', 'detalle_pilons.codigo_variedad')
         ->join('fincas', 'fincas.codigo_finca', '=', 'detalle_pilons.codigo_finca')
       // ->selectRaw('DATEDIFF(pilons.Fecha_datos_pilones, pilons.Fecha_empilonamiento) as rer')
         ->select('pilons.*', 'procedencias.nombre','pilons.id as tema', 'ubicacions.codigo_ubicacion as cod', DB::raw('DATEDIFF(now(), pilons.Fecha_datos_pilones) as rer'), DB::raw('DATEDIFF(now(), pilons.Fecha_empilonamiento) as empilonamiento'),
         DB::raw('(select SUM(peso) from detalle_pilons where pilon_id= tema) as suma'))
         ->where('procedencias.nombre','=', "$region")->where('variedads.nombre_variedad', 'like', "%$first%")->where('tipoclases.nombre_clase', 'like', "%$second%")->where('fincas.nombre_finca', 'like', "%$three%")
         ->get();
    }else{
        $pilon = DB::table('pilons')
        ->join('procedencias', 'procedencias.id','=',
         'pilons.sucursal_id')
         ->join('ubicacions', 'ubicacions.id', '=', 'pilons.ubicacion')
       // ->selectRaw('DATEDIFF(pilons.Fecha_datos_pilones, pilons.Fecha_empilonamiento) as rer')
         ->select('pilons.*', 'pilons.id as tema', 'procedencias.nombre', 'ubicacions.codigo_ubicacion as cod', DB::raw('DATEDIFF(now(), pilons.Fecha_datos_pilones) as rer'), DB::raw('DATEDIFF(now(), pilons.Fecha_empilonamiento) as empilonamiento'),
         DB::raw('(select SUM(peso) from detalle_pilons where pilon_id= tema) as suma'))
         ->where('procedencias.nombre','=', "$region")->where("$categoria", 'like', "%$caracteres%")
         ->get();
        }
        $ubicacion = Ubicacion::all();
        $finca = Finca::all();
        $clase = tipoclase::all();
        $clase = Variedad::all();
        return view ('pilones.PilonAll',['pilon'=>$pilon, 'procedencias'=>$procedencias]); 
        //return $parametro;
         
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        
       // $pilon = Pilon::all();
       $categoria = $request->get('filtro');
       if($categoria==null){
           $categoria="codigo_pilon";
       }
       $caracteres = $request->get('busqueda');
       $suc= Auth::user()->sucursal;
       $now=Carbon::now();
       $now = $now->format('d-m-Y');
       if($categoria=="contenido"){
        $nuevo= explode('*', $caracteres);
        $first = $nuevo[0];
        $second = $nuevo[1];
        $three = $nuevo[2];
        $cuatro = $nuevo[3];
        $pilon = DB::table('pilons')
        ->join('procedencias', 'procedencias.id','=',
         'pilons.sucursal_id')
         ->join('ubicacions', 'ubicacions.id', '=', 'pilons.ubicacion')
         ->join('detalle_pilons', 'detalle_pilons.pilon_id', '=', 'pilons.id')
         ->join('tipoclases', 'tipoclases.codigo_clase', '=', 'detalle_pilons.codigo_clase')
         ->join('variedads', 'variedads.codigo_variedad', '=', 'detalle_pilons.codigo_variedad')
         ->join('texturas', 'texturas.codigo_textura', '=', 'detalle_pilons.codigo_textura')
         ->join('fincas', 'fincas.codigo_finca', '=', 'detalle_pilons.codigo_finca')
       // ->selectRaw('DATEDIFF(pilons.Fecha_datos_pilones, pilons.Fecha_empilonamiento) as rer')
       
         ->select('pilons.*','pilons.id as tema', 'procedencias.nombre', 'ubicacions.codigo_ubicacion as cod', DB::raw('DATEDIFF(now(), pilons.Fecha_datos_pilones) as rer'), DB::raw('DATEDIFF(now(), pilons.Fecha_empilonamiento) as empilonamiento'),
         DB::raw('(select SUM(peso) from detalle_pilons where pilon_id= tema) as suma'))
         ->where('variedads.nombre_variedad', 'like', "%$three%")->where('texturas.nombre_textura', 'like', "%$second%")->where('tipoclases.nombre_clase', 'like', "%$first%")->where('fincas.nombre_finca', 'like', "%$cuatro%")
         ->OrderByRaw('Fecha_datos_pilones DESC')->get();
    }else{
        $pilon = DB::table('pilons')
        ->join('procedencias', 'procedencias.id','=',
         'pilons.sucursal_id')->where('sucursal_id', '=', "$suc")
         ->join('ubicacions', 'ubicacions.id', '=', 'pilons.ubicacion')
       // ->selectRaw('DATEDIFF(pilons.Fecha_datos_pilones, pilons.Fecha_empilonamiento) as rer')
         ->select('pilons.*','pilons.id as tema', 'procedencias.nombre', 'ubicacions.codigo_ubicacion as cod', DB::raw('DATEDIFF(now(), pilons.Fecha_datos_pilones) as rer'), DB::raw('DATEDIFF(now(), pilons.Fecha_empilonamiento) as empilonamiento'),
         DB::raw('(select SUM(peso) from detalle_pilons where pilon_id= tema) as suma'))
         ->where("$categoria", 'like', "%$caracteres%")->OrderByRaw('Fecha_datos_pilones DESC')->get();
    }
        $ubicacion = Ubicacion::all();
        $finca = Finca::all();
        $clase = tipoclase::all();
        $clase = Variedad::all();
        return view ('pilones.pilonmost',['pilon'=>$pilon]); 
       // return $now;
         
    }

    public function grafico()
    {
        $pilon = Pilon::all();
        $ubicacion = Ubicacion::all();
        $finca = Finca::all();
        $clase = tipoclase::all();
        $clase = Variedad::all();
        return view ('Reportes.Grafico',['pilon'=>$pilon]); 
         
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    { 
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function pilonindex()
    {
        $suc= Auth::user()->sucursal;
        $pilon = Pilon::all();
        $ubicacion = Ubicacion::where('procedencias_id', '=', $suc)->get();
        $finca = Finca::all();
        $clase = tipoclase::all();
        $variedad = Variedad::all();
        $textura = Textura::all();
        $true = 1;
        $mostrar=0;
        return view('pilones.pilon', ['ubicacion'=>$ubicacion, 'finca'=>$finca, 'textura'=>$textura,
        'clase'=>$clase, 'variedad'=>$variedad, 'true'=>$true, 'mostrar'=>$mostrar]);
        
    }

    //----------------------------------------------

    public function detallesave(Request $request)
    {
        $detalle=new Detalle_pilon();
        $detalle->codigo_clase = $request->codigo_clase;
        $detalle->codigo_finca = $request->codigo_finca;
        $detalle->codigo_variedad = $request->codigo_variedad;
        $detalle->codigo_textura = $request->codigo_textura;
        $detalle->pilon_id = $request->pilon_id;
        $detalle->peso = $request->peso;
        $detalle->save();
        
    }



    public function verDetalles($id)
    {
        //$detalle=Detalle_pilon::where('pilon_id','=', $id)->get();
        $detalle = DB::table('detalle_pilons')
        ->join('variedads', 'variedads.codigo_variedad', '=', 'detalle_pilons.codigo_variedad')
        ->join('tipoclases', 'tipoclases.codigo_clase', '=', 'detalle_pilons.codigo_clase')
        ->join('texturas', 'texturas.codigo_textura', '=', 'detalle_pilons.codigo_textura')
        ->join('fincas', 'fincas.codigo_finca', '=', 'detalle_pilons.codigo_finca')
        ->where('pilon_id', '=', $id)
        ->select('detalle_pilons.*', 'variedads.nombre_variedad as varied', 'tipoclases.nombre_clase as class', 'texturas.nombre_textura as text',
    'fincas.nombre_finca as fincas')->get();

        return $detalle;
        
    }

    public function destroyDetalle($pilon)
    {
        $detalle = Detalle_pilon::findOrFail($pilon);
        $detalle->delete();
        return $detalle;

        /*$pilon =Pilon:: where ('codigo_pilon','=', $pilon)->first();
         $pilon->delete();
         return redirect('/pilon/index');*/
        
    }


    public function ValidarUbicacion($ubi)
    {
        $ubicacion = Pilon::where('ubicacion', '=', $ubi)->first();
        return $ubicacion==null?true:false;

        /*$pilon =Pilon:: where ('codigo_pilon','=', $pilon)->first();
         $pilon->delete();
         return redirect('/pilon/index');*/
        
    }


    //-----------------------------------------------------------
    public function store(Request $request)
    {
        //
        $this->validate($request, [
            'codigo_pilon' => 'required',
            'descripcion_pilon'=> 'required',
            'Fecha_empilonamiento'=> 'required',
            'ubicacion'=> 'required',
        ]);

    /*$pilon = new pilon; 
    $pilon->codigo_pilon = $request->input('codigo_pilon');
    $pilon->descripcion_pilon = $request->input('descripcion_pilon');
    $pilon->save();
    $codigo_pilon=o;*/
    if($this->ValidarUbicacion($request->input('ubicacion'))){
        $id = pilon::create([
            'codigo_pilon' => $request->codigo_pilon,
            'descripcion_pilon' => $request->descripcion_pilon,
            'ubicacion' => $request->input('ubicacion'),
            'Fecha_datos_pilones' => $request->input('fecha_inicio'),
            'sucursal_id' => $request->sucursal,
            'Fecha_empilonamiento' => $request->input('Fecha_empilonamiento')
        ]);
    
        if($id==null){
            $mostrar = 0;
        }else{
            $mostrar = $id->id;
    
        }
        $update = Ubicacion::findOrFail($request->input('ubicacion'));
        $update->estado_ubicacion = 0;
        $update->save(); 
    
    
    
    
        //required min=<?php $hoy=date("Y-m-d"); echo $hoy;
        //value="{{date('Y-m-d', strtotime($pilon->fecha_datos_pilones))}}"
       // $pilon =Pilon::findOrFail($mostrar);
        $pilon = DB::table('pilons')->
     join('ubicacions', 'ubicacions.id', '=', 'pilons.ubicacion')->where('pilons.id', '=', $mostrar)
     ->select('pilons.*', 'ubicacions.codigo_ubicacion as ubiselect')->first();
        $ubicacion = Ubicacion::all();
        $finca = Finca::all();
        $clase = tipoclase::all();
        $variedad = Variedad::all();
        $textura = Textura::all();
        $true = 0; 
        return view('pilones.pilon', ['ubicacion'=>$ubicacion, 'finca'=>$finca,'textura'=>$textura,
        'clase'=>$clase, 'variedad'=>$variedad, 'true'=>$true, 'mostrar'=>$mostrar, 'pilon'=>$pilon]);

    }else{
        \Session::flash('message', 'Esta Ubicacion ya esta en uso');
        return $this->pilonindex();

    }

   
    //return $mostrar;

        }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\pilon  $pilon
     * @return \Illuminate\Http\Response
     */
    public function show($codigo_pilon)
    {
     $pilon = DB::table('pilons')->
     join('ubicacions', 'ubicacions.id', '=', 'pilons.ubicacion')->where('pilons.id', '=', $codigo_pilon)
     ->select('pilons.*', 'ubicacions.codigo_ubicacion as ubiselect')->first();
     //return view('Pilones.pilon')->with('pilon',$pilon);  
     
     $suc= Auth::user()->sucursal;
        $ubicacion = Ubicacion::where('procedencias_id', '=', $suc)->get();
        $finca = Finca::all();
        $clase = tipoclase::all();
        $variedad = Variedad::all();
        $textura = Textura::all();
        $true = 2;
        $mostrar=$codigo_pilon;
        return view('pilones.pilon', ['ubicacion'=>$ubicacion, 'finca'=>$finca, 'textura'=>$textura,
        'clase'=>$clase, 'variedad'=>$variedad, 'true'=>$true, 'mostrar'=>$mostrar, 'pilon'=>$pilon]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\pilon  $pilon
     * @return \Illuminate\Http\Response
     */
    public function edit(pilon $pilon)
    {
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\pilon $pilon
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $pilones)
    {
        $pilon =pilon:: where ('id','=', $pilones)->first();
        $this->validate($request, [
            'codigo_pilon' => 'required',
            'descripcion_pilon'=> 'required',
            'Fecha_empilonamiento'=> 'required',
            'ubicacion'=> 'required',
        ]);
        if ($request->input('ubicacion')==$request->input('disponible')) {
            $pilon->codigo_pilon = $request->input('codigo_pilon');
        $pilon->descripcion_pilon = $request->input('descripcion_pilon');
        $pilon->ubicacion = $request->input('ubicacion');
        $pilon->Fecha_datos_pilones = $request->input('fecha_inicio');
        $pilon->sucursal_id = $request->input('sucursal');
        $pilon->Fecha_empilonamiento= $request->input('Fecha_empilonamiento');
        $pilon->save(); 
        $updateLibre = Ubicacion::findOrFail($request->input('disponible'));
        $updateLibre->estado_ubicacion = 1;
        $updateLibre->save();
        $update = Ubicacion::findOrFail($request->input('ubicacion'));
        $update->estado_ubicacion = 0;
        $update->save(); 
        return $this->show($pilones);
        }else{
        if($this->ValidarUbicacion($request->input('ubicacion'))){
        $pilon->codigo_pilon = $request->input('codigo_pilon');
        $pilon->descripcion_pilon = $request->input('descripcion_pilon');
        $pilon->ubicacion = $request->input('ubicacion');
        $pilon->Fecha_datos_pilones = $request->input('fecha_inicio');
        $pilon->sucursal_id = $request->input('sucursal');
        $pilon->Fecha_empilonamiento= $request->input('Fecha_empilonamiento');
        $pilon->save(); 
        $updateLibre = Ubicacion::findOrFail($request->input('disponible'));
        $updateLibre->estado_ubicacion = 1;
        $updateLibre->save();
        $update = Ubicacion::findOrFail($request->input('ubicacion'));
        $update->estado_ubicacion = 0;
        $update->save(); 
        return $this->show($pilones);
        }else{
            \Session::flash('message', 'Esta Ubicacion ya esta en uso');
        return $this->show($pilones);

        }
    }
        //return redirect::route('pilon.show',['pilones'=>$pilones]);
       // return redirect('/pilon/edit/{'+$pilones+'}');  
       
        //required min=<?php $hoy=date("Y-m-d"); echo $hoy;
        //value="{{date('Y-m-d', strtotime($pilon->fecha_datos_pilones))}}"
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\pilon  $pilon
     * @return \Illuminate\Http\Response
     */
    public function destroy( $pilon)
    {
        $dato= Pilon::where('id', '=', $pilon)->select('ubicacion')->first();//->join('pilons', 'ubicacions.id', '=', 'pilons.ubicacion')
        // ->where('pilons.id', '=', "$pilon")->select('ubicacions.codigo_ubicacion')->first();
           // $update = Ubicacion::findOrFail($request->input('ubicacion'));
         /*  $update= Ubicacion::where('codigo_ubicacion','=',$dato)->first();
         $update->estado_ubicacion = 1;
         $update->save(); */
            $borrar= Pilon::findOrFail($pilon);
            $detalle1 = Detalle_pilon::where('pilon_id', '=', "$pilon")->get();
            foreach ($detalle1 as $value) {
                $detall = Detalle_pilon::where('pilon_id', '=', "$pilon")->first();
                $detall->delete();
            }
            $detalle2 = Detalle_dato_pilon::where('pilon_id', '=', "$pilon")->get();
            foreach ($detalle2 as $value) {
                $detalle = Detalle_dato_pilon::where('pilon_id', '=', "$pilon")->first();
                $detalle->delete();
            }
            //$detalle2->delete();
           $borrar->delete();
         
            return redirect('/pilon/index')->with('Eliminar', 'Ok.');
        /*} catch (\Throwable $th) {
            return redirect('/pilon/index')->with('Eliminar', 'No.');
            //throw $th;
        }*/
        //$pilon =Pilon:: where ('codigo_pilon','=', $pilon)->first();
       
    }
}
