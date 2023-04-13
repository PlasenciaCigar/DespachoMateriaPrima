<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DevolucionesMateriaPrima implements FromCollection , ShouldAutoSize , WithHeadings
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
        $recibirCapa = DB::table("entrada_materia_primas")
            ->join("materia_primas", "materia_primas.Codigo", "=", "entrada_materia_primas.codigo_materia_prima")
            ->select(
               "Codigo"
                , "Descripcion",
                "entrada_materia_primas.Libras")
                ->where('procedencia', '=', 'Despacho')
            ->whereDate('entrada_materia_primas.created_at', '=', $this->fecha)
            ->get();
        return $recibirCapa;
    }
    public function headings(): array
    {
        return [
            [
                'Devolución de Materia Prima, DESPACHO a RMP. ',

            ],
            [

                'Fecha : '.$this->fecha,
                'Planta : TAOSA'
            ],
            [

                'Codigo',
                'Descripcion',
                'Libras'
            ]];
    }
}
