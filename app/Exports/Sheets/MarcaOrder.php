<?php

namespace App\Exports\Sheets;

use App\CapaEntrega;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Calidad;
use App\RecibirCapa;
use App\Semilla;
use App\Tamano;
use Carbon\Carbon;
use Carbon\Traits\Date;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class MarcaOrder implements FromCollection, ShouldAutoSize , WithHeadings
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
    public function collection()
    {
        $entregaCapa=DB::table("capa_entregas")
        ->join("vitolas","capa_entregas.id_vitolas","=","vitolas.id")
        ->join("semillas","capa_entregas.id_semilla","=","semillas.id")
        ->join("marcas","capa_entregas.id_marca","=","marcas.id")
        ->join("calidads", "capa_entregas.id_calidad", "calidads.id")
        ->select(
            "capa_entregas.id_vitolas",
            "capa_entregas.id_marca",
            "capa_entregas.id_semilla",
            "capa_entregas.id_calidad",
            "semillas.name as semilla",
            "marcas.name as marca",
            "vitolas.name as vitola",
            DB::raw('SUM(capa_entregas.total) as Totale'))
        ->whereDate("capa_entregas.created_at","=" ,$this->fecha)
        ->orderBy('marca')
        ->groupByRaw('capa_entregas.id_marca, capa_entregas.id_vitolas, capa_entregas.id_semilla, capa_entregas.id_calidad')
        ->get();
        $MarcaP = [];
        foreach ($entregaCapa as $capa) {
            $pso= DB::table('existencia_diarios')->where('id_semillas', '=', $capa->id_semilla)
            ->where('id_calidad','=', $capa->id_calidad)
            ->whereDate('created_at', '=', $this->fecha)->sum('pesoconsumo');

            $cant= DB::table('capa_entregas')->where('id_semilla', '=', $capa->id_semilla)
            ->where('id_calidad','=', $capa->id_calidad)
            ->whereDate('created_at', '=', $this->fecha)->sum('total');
            $prm= $pso/$cant;
            $ttl= round($prm*$capa->Totale, 2);

                $MarcaP[] = ['Marca'=>$capa->marca,'Vitola'=>$capa->vitola,'Semilla'=>$capa->semilla,
             'Calidad'=>$capa->id_calidad, 'Cantidad'=>$capa->Totale, 'Peso'=> $ttl];
            
        }
        return collect($MarcaP);
    }

    public function headings(): array
    {
        return [
            [
                'Informe de Capa Entregada',
            ],
            [
                'Fecha : '.$this->fecha,
                'Planta : TAOSA'
            ],
            [
                'Marca',
                'Vitola',
            'Semilla',
            'Calidad',
            'Cantidad',
            'Peso'
        ]];
    }
}
