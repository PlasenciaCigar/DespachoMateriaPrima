<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\MateriaPrima;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\EntradaMateriaPrima;

class Kardex extends Controller
{
    public function index($codigo, Request $request){
        $fecha=$request->fecha;
        $fecha1=$request->fecha1;
        $codigo1=$request->codigo;
        if($fecha != null){
            $fecha=Carbon::parse($fecha)->format('Y-m-d');
        }else{
            $fecha = Carbon::now()->format('Y-m-d');
        }

        if($fecha1 != null){
            $fecha1= Carbon::parse($fecha1)->format('Y-m-d'); 
        }else{
            $fecha1 = Carbon::now()->format('Y-m-d');
        }

        if($codigo1 != null){
            $codigo= $codigo1; 
        }

        $entrada= DB::table('entrada_materia_primas')->join('materia_primas', 'materia_primas.Codigo', '=',
        'entrada_materia_primas.codigo_materia_prima')->select('entrada_materia_primas.*', 'materia_primas.Descripcion as nombre')
        ->where('entrada_materia_primas.codigo_materia_prima', '=', $codigo)->get();
        $saldo=0;
        
        return view('rmp.KARDEX.Kardex')->with('entrada', $entrada)->with('codigo',$codigo)->withNoPagina(1)
        ->with('fecha',$fecha)->with('fecha1',$fecha1)->with('saldo',$saldo);

        //->whereBetween('entrada_materia_primas.created_at', [$fecha, $fecha1])

    }
}
