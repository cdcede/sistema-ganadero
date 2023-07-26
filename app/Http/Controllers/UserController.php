<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LivestockMedicine;
use App\Models\Livestock;
use App\Models\Medicine;
//use App\Models\Role;
use DB;

class UserController extends Controller
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

        return view('pages.users.index');

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::select('users.id','users.username', 'users.first_name','users.last_name', 
        'email', 'password','users.status', 'roles.id as role_id','roles.slug as rol')
            ->join('role_user', 'users.id','=','role_user.user_id')
            ->join('roles', 'roles.id','=','role_user.role_id')
            ->where('users.id','!=',1)
            ->orderBy('users.id', 'DESC')->get();

        return response()->json([
            'data'    => $users,
            'message' => 'Consulta ejecutada correctamente'
        ], 200);
        
        /* return [
            'pagination' => [
                'total'         => $users->total(),
                'current_page'  => $users->currentPage(),
                'per_page'      => $users->perPage(),
                'last_page'     => $users->lastPage(),
                'from'          => $users->firstItem(),
                'to'            => $users->lastItem(),
            ],
            'users' => $users
        ]; */
    }

    public function getUsers()
    {
        $users = User::select('id',
        DB::raw("CONCAT(first_name,' ',last_name) as name"))
            ->where('status', true)->get();
        return response()->json([
            'data'    => $users,
            'message' => 'Consulta ejecutada correctamente'
        ], 200);
    }

    public function showDashboard()
    {
        $users = User::get();
        $usersCount = $users->count();
        $livestockMedicines = LivestockMedicine::get();
        $livestockMedicinesCount = $livestockMedicines->count();
        $livestocks = Livestock::get();
        $livestocksCount = $livestocks->count();
        $medicines = Medicine::get();
        $medicinesCount = $medicines->count();
        
        return response()->json([
            'data'    => [
                'users' => $usersCount,
                'livestockMedicines' => $livestockMedicinesCount,
                'livestocks' => $livestocksCount,
                'medicines' => $medicinesCount
            ],
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
            'username' => 'required|max:50|unique:users',
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'email' => 'required|unique:users',
            'password' => 'required',
            'password_confirmation' => 'required|same:password',
            'status' => 'required',
        ]);
 
        $user = new User();
        $user->username = $request->username;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->status = $request->status;

        $userRole = config('roles.models.role')::where('id', '=', $request->role_id)->first();

        if ($user->save()){
            $user->attachRole($userRole);
            return response()->json([
                'user'    => $user,
                'message' => 'Registro guardado correctamente'
            ], 200);
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $this->validate($request, [
            'username' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required',
            //'password' => 'required',
            //'password_confirmation' => 'required|same:password',
            'status' => 'required',
        ]);

        $user->username = $request->username;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        if ($request->password) {
            $user->password = bcrypt($request->password);
        }
        $user->status = $request->status;

        if ($user->save()) {

            $updateUserRole = DB::table('role_user')
              ->where('user_id', '=', $user->id)
              ->update(['role_id' =>  $request->role_id]);
            
            return response()->json([
                'message' => 'Registro actualizado correctamente'
            ], 200);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();
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

    public function getRoles(){
        $roles = DB::table('roles')
            ->select('*')
            ->where('id','!=',1)
            ->get();

        return response()->json([
            'data'    => $roles,
            'message' => 'Consulta ejecutada correctamente'
        ], 200);
    }
}
