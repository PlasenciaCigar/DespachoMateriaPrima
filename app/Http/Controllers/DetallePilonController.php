<?php

namespace App\Http\Controllers;

use App\Models\Detalle_pilon;
use App\Models\Detalle_dato_pilon;
use App\Models\Pilon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetallePilonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

     public function mostrar($id){
         //$id=1;
       // $datos = Detalle_dato_pilon::join('')->where('pilon_id', '=', $id)->get();
        $datos= DB::table('detalle_dato_pilons')
        ->join('pilons', 'pilons.id', '=', 'detalle_dato_pilons.pilon_id')
        ->where('detalle_dato_pilons.pilon_id', '=', $id)->select('detalle_dato_pilons.*',
        'pilons.Fecha_empilonamiento as empi')->get();
        $nueva_agenda=[];

        foreach($datos as $value){
            
            $nueva_agenda[] = [
                "id"=> $value->id,
                "end"=>$value->empi,
                "start"=> $value->fecha_detalle,
                $vir= $value->virado==1 ? "virado":"",
                $moj= $value->mojado==1 ? "mojado":"",
                $fum= $value->fumigado==1 ? "fumigado":"", //$value->mojado,
                "title"=> $value->temperatura." °F"."\n". $vir."\n".$moj."\n". $fum,
                //."°F",
                "extendedProps"=>[
                    "virado"=> $value->virado,
                    "mojado"=>$value->mojado,
                    "fumigado"=> $value->fumigado,
                    "pilon_id"=> $value->pilon_id,
                    "Fecha_empilonamiento"=>$value->empi
                ]
                //"title"=> $value->virado,
                //"borderColor"=> $value->mojado,
               // "backgroundColor"=> $value->fumigado == null: "";

            ];
        }
        return response()->json($nueva_agenda);
     }
    public function index($id)
    {
        $datos = Detalle_dato_pilon::where('pilon_id', '=', $id)->get();
        $pilon = Pilon::where('id', '=', $id)->first();
       
        return view('Detalle_Dato_Pilon.Calendario', ['datos'=>$datos, 'id'=>$id, 'pilon'=>$pilon]);
        //return $id;
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /*$this->validate($request, [
            'fecha_detalle' => 'required|unique:detalle_dato_pilons,fecha_detalle',
        ]);*/
        if($this->validarFechas($request->fecha_detalle, $request->pilon_id)){
        $detalle=new Detalle_dato_pilon();
        $detalle->fecha_detalle = $request->fecha_detalle;
        $detalle->temperatura = $request->temperatura;
        $detalle->virado = $request->virado;
        $detalle->mojado = $request->mojado;
        $detalle->fumigado = $request->fumigado;

        $detalle->pilon_id = $request->pilon_id;
        $detalle->save();
        return response()->json(["ok"=>true]);
       // $detalle->save();
    }else{
        return response()->json(["ok"=>false]);
    }
}

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Detalle_pilon  $detalle_pilon
     * @return \Illuminate\Http\Response
     */
    public function show(Detalle_pilon $detalle_pilon)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Detalle_pilon  $detalle_pilon
     * @return \Illuminate\Http\Response
     */
    public function edit(Detalle_pilon $detalle_pilon)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Detalle_pilon  $detalle_pilon
     * @return \Illuminate\Http\Response
     */
    public function validarFechas( $fecha, $pilon_id)
    {
        $validar = Detalle_dato_pilon::where('pilon_id', '=', $pilon_id)
        ->where('fecha_detalle', '=', $fecha)
        ->first();
        return $validar ==null?true:false;
    }
    public function update(Request $request, $detalle_pilon)
    {
       /* $this->validate($request, [
            //'fecha_detalle' => 'required|unique:detalle_dato_pilons,fecha_detalle',
        ]);*/
        $detalle = Detalle_dato_pilon::findOrFail($detalle_pilon);
        $detalle->fecha_detalle = $request->fecha_detalle;
        $detalle->temperatura = $request->temperatura;
        $detalle->virado = $request->virado;
        $detalle->mojado = $request->mojado;
        $detalle->fumigado = $request->fumigado;
        $detalle->pilon_id = $request->pilon_id;
        $detalle->save();
        return response()->json(["ok"=>true]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Detalle_pilon  $detalle_pilon
     * @return \Illuminate\Http\Response
     */
    public function destroy($detalle_pilon)
    {
        $detalles = Detalle_dato_pilon::findOrFail($detalle_pilon);
        $detalles->delete();
        return response()->json(["ok"=>true]);
    }
}
