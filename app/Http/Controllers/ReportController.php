<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\ReportExport;
use App\Exports\KardexExport;
use Maatwebsite\Excel\Facades\Excel;
use DB;
use PDF;
use Auth;
use App\Models\LivestockMedicine;

class ReportController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function showView()
    {

        return view('pages.reports.index');

    }

    public function getLivestockMedicine(Request $request){

        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $medicine_id = $request->medicine_id;
        $breed_id = $request->breed_id;
        $livestock_id = $request->livestock_id;
        $mark_id = $request->mark_id;
        $category_id = $request->category_id;
        $user_id = $request->user_id;

        if($medicine_id === "null"){
            $medicine_id = "";
        }
        if($livestock_id === "null"){
            $livestock_id = "";
        }
        if($breed_id === "null"){
            $breed_id = "";
        }
        if($mark_id === "null"){
            $mark_id = "";
        }
        if($category_id === "null"){
            $category_id = "";
        }
        if($user_id === "null"){
            $user_id = "";
        }

        if ($livestock_id) {

            $livestock_medicines = LivestockMedicine::select('livestock_medicines.id', 
            DB::raw("CONCAT(l.name,' | ', l.identification) as animal"),'b.name as raza',
            'c.name as categoria','m.name as marca','me.name as medicina',
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
            ->ganado($livestock_id)
            ->conMedicina($medicine_id)
            ->conRaza($breed_id)
            ->usuario($user_id)
            //->entradaMedicinaConMarca($mark_id)
            //->conMarca($mark_id)
            //->conCategoria($category_id)
            ->whereBetween('date',[$date_from, $date_to])
            ->get();
        }else {
            $livestock_medicines = LivestockMedicine::select('livestock_medicines.id', 
            DB::raw("CONCAT(l.name,' | ', l.identification) as animal"),'b.name as raza',
            'c.name as categoria','m.name as marca','me.name as medicina',
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
            //->ganado($livestock_id)
            ->conMedicina($medicine_id)
            ->conRaza($breed_id)
            ->usuario($user_id)
            //->entradaMedicinaConMarca($mark_id)
            //->conMarca($mark_id)
            //->conCategoria($category_id)
            ->whereBetween('date',[$date_from, $date_to])
            ->get();
        }


        return response()->json([
            'data'    => $livestock_medicines,
            'message' => 'Consulta ejecutada correctamente'
        ], 200);

    }

    public function exportExcel(Request $request){

        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $medicine_id = $request->medicine_id;
        $breed_id = $request->breed_id;
        $livestock_id = $request->livestock_id;
        $mark_id = $request->mark_id;
        $category_id = $request->category_id;
        $user_id = $request->user_id;
        
        /* $consulta2 = LivestockMedicine::with('livestock.breed','medicine.mark', 'medicine.category')
            ->whereBetween('livestock_medicines.date',['2022-01-01','2022-07-30'])
            ->get(); */

        //return $consulta2;
        return (new ReportExport($medicine_id,$livestock_id,$breed_id,$mark_id,$category_id,$date_from, $date_to, $user_id))->download('reporte.xlsx');

        $reporte = Excel::download(new ReportExport, 'reporte.xlsx');

        return response()->json([
            'success' => true,
            'data' => $reporte,
            'message' => 'Reporte generado correctamente.'
        ]);
    }

    public function exportKardex(Request $request){

        return Excel::download(new KardexExport, 'kardex.xlsx');

        //return (new ReportExport())->download('reporte.xlsx');

        $reporte = Excel::download(new ReportExport, 'reporte.xlsx');

        return response()->json([
            'success' => true,
            'data' => $reporte,
            'message' => 'Reporte generado correctamente.'
        ]);
    }

    public function exportPDF(Request $request){

        $date_from = $request->date_from;
        $date_to = $request->date_to;
        $medicine_id = $request->medicine_id;
        $breed_id = $request->breed_id;
        $livestock_id = $request->livestock_id;
        $mark_id = $request->mark_id;
        $category_id = $request->category_id;

        if($medicine_id === "null"){
            $medicine_id = "";
        }
        if($livestock_id === "null"){
            $livestock_id = "";
        }
        if($breed_id === "null"){
            $breed_id = "";
        }
        if($mark_id === "null"){
            $mark_id = "";
        }
        if($category_id === "null"){
            $category_id = "";
        }

        $livestock_medicines = LivestockMedicine::select('livestock_medicines.id', 
            DB::raw("CONCAT(l.name,' | ', l.identification) as animal"),'b.name as raza',
            'c.name as categoria','m.name as marca','me.name as medicina',
            DB::raw("CONCAT(dose,' ml') as dosis"), 
            DB::raw('DATE(date) AS fecha_aplicacion'))
            ->join('medicines as me','me.id','=','livestock_medicines.medicine_id')
            ->join('livestocks as l','l.id','=','livestock_medicines.livestock_id')
            ->join('breeds as b','b.id','=','l.breed_id')
            ->join('categories as c','c.id','=','me.category_id')
            ->join('marks as m','m.id','=','me.mark_id')
            ->orderBy('date', 'desc')
            ->ganado($livestock_id)
            ->medicina($medicine_id)
            ->conRaza($breed_id)
            ->conMarca($mark_id)
            ->conCategoria($category_id)
            ->whereBetween('date',[$date_from, $date_to])
            ->get();

        $pdf = PDF::loadView('pages.reports.pdf', compact('livestock_medicines'));
        return $pdf->setOrientation('landscape')->download('reporte.pdf');
    }

    public function dosePerMedicine(){

        $dosePerMedicine = LivestockMedicine::select('m.name',DB::raw("COUNT(dose) as cant_dosis"))
            ->join('medicine_entries as me','me.id','=','livestock_medicines.medicine_entry_id')
            ->join('medicines as m','m.id','=','me.medicine_id')
            ->groupBy('m.name')
            ->get();
        $arr = array();
        foreach ($dosePerMedicine as $doseMedicine) {
           $new = array_push($arr,[$doseMedicine->name, intval( $doseMedicine->cant_dosis )]);
        }

        return response()->json([
            'data'    => $arr,
            'message' => 'Consulta ejecutada correctamente'
        ], 200);
    }

    public function topAnimalDose(){

        $topFiveAnimals = LivestockMedicine::select(DB::raw("CONCAT(l.name,' | ', l.identification) as animal"),
            DB::raw("COUNT(dose) as cant_dosis"))
            //DB::raw("COUNT(dose) as cant_dosis"), 'me.name as medicina')
            ->join('livestocks as l','l.id','=','livestock_medicines.livestock_id')
            //->join('medicines as me','me.id','=','livestock_medicines.medicine_id')
            //->groupBy('animal', 'medicina')
            ->groupBy('animal')
            ->orderBy('cant_dosis', 'desc')
            ->limit(5)
            ->get();
        
        /* return response()->json([
            'data'    => $topFiveAnimals,
            'message' => 'Consulta ejecutada correctamente'
        ], 200); */
        
        $data = [];
        $categories = [];

        foreach ($topFiveAnimals as $topFiveAnimal) {
            
            array_push($data, [$topFiveAnimal->animal, intval( $topFiveAnimal->cant_dosis )]);

            /* array_push($data,[
                'name' => $topFiveAnimal->medicina,
                'data'=> [$topFiveAnimal->animal, $topFiveAnimal->cant_dosis]
            ]); */

            array_push($categories, $topFiveAnimal->animal);
        }

        return response()->json([
            'consulta'    => $topFiveAnimals,
            'data'    => $data,
            'categories'    => $categories,
            'message' => 'Consulta ejecutada correctamente'
        ], 200);
    }
}
