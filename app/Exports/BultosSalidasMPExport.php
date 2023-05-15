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
class BultosSalidasMPExport implements FromView, ShouldAutoSize
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
        $data = DB::select('SELECT m.name as marcas, v.name, sum(peso) as peso,
        codigo_mp as Codigo,
        Descripcion, sum(bultos) as bultos
        from salida_despacho_mp as sal
        inner join marcas as m on m.id = sal.marca
        inner join vitolas as v on v.id = sal.vitola
        inner join materia_primas as mp on mp.Codigo = sal.codigo_mp
        where sal.created_at = (:fecha) group by codigo_mp, marcas, vitola,
        Descripcion order by marcas, vitola',
         ['fecha'=>$this->fecha]);


        return view('ReportesExcel.ReporteBultosMP', [
            'dato'=>$data, 'fecha'=>$this->fecha
        ]);
    }
}
