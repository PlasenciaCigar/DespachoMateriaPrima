<?php

namespace App\Imports;
use Carbon\Carbon;
use App\MateriaPrima;
use Maatwebsite\Excel\Concerns\ToModel;

class MateriaPrimaImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new MateriaPrima([
            'Codigo' => $row[0],
            'Descripcion' => $row[1],
            'Libras' => $row[2],
            'created_at' => Carbon::now()->format('Y-m-d'),
        ]);
    }
}
