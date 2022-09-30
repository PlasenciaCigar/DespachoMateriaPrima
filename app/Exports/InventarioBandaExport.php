<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InventarioBandaExport implements FromCollection,ShouldAutoSize ,WithHeadings
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
        $entregaCapa=DB::table("inventario_bandas")
            ->leftJoin("semillas","inventario_bandas.id_semillas","=","semillas.id")
            ->leftJoin("variedads", "inventario_bandas.id_variedad", "=", "variedads.id")
            ->leftJoin("procedencias", "inventario_bandas.id_procedencia", "=", "procedencias.id")
            ->leftJoin("tamanos","inventario_bandas.id_tamano","=","tamanos.id")
            ->select("semillas.name as nombre_semillas",
           "tamanos.name as nombre_tamano",

               "variedads.name as nombre_variedad",
                "procedencias.name as nombre_procedencia"
                ,"inventario_bandas.totalinicial","inventario_bandas.pesoinicial"
                , DB::raw("((inventario_bandas.pesoinicial/100)*inventario_bandas.totalinicial/16) as Libra")
                ,"inventario_bandas.totalentrada","inventario_bandas.pesoentrada"
                , DB::raw("((inventario_bandas.pesoentrada/100)*inventario_bandas.totalentrada/16) as Libra2")
                ,"inventario_bandas.totalfinal","inventario_bandas.pesofinal"
                , DB::raw("((inventario_bandas.pesofinal/100)*inventario_bandas.totalfinal/16) as Libra3")
                ,"inventario_bandas.totalconsumo","inventario_bandas.pesoconsumo"
                , DB::raw("((inventario_bandas.pesoconsumo/100)*inventario_bandas.totalconsumo/16) as Libra4")
                ,"inventario_bandas.pesobanda")
            ->whereDate("inventario_bandas.created_at","=" ,$this->fecha)
            ->orderBy("nombre_semillas")->get();

        return $entregaCapa;
        //
    }

    public function headings(): array
    {
        return [
            [
                ' Inventario De Existencia de Banda  ',

            ],
            [

                'Fecha : '.$this->fecha,
                'Planta : TAOSA'
            ],
            [
                'Semilla',
                'Tamaño',
                'variedad',
                'Procedencia',
                'Inv.Inicial',
                'Peso',
                'Libras',
                'Entradas',
                'Peso',
                'Libras',
                'Inv.Final ','peso',
                'Libras',
                'Consumo ','peso ',
                'Libras',
                'Peso Por 100 Bandas',
            ]];
    }
}
