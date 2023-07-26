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

class ReportExport implements FromQuery, WithHeadings, ShouldAutoSize, WithStyles
{
    use Exportable;

    public function __construct($medicine_id, $livestock_id, $breed_id, $mark_id, $category_id, $date_from, $date_to, $user_id)
    {
        $this->medicine_id = $medicine_id;
        $this->livestock_id = $livestock_id;
        $this->breed_id = $breed_id;
        $this->mark_id = $mark_id;
        $this->category_id = $category_id;
        $this->date_from = $date_from;
        $this->date_to = $date_to;
        $this->user_id = $user_id;
    }

    public function query()
    {
        /* return DB::table('livestock_medicines as lm')
            ->join('medicines as me','me.id','=','lm.medicine_id')
            ->join('livestocks as l','l.id','=','lm.livestock_id')
            ->join('breeds as b','b.id','=','l.breed_id')
            ->join('categories as c','c.id','=','me.category_id')
            ->join('marks as m','m.id','=','me.mark_id')
            ->select('lm.id','l.name as animal','b.name as raza','c.name as categoria',
            'm.name as marca','me.name as medicina',DB::raw("CONCAT(lm.dose,' ml') as dosis"), 
            DB::raw('DATE(lm.date) AS fecha_aplicacion'))
            ->orderBy('date', 'desc')
            ->ganado($this->livestock_id)
            ->medicina($this->medicine_id)
            //->where('breed_id',$this->breed_id)
            ->whereBetween('lm.date',[$this->date_from, $this->date_to]); */
        if($this->medicine_id === "null"){
            $this->medicine_id = "";
        }
        if($this->livestock_id === "null"){
            $this->livestock_id = "";
        }
        if($this->breed_id === "null"){
            $this->breed_id = "";
        }
        if($this->mark_id === "null"){
            $this->mark_id = "";
        }
        if($this->category_id === "null"){
            $this->category_id = "";
        }
        if($this->user_id === "null"){
            $this->user_id = "";
        }

        //dd($this->breed_id);
        
        /* $consulta = LivestockMedicine::query()->select('livestock_medicines.id','l.name as animal','b.name as raza','c.name as categoria',
        'm.name as marca','me.name as medicina',DB::raw("CONCAT(dose,' ml') as dosis"), 
        DB::raw('DATE(date) AS fecha_aplicacion'))
        ->join('medicines as me','me.id','=','livestock_medicines.medicine_id')
        ->join('livestocks as l','l.id','=','livestock_medicines.livestock_id')
        ->join('breeds as b','b.id','=','l.breed_id')
        ->join('categories as c','c.id','=','me.category_id')
        ->join('marks as m','m.id','=','me.mark_id')
        ->orderBy('date', 'desc')
        ->ganado($this->livestock_id)
        ->medicina($this->medicine_id)
        ->conRaza($this->breed_id)
        ->whereBetween('date',[$this->date_from, $this->date_to])
        ->get();

        dd ($consulta); */
        if ($this->livestock_id) {
            return LivestockMedicine::query()->select('livestock_medicines.id',
            DB::raw("CONCAT(l.name,' | ', l.identification) as animal"),
            'b.name as raza','c.name as categoria',
            'm.name as marca','me.name as medicina',
            DB::raw("CONCAT(dose,' ml') as dosis"), 
            DB::raw('DATE(date) AS fecha_aplicacion'),
            DB::raw("CONCAT(first_name,' ', last_name) as usuario"))
            ->join('medicine_entries as men','men.id','=','livestock_medicines.medicine_entry_id')
            ->join('medicines as me','me.id','=','men.medicine_id')
            ->join('livestocks as l','l.id','=','livestock_medicines.livestock_id')
            ->join('breeds as b','b.id','=','l.breed_id')
            ->join('categories as c','c.id','=','me.category_id')
            ->join('marks as m','m.id','=','me.mark_id')
            ->join('users as u','u.id','=','livestock_medicines.user_id')
            ->orderBy('date', 'desc')
            ->ganado($this->livestock_id)
            ->conMedicina($this->medicine_id)
            ->conRaza($this->breed_id)
            ->usuario($this->user_id)
            //->conMarca($this->mark_id)
            //->conCategoria($this->category_id)
            ->whereBetween('date',[$this->date_from, $this->date_to]);
        }else{
            return LivestockMedicine::query()->select('livestock_medicines.id',
            DB::raw("CONCAT(l.name,' | ', l.identification) as animal"),
            'b.name as raza','c.name as categoria',
            'm.name as marca','me.name as medicina',
            DB::raw("CONCAT(dose,' ml') as dosis"), 
            DB::raw('DATE(date) AS fecha_aplicacion'),
            DB::raw("CONCAT(first_name,' ', last_name) as usuario"))
            ->join('medicine_entries as men','men.id','=','livestock_medicines.medicine_entry_id')
            ->join('medicines as me','me.id','=','men.medicine_id')
            ->join('livestocks as l','l.id','=','livestock_medicines.livestock_id')
            ->join('breeds as b','b.id','=','l.breed_id')
            ->join('categories as c','c.id','=','me.category_id')
            ->join('marks as m','m.id','=','me.mark_id')
            ->join('users as u','u.id','=','livestock_medicines.user_id')
            ->orderBy('date', 'desc')
            //->ganado($this->livestock_id)
            ->conMedicina($this->medicine_id)
            ->conRaza($this->breed_id)
            ->usuario($this->user_id)
            //->conMarca($this->mark_id)
            //->conCategoria($this->category_id)
            ->whereBetween('date',[$this->date_from, $this->date_to]);
        }

        
        

        /* return LivestockMedicine::with('livestock.breed','medicine.mark', 'medicine.category')
            ->orderBy('date', 'desc')
            ->whereBetween('livestock_medicines.date',['2022-01-01','2022-07-30'])
            ->get(); */
            
    }

    public function headings(): array
    {
        return [
            '#',
            'Animal',
            'Raza',
            'Categoria',
            'Marca',
            'Medicina',
            'Dosis',
            'Fecha de aplicación',
            'Usuario',
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

            'B'    => ['font' => ['bold' => true,
                                'size' => 12,
                                'color' => ['rgb' => '191970']]],
            'G'    => ['font' => ['bold' => true,
                                'size' => 12,
                                'color' => ['rgb' => 'FF0000']]],
        ];
    }
}
