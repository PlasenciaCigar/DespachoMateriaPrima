<?php

namespace App\Http\Controllers;
//use App\Support\Collection;
use Illuminate\Support\Collection;
use App\Models\Detalle_dato_pilon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Chart;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;


class DetalleDatoPilonController extends Controller
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

    public function all(Request $request, $ids)
    {
       
       // $temp = Detalle_Dato_Pilon::all();
       $temperatu=[];
       $virado= DB::table('detalle_dato_pilons')->selectRaw(DB::raw('SUM(virado) as ot'))
       ->where('pilon_id', '=', $ids)->first();
       $fumigados= DB::table('detalle_dato_pilons')->selectRaw(DB::raw('SUM(fumigado) as ot2'))
       ->where('pilon_id', '=', $ids)->first();
       $mojados= DB::table('detalle_dato_pilons')->selectRaw(DB::raw('SUM(mojado) as ot3'))
       ->where('pilon_id', '=', $ids)->first();
       $temp = Detalle_dato_pilon::all();
        $temperatura = DB::table('detalle_dato_pilons')->where('pilon_id', '=', $ids)
        ->select('temperatura', 'fecha_detalle')
        ->orderByRaw('fecha_detalle ASC')// DB::raw('count(*) as total'))
      // ->groupBy('fecha_detalle')
        ->pluck('temperatura', 'fecha_detalle')->all();
        $myCollectionObj = collect($temperatura);
        $vir= DB::table('detalle_dato_pilons')->select('temperatura','virado', 'fecha_detalle')->where('pilon_id','=',$ids)
        ->orderByRaw('fecha_detalle ASC')->get();
        $arre=[];
        foreach ($vir as $key => $value) {
            $arre[]= [$value->virado==1 ? "$value->temperatura":"null"];
        }
  
        $data = $this->get($myCollectionObj);
        

        $fechas = DB::table('detalle_dato_pilons')->select('fecha_detalle')
        ->orderByRaw('fecha_detalle ASC')// DB::raw('count(*) as total'))
      // ->groupBy('fecha_detalle')
        ->pluck('fecha_detalle')->all();
        $chart = new Detalle_dato_pilon();
        $chart->labels = (array_keys($temperatura));
        $chart->dataset = (array_values($temperatura));
        $chart->vir = (array_values($arre));
       // return view('Reportes.Grafico', ['virado'=>$virado, 'chart'=>new LengthAwarePaginator($chart->take($perPage), $chart->count(), $perPage, $page, $options)]);
        return view('Reportes.Grafico', compact('chart', 'virado', 'mojados', 'fumigados')); 
//return view('charts.index', compact('chart'));
//return Response::json($results);

    }
    public function get($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }

    public function alle(Request $request)
    {
       
       // $temp = Detalle_Dato_Pilon::all();
        $users = DB::table('Detalle_Dato_Pilons')->select('temperatura', DB::raw('count(*) as total'))
        ->groupBy('temperatura')
        ->pluck('total', 'temperatura')->all();

        $chart = new Detalle_Dato_Pilon;
        $chart->labels = (array_keys($users));
        $chart->dataset = (array_values($users));
        return $chart;//view('Reportes.Grafico', compact('chart')); 
//return view('charts.index', compact('chart'));

    }

    public function index()
    {
        return view('Detalle_Dato_Pilon.Calendario');
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Detalle_dato_pilon  $detalle_dato_pilon
     * @return \Illuminate\Http\Response
     */
    public function show(Detalle_dato_pilon $detalle_dato_pilon)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Detalle_dato_pilon  $detalle_dato_pilon
     * @return \Illuminate\Http\Response
     */
    public function edit(Detalle_dato_pilon $detalle_dato_pilon)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Detalle_dato_pilon  $detalle_dato_pilon
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Detalle_dato_pilon $detalle_dato_pilon)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Detalle_dato_pilon  $detalle_dato_pilon
     * @return \Illuminate\Http\Response
     */
    public function destroy(Detalle_dato_pilon $detalle_dato_pilon)
    {
        //
    }
}
