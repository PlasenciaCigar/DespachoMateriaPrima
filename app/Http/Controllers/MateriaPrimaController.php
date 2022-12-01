<?php

namespace App\Http\Controllers;

use App\MateriaPrima;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Session;
use App\Exports\MateriaPrimaExport;
use App\Imports\MateriaPrimaImport;
use Maatwebsite\Excel\Facades\Excel;

class MateriaPrimaController extends Controller
{

    public function index(Request $request)
    {
        $fecha = $request->get("fecha");
        $descripcion = $request->get("descripcion");
        
        $materiaprima = MateriaPrima::where('created_at', 'LIKE', '%'.$fecha.'%')
        ->where('Descripcion', 'LIKE', '%'.$descripcion.'%')->get();

        $total = MateriaPrima::where('created_at','LIKE','%'.$fecha.'%')
        ->where('Descripcion', 'LIKE', '%'.$descripcion.'%')->sum('Libras');
        
        return view("rmp.Materia_prima")->with('materiaprima', $materiaprima)
        ->with('total',$total)->withNoPagina(1)->with('fecha', $fecha);
    }

    public function validacionCodigo($codigo){
        $validar = MateriaPrima::where($codigo);
        return $codigo!=null ? true : false;
        }

    public function store(Request $request)
    {
        if($this->validacionCodigo($request->codigo)){
        $materia_prima= new MateriaPrima;

        $materia_prima->Codigo = $request->codigo;
        $materia_prima->Descripcion = $request->descripcion;
        $materia_prima->Libras = $request->libras;
        $materia_prima->created_at = $request->fecha;
        $materia_prima->save();
        return back();
    }else{
        Session::flash('flash_message', 'YA EXISTE ESTE PRODUCTO');
        return back();
    }
    }

    public function update(Request $request)
    {
        $materiaprima = MateriaPrima::FindOrFail($request->codigo);
        $materiaprima->Descripcion = $request->descripcion;
        $materiaprima->Libras = $request->libras;
        $materiaprima->save();
        return back();
    }

    public function export(MateriaPrima $materiaPrima)
    {
        return Excel::download(new MateriaPrimaExport, 'materiaprima.xlsx');

    }

    public function importMP() 
    {
        Excel::import(new MateriaPrimaImport,'C:\wamp64\www\DespachoMateriaPrima\public\mp.xlsx');
        return redirect('/rmp/ligas')->with('success', 'All good!');
    }
}
