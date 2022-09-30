<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithFooter;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExistenciaDiarioExports implements FromCollection,ShouldAutoSize ,WithHeadings, FromArray
{


    use \Maatwebsite\Excel\Concerns\Exportable;

    protected $fecha;

    public function __construct(string $fecha)
    {

        $this->fecha = $fecha;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $entregaCapa=DB::table("existencia_diarios")
            ->leftJoin("semillas","existencia_diarios.id_semillas","=","semillas.id")
            ->leftJoin("calidads","existencia_diarios.id_calidad","=","calidads.id")
            ->leftJoin("tamanos","existencia_diarios.id_tamano","=","tamanos.id")
            ->select("semillas.name as nombre_semillas",
                \DB::raw("(CASE WHEN calidads.name = 'Primera' THEN '1'
                WHEN calidads.name = 'segunda' THEN '2'
                WHEN calidads.name = 'Tercera' THEN '3'
                WHEN calidads.name = 'cuarta' THEN '4' END) as nombre_calidads"),
               "tamanos.name as nombre_tamano"
                ,"existencia_diarios.totalinicial","existencia_diarios.pesoinicial"
                ,"existencia_diarios.totalentrada","existencia_diarios.pesoentrada"
                ,"existencia_diarios.totalfinal","existencia_diarios.pesofinal",
                "existencia_diarios.totalconsumo","existencia_diarios.pesoconsumo"
              )
            ->whereDate("existencia_diarios.created_at","=" ,$this->fecha)
            ->orderBy("nombre_semillas")
            ->orderBy("nombre_calidads")
            ->orderBy("nombre_tamano")->get();

            $saltos=[];
            $condicional=0;
            $namecon='';
            foreach($entregaCapa as $val){
                if($val->nombre_calidads!=$condicional || $val->nombre_semillas!=$namecon){
                    $saltos[] = ["name"=>'',
                "nombre_calidads"=>'',
                "nombre_tamano"=>'',
                "totalinicial"=>'',
                "pesoinicial"=>'',
                "totalentrada"=>'',
                "pesoentrada"=>'',
                "totalfinal"=>'',
                "pesofinal"=>'',
                "totalconsumos"=>'',
                "pesoconsumo"=>'',
                ]
                ;
                }
                $saltos[] = ["name"=>$val->nombre_semillas,
                "nombre_calidads"=>$val->nombre_calidads,
                "nombre_tamano"=>$val->nombre_tamano,
                "totalinicial"=>$val->totalinicial,
                "pesoinicial"=>$val->pesoinicial,
                "totalentrada"=>$val->totalentrada,
                "pesoentrada"=>$val->pesoentrada,
                "totalfinal"=>$val->totalfinal,
                "pesofinal"=>$val->pesofinal,
                "totalconsumos"=>$val->totalconsumo,
                "pesoconsumo"=>$val->pesoconsumo,
                ];
                $condicional = $val->nombre_calidads;
                $namecon= $val->nombre_semillas;
            }

        return collect($saltos);

        //
    }

    public function array(): array{

        $capita=DB::table("existencia_diarios")
            ->leftJoin("semillas","existencia_diarios.id_semillas","=","semillas.id")
            ->leftJoin("calidads","existencia_diarios.id_calidad","=","calidads.id")
            ->leftJoin("tamanos","existencia_diarios.id_tamano","=","tamanos.id")
            ->selectRaw('sum(existencia_diarios.totalconsumo) as Consumo')
            ->selectRaw('sum(existencia_diarios.totalentrada) as Entrada')
            ->selectRaw('sum(existencia_diarios.totalinicial) as Inicial')
            ->selectRaw('sum(existencia_diarios.totalfinal) as Final')
            ->selectRaw('sum(existencia_diarios.pesoinicial) as pesoI')
            ->selectRaw('sum(existencia_diarios.pesoentrada) as pesoE')
            ->selectRaw('sum(existencia_diarios.pesofinal) as pesoF')
            ->selectRaw('sum(existencia_diarios.pesoconsumo) as pesoC')
            ->whereDate("existencia_diarios.created_at","=" ,$this->fecha)
            ->get();
            $contadorE=0;
            $contadorC=0;
            $contadorI=0;
            $contadorF=0;
            $contadorPI=0;
            $contadorPE=0;
            $contadorPF=0;
            $contadorPC=0;
            foreach($capita as $ca){
                $contadorC+=$ca->Consumo;
                $contadorE+=$ca->Entrada;
                $contadorI+=$ca->Inicial;
                $contadorF+=$ca->Final;
                $contadorPI+=$ca->pesoI;
                $contadorPE+=$ca->pesoE;
                $contadorPF+=$ca->pesoF;
                $contadorPC+=$ca->pesoC;

            }

            return [[''],
                ['Totales','','',$contadorI, $contadorPI, $contadorE, $contadorPE,
                $contadorF, $contadorPF,$contadorC, $contadorPC]];
    }




    public function headings(): array
    {
        $capita=DB::table("existencia_diarios")
        ->leftJoin("semillas","existencia_diarios.id_semillas","=","semillas.id")
        ->leftJoin("calidads","existencia_diarios.id_calidad","=","calidads.id")
        ->leftJoin("tamanos","existencia_diarios.id_tamano","=","tamanos.id")
        ->selectRaw('sum(existencia_diarios.totalconsumo) as Total')
        ->whereDate("existencia_diarios.created_at","=" ,$this->fecha)
        ->get();
        return [
            [
                ' Inventario De Existencia de Capa  ',

            ],
            [

                'Fecha : '.$this->fecha,
                'Planta : TAOSA'
            ],
            [
                'Semilla',
            'calidad',
            'Tamaño',
            'Inv.Inicial',
            'Peso',
            'Entradas',
            'Peso',
            'Inv.Final ','Peso ',
            'Consumo ','Peso ',
                'Dev. Cuarto Frio ','Peso ',
            ]];
    }
}
