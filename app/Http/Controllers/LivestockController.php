<?php

namespace App\Http\Controllers;

use App\Models\Livestock;
use Illuminate\Http\Request;
use Auth;

class LivestockController extends Controller
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

        return view('pages.ganado.index');

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() 
    {
        $livestock = Livestock::with('breed')->get();

        return response()->json([
            'data'    => $livestock,
            'message' => 'Consulta ejecutada correctamente'
        ], 200);
    }

    public function getLivestoks()
    {
        $livestock = Livestock::where('status', true)->get();
        return response()->json([
            'data'    => $livestock,
            'message' => 'Consulta ejecutada correctamente'
        ], 200);
    }

    public function getLivestock(){

        /* $customers = Customer::select('ide_cliente',\DB::raw("CONCAT(cedula,' | ',nombre) as test"), 'cedula', 'nombre')
            ->wordFilter($request->keyword)
            ->take(10)
            ->get();
        */

        //$livestock = Livestock::with('breed')->get();
        
        $livestock = Livestock::select('id',\DB::raw("CONCAT(name,' | ',identification) as name_identification"))
            ->with('breed')->where('status', true)->get();

        return response()->json([
            'data'    => $livestock,
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
            'name' => 'required|max:50|iunique:livestocks,name',
            'identification' => 'required',
            'description' => 'required',
            'sex' => 'required',
            'breed_id' => 'required',
            'status' => 'required',
        ]);
 
        $livestock = new Livestock();
        $livestock->name = $request->name;
        $livestock->identification = $request->identification;
        $livestock->description = $request->description;
        $livestock->sex = $request->sex;
        $livestock->birth_date = $request->birth_date;
        $livestock->purchase_date = $request->purchase_date;
        $livestock->death_date = $request->death_date;
        $livestock->breed_id = $request->breed_id;
        $livestock->usuario_id = Auth::user()->id;
        $livestock->status = $request->status;

        if ($livestock->save()){
            return response()->json([
                'livestock'    => $livestock,
                'message' => 'Registro guardado correctamente'
            ], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Livestock  $livestock
     * @return \Illuminate\Http\Response
     */
    public function show(Livestock $livestock)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Livestock  $livestock
     * @return \Illuminate\Http\Response
     */
    public function edit(Livestock $livestock)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Livestock  $livestock
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Livestock $livestock)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'identification' => 'required',
            'description' => 'required',
            'sex' => 'required',
            'breed_id' => 'required',
            'status' => 'required',
        ]);

        $updated = $livestock->fill($request->all())->save();

        /* $livestock->name = $request->name;
        $livestock->identification = $request->identification;
        $livestock->description = $request->description;
        $livestock->sex = $request->sex;
        $livestock->birth_date = $request->birth_date;
        $livestock->purchase_date = $request->purchase_date;
        $livestock->death_date = $request->death_date;
        $livestock->breed_id = $request->breed_id;
        $livestock->user_id = Auth::user()->id;
        $livestock->status = $request->status; */

        if ($updated) {
            return response()->json([
                'message' => 'Registro actualizado correctamente'
            ], 200);
        }
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Livestock  $livestock
     * @return \Illuminate\Http\Response
     */
    public function destroy(Livestock $livestock)
    {
        try {
            $livestock->delete();
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
