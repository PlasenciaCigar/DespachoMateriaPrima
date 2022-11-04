<?php

namespace App\Exports;
use App\Exports\Sheets\MarcaExport;
use App\Exports\Sheets\MarcaOrder;
use App\Exports\Sheets\MarcaSemilla;
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
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MarcaPeso implements WithMultipleSheets
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

    public function sheets(): array
    {
        $sheets = [];

            $sheets[] = new MarcaExport($this->fecha);
            $sheets[] = new MarcaSemilla($this->fecha);
            $sheets[] = new MarcaOrder($this->fecha);

        return $sheets;
    }
}
