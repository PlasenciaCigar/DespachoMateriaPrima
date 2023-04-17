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
class SalidaDetallaMateriaPrima implements FromView, ShouldAutoSize
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
        $data = DB::table('salidas_materia_primas')
        ->join('combinaciones', 'combinaciones.id', '=', 'salidas_materia_primas.id_combinacion')
        ->join('b_inv_inicials', 'b_inv_inicials.id', 'combinaciones.bulto')
        ->join('vitolas', 'vitolas.id', 'b_inv_inicials.id_vitolas')
        ->join('marcas', 'marcas.id', 'b_inv_inicials.id_marca')
        ->join('detalle_combinaciones', 'salidas_materia_primas.id_combinacion', '=', 'detalle_combinaciones.id_combinaciones')
        ->select('salidas_materia_primas.*', 'salidas_materia_primas.id as salida',
         'vitolas.name as vitola', 'marcas.name as marca',
          'vitolas.id as v_id','marcas.id as m_id',
          'combinaciones.id as combinacion', DB::raw('sum(detalle_combinaciones.peso) as totalpeso'))
        ->where('salidas_materia_primas.created_at', 'LIKE', '%'.$this->fecha.'%')
        ->groupby('salidas_materia_primas.id')
        ->get();
            
        $total= DB::table('salida_det_mp')
        ->where('created_at', '=', $this->fecha)
        ->where('salida_det_mp.observacion', '=', 'A Despacho')
        ->sum('peso');

        return view('ReportesExcel.ReporteSalidaDetalladoMP', [
            'dato'=>$data, 'fecha'=>$this->fecha, 'total'=>$total
        ]);
    }
}
