<?php

namespace App\Http\Controllers;

use App\Models\Breed;
use Illuminate\Http\Request;

class BreedController extends Controller
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

        return view('pages.razas.index');

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $breeds = Breed::get();

        return response()->json([
            'data'    => $breeds,
            'message' => 'Consulta ejecutada correctamente'
        ], 200);
    }

    public function getBreeds()
    {
        $breeds = Breed::where('status', true)->get();
        return response()->json([
            'data'    => $breeds,
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
            'name' => 'required|max:50|iunique:breeds,name',
            'status' => 'required',
        ]);
 
        $breed = new Breed();
        $breed->name = $request->name;
        $breed->status = $request->status;

        if ($breed->save()){
            return response()->json([
                'breed'    => $breed,
                'message' => 'Registro guardado correctamente'
            ], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Breed  $breed
     * @return \Illuminate\Http\Response
     */
    public function show(Breed $breed)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Breed  $breed
     * @return \Illuminate\Http\Response
     */
    public function edit(Breed $breed)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Breed  $breed
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Breed $breed)
    {
        $this->validate($request, [
            'name' => 'required',
            'status' => 'required',
        ]);

        $breed->name = $request->name;
        $breed->status = $request->status;

        if ($breed->save()) {
            return response()->json([
                'message' => 'Registro actualizado correctamente'
            ], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Breed  $breed
     * @return \Illuminate\Http\Response
     */
    public function destroy(Breed $breed)
    {
        /* try {
            //code...
        } catch (\Throwable $th) {
            //throw $th;
        } */
        try {

            $breed->delete();
            return response()->json([
                'message' => 'Registro eliminado correctamente'
            ], 200);

        } catch (\Exception $e) {

            if ($e->getCode() == 23000) {
                return response()->json([
                    'success' => false,
                    'exception' => $e,
                    'errors' => $message = ['Error: No se puede eliminar un registro con historial.']
                ],500);
            }            
        }
    }
}
