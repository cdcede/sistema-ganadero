<?php

namespace App\Http\Controllers;

use App\Models\Mark;
use Illuminate\Http\Request;

class MarkController extends Controller
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

        return view('pages.marcas.index');

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $marks = Mark::get();

        return response()->json([
            'data'    => $marks,
            'message' => 'Consulta ejecutada correctamente'
        ], 200);
    }

    public function getMarks()
    {
        $marks = Mark::where('status', true)->get();
        return response()->json([
            'data'    => $marks,
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
            'name' => 'required|max:50|iunique:marks,name',
            'status' => 'required',
        ]);
 
        $mark = new Mark();
        $mark->name = $request->name;
        $mark->status = $request->status;

        if ($mark->save()){
            return response()->json([
                'mark'    => $mark,
                'message' => 'Registro guardado correctamente'
            ], 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Mark  $mark
     * @return \Illuminate\Http\Response
     */
    public function show(Mark $mark)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Mark  $mark
     * @return \Illuminate\Http\Response
     */
    public function edit(Mark $mark)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Mark  $mark
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Mark $mark)
    {
        $this->validate($request, [
            'name' => 'required',
            'status' => 'required',
        ]);

        $mark->name = $request->name;
        $mark->status = $request->status;

        if ($mark->save()) {
            return response()->json([
                'message' => 'Registro actualizado correctamente'
            ], 200);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Mark  $mark
     * @return \Illuminate\Http\Response
     */
    public function destroy(Mark $mark)
    {
        try {
            $mark->delete();
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
