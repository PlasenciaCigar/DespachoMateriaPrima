<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use App\BInvInicial;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EntregaBultoExport implements FromCollection  , ShouldAutoSize ,WithHeadings
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

        $bultoentrega=DB::table("bultos_salidas")
            ->leftJoin("empleados_bandas","bultos_salidas.id_empleado","=","empleados_bandas.id")
            ->leftJoin("vitolas","bultos_salidas.id_vitolas","=","vitolas.id")
            ->leftJoin("marcas","bultos_salidas.id_marca","=","marcas.id")
            ->select("empleados_bandas.codigo AS codigo_empleado",
                "empleados_bandas.nombre AS nombre_empleado",
                "vitolas.name as nombre_vitolas",
               "marcas.name as nombre_marca",
               "empleados_bandas.salon as salon"
                ,"bultos_salidas.total",
                "bultos_salidas.id_marca",
                "bultos_salidas.id_vitolas", "bultos_salidas.created_at as fechas")
                ->orderby("empleados_bandas.codigo")
                ->orderby("marcas.name")
            ->whereDate("bultos_salidas.created_at","=" ,$this->fecha)->get();
        $tkm = [];
        foreach($bultoentrega as $bultosConOnzas){
            $consultabulto= BInvInicial::where('id_marca', $bultosConOnzas->id_marca)
            ->where('id_vitolas', $bultosConOnzas->id_vitolas)
            ->select('onzas')->first();
            $comprobacion;
            $consultabulto != null ? $comprobacion= $consultabulto->onzas : $comprobacion=null;

            $tkm[] = ["codigo_empleado"=>$bultosConOnzas->codigo_empleado,
            "nombre_empleado"=>$bultosConOnzas->nombre_empleado,
            "nombre_vitolas"=>$bultosConOnzas->nombre_vitolas,
            "nombre_marca"=>$bultosConOnzas->nombre_marca,
            "salon"=>$bultosConOnzas->salon,
            "total"=>$bultosConOnzas->total,
            "onzas"=>$comprobacion,
            "peso"=>$comprobacion*$bultosConOnzas->total/16];
        }

        return collect($tkm);
    }


    public function headings(): array
    {
        return [
            [
                'Entrega de Bultos a los Salones ',

            ],
            [

                'Fecha : '.$this->fecha,
                'Planta : TAOSA'
            ],
            [
                'Codigo',
            'Empleado',
            'Vitola',
            'Marca',
            'Salon',
            'Total Entregada',
            'Onzas',
            'Peso'

        ]];


    }
}
