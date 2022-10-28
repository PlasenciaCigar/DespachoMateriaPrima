<?php

namespace App\Exports;

use App\MateriaPrima;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MateriaPrimaExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return MateriaPrima::select('Codigo', 'Descripcion', 'Libras')->get();
    }


    public function headings(): array
    {
        return [
            [
                'MATERIA PRIMA EXISTENTE',

            ],
            [
                'Planta : TAOSA'
            ],
            [
            'Codigo',
            'Nombre',
            'Libras',
        ]];
    }
}
