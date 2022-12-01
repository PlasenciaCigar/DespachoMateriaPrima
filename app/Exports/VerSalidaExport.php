<?php

namespace App\Exports;

use App\CapaEntrega;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class VerSalidaExport implements FromView, ShouldAutoSize
{

    use Exportable;

    protected $fecha;
    public function __construct(String $fecha )
    {

        $this->fecha = $fecha;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        $data = DB::table('salida_det_mp')
            ->where('salida_det_mp.created_at', '=', $this->fecha)
            ->where('salida_det_mp.observacion', '=', 'A Despacho')
            ->join('materia_primas', 'materia_primas.Codigo',
            'salida_det_mp.codigo_materia_prima')
            ->select('salida_det_mp.*', 'materia_primas.Descripcion')->get();
            
        $total= DB::table('salida_det_mp')
        ->where('created_at', '=', $this->fecha)
        ->where('salida_det_mp.observacion', '=', 'A Despacho')
        ->sum('peso');

        return view('ReportesExcel.ReporteSalidaMP', [
            'dato'=>$data, 'fecha'=>$this->fecha, 'total'=>$total
        ]);
    }
}
