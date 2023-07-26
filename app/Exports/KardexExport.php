<?php

namespace App\Exports;
use DB;
use App\Models\LivestockMedicine;

//use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KardexExport implements FromQuery, WithHeadings, ShouldAutoSize, WithStyles
{
    use Exportable;

    public function query()
    {


        /* return DB::query()->table('livestock_medicines as lm')
            ->select('mee.id', 'm.name as marca', 'me.name as medicina', 'expiration_date', 
            'quantity as ingreso',
            DB::raw("SUM(dose) as egreso"),
            DB::raw("quantity - SUM(dose) as stock"))
            ->join('medicine_entries as mee', 'mee.id', '=', 'lm.medicine_entry_id')
            ->join('medicines as me', 'me.id', '=', 'mee.medicine_id')
            ->join('marks as m', 'm.id', '=', 'me.mark_id')
            ->groupBy('mee.id', 'm.name', 'me.name', 'expiration_date', 'quantity')
            ->get(); */

        return LivestockMedicine::query()->select('mee.id', 'm.name as marca', 'me.name as medicina', 'expiration_date', 
            'quantity as ingreso',
            DB::raw("SUM(dose) as egreso"),
            DB::raw("quantity - SUM(dose) as stock"))
            ->join('medicine_entries as mee', 'mee.id', '=', 'livestock_medicines.medicine_entry_id')
            ->join('medicines as me', 'me.id', '=', 'mee.medicine_id')
            ->join('marks as m', 'm.id', '=', 'me.mark_id')
            ->groupBy('mee.id', 'm.name', 'me.name', 'expiration_date', 'quantity');
            
    }

    public function headings(): array
    {
        return [
            '#',
            'Marca',
            'Medicina',
            'Fecha de expiración',
            'Ingreso',
            'Egreso',
            'Stock',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],

            // Styling a specific cell by coordinate.
            //'B2' => ['font' => ['italic' => true]],

            // Styling an entire column.
            //'C'  => ['font' => ['size' => 16]],

            'E'    => ['font' => ['bold' => true,
                                'size' => 12,
                                'color' => ['rgb' => '009900']]],
            'F'    => ['font' => ['bold' => true,
                                'size' => 12,
                                'color' => ['rgb' => 'FF0000']]],
            'G'    => ['font' => ['bold' => true,
                                'size' => 12,
                                'color' => ['rgb' => '000099']]],
        ];
    }
}
