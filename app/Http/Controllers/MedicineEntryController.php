<?php

namespace App\Http\Controllers;

use App\Models\MedicineEntry;
use Illuminate\Http\Request;
use DB;

class MedicineEntryController extends Controller
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

        return view('pages.ingreso-medicina.index');

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $medicineEntry = MedicineEntry::with('medicine')
            ->orderBy('expiration_date', 'asc')
            ->get();

        return response()->json([
            'data'    => $medicineEntry,
            'message' => 'Consulta ejecutada correctamente'
        ], 200);
    }


    public function getMedicineEntries()
    {

        /* $medicineEntry = MedicineEntry::select('medicine_entries.id',
            DB::raw("CONCAT(medicines.name,' | ',expiration_date) as medicine"),
            'medicines.id as medicine_id')
            //->with('medicine')    
            ->join('medicines', 'medicines.id', '=', 'medicine_entries.medicine_id')
            ->where('expiration_Date', '>', date('Y-m-d'))
            ->where('medicine_entries.status', true)
            ->orderBy('expiration_date', 'asc')
            ->get(); */
        
            /* $medicineEntry = DB::select("select me.id, concat(m.name, ' | ', expiration_date) as medicine, 
            quantity - sum(COALESCE(dose,0)) as stock, m.id as medicine_id
            from medicine_entries me 
            inner join medicines m on m.id = me.medicine_id 
            left join livestock_medicines lm on lm.medicine_entry_id = me.id
            where expiration_date > '$fecha'
            and me.status = true
            group by me.id, m.name, expiration_date, quantity, m.id
            having stock > 0
            order by expiration_date asc"); */

        $fecha = date('Y-m-d');

        $medicineEntry = DB::select("select me.id, concat(m.name, ' | ', expiration_date) as medicine, 
            m.id as medicine_id, quantity
            from medicine_entries me 
            inner join medicines m on m.id = me.medicine_id 
            where expiration_date > '$fecha'
            and me.status = true
            group by me.id, m.name, expiration_date, quantity, m.id
            order by expiration_date asc");
        $arr = array();
        foreach ($medicineEntry as $medicine) {
            $stock = DB::select("select quantity - sum(COALESCE(dose,0)) as stock
            from medicine_entries me 
            inner join medicines m on m.id = me.medicine_id 
            left join livestock_medicines lm on lm.medicine_entry_id = me.id
            where expiration_date > '$fecha'
            and me.status = true
            and me.id = $medicine->id
            group by me.id, m.name, expiration_date, quantity, m.id
            having stock >= 0
            order by expiration_date asc");

            //return response()->json($stock, 200);

            if ($stock) {
                if ($stock[0]->stock > 0) {
                    $medicine->stock = $stock[0]->stock;
                    $new = array_push($arr, $medicine);
                }

            }else {
                $medicine->stock = $medicine->quantity;
                $new = array_push($arr, $medicine);
            }
            
        }

        

        return response()->json([
            'data'    => $arr,
            'message' => 'Consulta ejecutada correctamente'
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'expiration_date' => 'required',
            'quantity' => 'required',
            'medicine_id' => 'required',
            'status' => 'required',
        ]);
 
        $medicineEntry = new MedicineEntry();
        $medicineEntry->expiration_date = $request->expiration_date;
        $medicineEntry->quantity = $request->quantity;
        $medicineEntry->medicine_id = $request->medicine_id;
        $medicineEntry->status = $request->status;

        if ($medicineEntry->save()){
            return response()->json([
                'medicineEntry'    => $medicineEntry,
                'message' => 'Registro guardado correctamente'
            ], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MedicineEntry  $medicineEntry
     * @return \Illuminate\Http\Response
     */
    public function show(MedicineEntry $medicineEntry)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MedicineEntry  $medicineEntry
     * @return \Illuminate\Http\Response
     */
    public function edit(MedicineEntry $medicineEntry)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MedicineEntry  $medicineEntry
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MedicineEntry $medicineEntry)
    {
        $this->validate($request, [
            'expiration_date' => 'required',
            'quantity' => 'required',
            'medicine_id' => 'required',
            'status' => 'required',
        ]);

        $updated = $medicineEntry->fill($request->all())->save();

        if ($updated) {
            return response()->json([
                'message' => 'Registro actualizado correctamente'
            ], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MedicineEntry  $medicineEntry
     * @return \Illuminate\Http\Response
     */
    public function destroy(MedicineEntry $medicineEntry)
    {
        try {
            $medicineEntry->delete();
            return response()->json([
                'message' => 'Registro eliminado correctamente'
            ], 200);    
        } catch (\Throwable $th) {
            if ($th->getCode() == 23000) {
                return response()->json([
                    'success' => false,
                    'exception' => $th,
                    'errors' => $message = ['Error: No se puede eliminar un registro con historial.']
                ],500);
            } 
        }
    }
}
