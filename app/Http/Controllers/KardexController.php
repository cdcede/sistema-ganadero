<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\LivestockMedicine;

class KardexController extends Controller
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
        return view('pages.kardex.index');
    }

    public function getKardex()
    {

        /* $entradas = DB::table('medicine_entries as me')
            ->join('medicines as m', 'm.id', '=', 'me.medicine_id')
            ->join('marks', 'marks.id', '=', 'm.mark_id')
            ->select('me.id', 'me.quantity', 'me.created_at', 'm.name', 'marks.name as mark')
            ->get();

        $salidas = DB::table('livestock_medicines as lm')
            ->join('medicine_entries as me', 'me.id', '=', 'lm.medicine_entry_id')
            ->join('medicines as m', 'm.id', '=', 'me.medicine_id')
            ->join('marks', 'marks.id', '=', 'm.mark_id')
            ->select('me.id', 'lm.dose', 'lm.date', 'm.name', 'marks.name as mark')
            ->get();
        */
        $kardex = DB::select("SELECT mee.id, m.name as marca, me.name as medicina, expiration_date, quantity as ingreso, sum(dose) as egreso, quantity - sum(dose) as stock
        FROM livestock_medicines lm
        INNER JOIN medicine_entries mee ON mee.id = lm.medicine_entry_id
        INNER JOIN medicines me ON me.id = mee.medicine_id
        INNER JOIN marks m ON m.id = me.mark_id
        group by mee.id, m.name, me.name, expiration_date, quantity");

        /* $kardex2 = DB::table('livestock_medicines as lm')
            ->select('mee.id', 'm.name as marca', 'me.name as medicina', 'expiration_date', 
            'quantity as ingreso',
            DB::raw("SUM(dose) as egreso"),
            DB::raw("quantity - SUM(dose) as stock"))
            ->join('medicine_entries as mee', 'mee.id', '=', 'lm.medicine_entry_id')
            ->join('medicines as me', 'me.id', '=', 'mee.medicine_id')
            ->join('marks as m', 'm.id', '=', 'me.mark_id')
            ->groupBy('mee.id', 'm.name', 'me.name', 'expiration_date', 'quantity')
            ->get(); */

        /* $kardex3 = LivestockMedicine::join('medicine_entries as mee', 'mee.id', '=', 'livestock_medicines.medicine_entry_id')
            ->join('medicines as me', 'me.id', '=', 'mee.medicine_id')
            ->join('marks as m', 'm.id', '=', 'me.mark_id')
            ->select('mee.id', 'm.name as marca', 'me.name as medicina', 'expiration_date', 
            'quantity as ingreso',
            DB::raw("SUM(dose) as egreso"),
            DB::raw("quantity - SUM(dose) as stock"))
            ->groupBy('mee.id', 'm.name', 'me.name', 'expiration_date', 'quantity')
            ->get(); */

        return response()->json([
            //'entradas' => $entradas,
            //'salidas' => $salidas,
            'data' => $kardex,
            'message' => 'Consulta ejecutada correctamente'
        ], 200);
    }

}
