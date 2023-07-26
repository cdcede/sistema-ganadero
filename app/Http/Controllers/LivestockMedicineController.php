<?php

namespace App\Http\Controllers;

use App\Models\LivestockMedicine;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Auth;

class LivestockMedicineController extends Controller
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

        return view('pages.medicina-aplicada.index');

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $livestock_medicines = LivestockMedicine::with('livestock','medicineEntry.medicine')->get();

        return response()->json([
            'data'    => $livestock_medicines,
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
            'date' => 'required',
            'dose' => 'required',
            'livestock_id' => 'required',
            'medicine_id' => 'required',
        ]);
        
        $dose_validation = LivestockMedicine::join('medicine_entries', 'livestock_medicines.medicine_entry_id', '=', 'medicine_entries.id')
            ->join('medicines', 'medicine_entries.medicine_id', '=', 'medicines.id')
            ->where('livestock_id',$request->livestock_id)
            ->where('medicines.id',$request->medicine_id['medicine_id'])
            ->where('date',$request->date)->exists();
        
        if ($dose_validation){
            return response()->json([
                'message' => 'Ya existe un registro con esa fecha, medicina y animal.'
            ], 400);
        }

        if ($request->dose > $request->medicine_id['stock']) {
            return response()->json([
                'message' => 'La dosis no puede ser mayor a la cantidad disponible.'
            ], 400);
        }

        $livestock_medicine = new LivestockMedicine();
        $livestock_medicine->date = $request->date;
        $livestock_medicine->dose = $request->dose;
        $livestock_medicine->livestock_id = $request->livestock_id;
        $livestock_medicine->medicine_entry_id = $request->medicine_id['id'];
        $livestock_medicine->user_id = Auth::user()->id;

        if ($livestock_medicine->save()){
            return response()->json([
                'livestock_medicine'    => $livestock_medicine,
                'message' => 'Registro guardado correctamente'
            ], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LivestockMedicine  $livestockMedicine
     * @return \Illuminate\Http\Response
     */
    public function show(LivestockMedicine $livestockMedicine)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LivestockMedicine  $livestockMedicine
     * @return \Illuminate\Http\Response
     */
    public function edit(LivestockMedicine $livestockMedicine)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LivestockMedicine  $livestockMedicine
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, LivestockMedicine $livestockMedicine)
    {

        $this->validate($request, [
            'date' => 'required',
            'dose' => 'required',
            'livestock_id' => 'required',
            'medicine_id' => 'required',
        ]);

        $dose_validation = LivestockMedicine::join('medicine_entries', 'livestock_medicines.medicine_entry_id', '=', 'medicine_entries.id')
            ->join('medicines', 'medicine_entries.medicine_id', '=', 'medicines.id')
            ->where('livestock_id',$request->livestock_id)
            ->where('medicines.id',$request->medicine_id['medicine_id'])
            ->where('date',$request->date)->exists();
        
        if ($dose_validation){
            return response()->json([
                'message' => 'Ya existe un registro con esa fecha, medicina y animal.'
            ], 400);
        }

        if ($request->dose > $request->medicine_id['stock']) {
            return response()->json([
                'message' => 'La dosis no puede ser mayor a la cantidad disponible.'
            ], 400);
        }

        $livestockMedicine->date = $request->date;
        $livestockMedicine->dose = $request->dose;
        $livestockMedicine->livestock_id = $request->livestock_id;
        $livestockMedicine->medicine_entry_id = $request->medicine_id['id'];
        //$livestockMedicine->user_id = Auth::user()->id;

        if ($livestockMedicine->save()) {
            return response()->json([
                'message' => 'Registro actualizado correctamente'
            ], 200);
        }

       /*  $updated = $livestockMedicine->fill($request->all())->save();

        if ($updated) {
            return response()->json([
                'message' => 'Registro actualizado correctamente'
            ], 200);
        } */
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LivestockMedicine  $livestockMedicine
     * @return \Illuminate\Http\Response
     */
    public function destroy(LivestockMedicine $livestockMedicine)
    {
        try {
            $livestockMedicine->delete();
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
