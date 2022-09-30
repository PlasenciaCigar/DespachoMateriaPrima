<?php
    
namespace App\Http\Controllers;

use App\Models\Reporte;
use Illuminate\Http\Request;

class ReporteController extends Controller{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function generar()
    {
        $Pilones = \DB::table('pilons')
        ->select(['id','codigo_pilon','descripcion_pilon','Fecha_datos_pilones','ubicacion','sucursal_id'])
        ->get();
        $view = \View::make('Reportes.Reporte', compact('Pilones'))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        return $pdf->stream('informe'.'.pdf');
    }
}
