<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class EntradaBultosExport implements FromCollection , ShouldAutoSize , WithHeadings
{
    use Exportable;

    protected $fecha;
    public function __construct(String $fecha )
    {

        $this->fecha = $fecha;
    }
    /**
     * @param Request $request
     * @return View
     */
    public function collection()
    {
        $recibirCapa = DB::table("entrada_bultos")
            ->leftJoin("marcas", "entrada_bultos.marca", "=", "marcas.id")
            ->leftJoin("vitolas", "entrada_bultos.vitola", "=", "vitolas.id")

            ->select(
               "marcas.name as marcas"
                , "vitolas.name as vitolas",
                "entrada_bultos.bultos"
                , "entrada_bultos.peso",
                "entrada_bultos.libras")
            ->whereDate('entrada_bultos.created_at', '=', $this->fecha)
            ->get();
        return $recibirCapa;
    }
    public function headings(): array
    {
        return [
            [
                'Entradas de Bultos Diario ',

            ],
            [

                'Fecha : '.$this->fecha,
                'Planta : TAOSA'
            ],
            [

                'Marca',
                'Vitola',
                'Bultos',
                'Peso',
                'Libras',
            ]];
    }
}
