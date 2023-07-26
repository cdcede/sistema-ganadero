<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use Illuminate\Http\Request;

class MedicineController extends Controller
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

        return view('pages.medicinas.index');

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $medicines = Medicine::with('category','mark')->get();

        return response()->json([
            'data'    => $medicines,
            'message' => 'Consulta ejecutada correctamente'
        ], 200);
    }

    public function getMedicines()
    {
        $medicines = Medicine::with('category','mark')->where('status', true)->get();
        return response()->json([
            'data'    => $medicines,
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
            'name' => 'required|max:50|iunique:medicines,name',
            'category_id' => 'required',
            'mark_id' => 'required',
            'status' => 'required',
        ]);
 
        $medicine = new Medicine();
        $medicine->name = $request->name;
        $medicine->category_id = $request->category_id;
        $medicine->mark_id = $request->mark_id;
        $medicine->status = $request->status;

        if ($medicine->save()){
            return response()->json([
                'medicine'    => $medicine,
                'message' => 'Registro guardado correctamente'
            ], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Medicine  $medicine
     * @return \Illuminate\Http\Response
     */
    public function show(Medicine $medicine)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Medicine  $medicine
     * @return \Illuminate\Http\Response
     */
    public function edit(Medicine $medicine)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Medicine  $medicine
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Medicine $medicine)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'category_id' => 'required',
            'mark_id' => 'required',
            'status' => 'required',
        ]);

        $updated = $medicine->fill($request->all())->save();

        if ($updated) {
            return response()->json([
                'message' => 'Registro actualizado correctamente'
            ], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Medicine  $medicine
     * @return \Illuminate\Http\Response
     */
    public function destroy(Medicine $medicine)
    {
        try {
            $medicine->delete();
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
