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
class ConsumoBandarExport implements FromView, ShouldAutoSize
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
        $consumobanda=DB::table("consumo_bandas")
            ->leftJoin("vitolas","consumo_bandas.id_vitolas","=","vitolas.id")
            ->leftJoin("marcas","consumo_bandas.id_marca","=","marcas.id")
            ->leftJoin("tamanos","consumo_bandas.id_tamano","=","tamanos.id")
            ->leftJoin("semillas","consumo_bandas.id_semillas","=","semillas.id")
              ->leftJoin("variedads", "consumo_bandas.variedad", "=", "variedads.id")
              ->leftJoin("procedencias", "consumo_bandas.procedencia", "=", "procedencias.id")

            ->select("marcas.name as nombre_marca",
                "vitolas.name as nombre_vitolas",
                "semillas.name as nombre_semillas",
                "variedads.name as nombre_variedad",
                "tamanos.name as nombre_tamano",
               "procedencias.name as nombre_procedencia"
                ,"consumo_bandas.total"
                ,"consumo_bandas.onzas"
                ,"consumo_bandas.libras")
            ->whereDate("consumo_bandas.created_at", "=", $this->fecha)
            ->orderByRaw('nombre_semillas, nombre_marca')->get();
            $first = $consumobanda->first();

        return view('ReportesExcel.ReporteBandaEntrega', [
            'consumobanda'=>$consumobanda, 'fecha'=>$this->fecha,
             'first'=>$first
        ]);
    }
}
