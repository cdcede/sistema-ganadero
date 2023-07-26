<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class LivestockMedicine extends Model
{
    use HasFactory;
    //use HasFactory, SoftDeletes;

    protected $fillable = [
        'date',
        'dose',
        'livestock_id',
        'medicine_id',
        'user_id',
    ];

    public function livestock()
    {
        return $this->belongsTo(\App\Models\Livestock::class);
    }

    public function medicineEntry()
    {
        return $this->belongsTo(\App\Models\MedicineEntry::class);
    }

    public function scopeGanado($query, $searchTerm)
    {
        return $query->where('livestock_id', '=', "$searchTerm");
    }

    public function scopeMedicinaEntry($query, $searchTerm)
    {
        return $query->where('medicine_entry_id', 'LIKE', "%$searchTerm%");
    }

    public function scopeUsuario($query, $searchTerm)
    {
        return $query->where('user_id', 'LIKE', "%$searchTerm%");
    }

    public function scopeConMedicina($query, $searchTerm)
    {
        return $query->whereHas('medicineEntry', function($query) use ($searchTerm) {
            $query->medicina($searchTerm);
        });
    }

    public function scopeConRaza($query, $searchTerm)
    {
        return $query->whereHas('livestock', function($query) use ($searchTerm) {
            $query->raza($searchTerm);
        });
    }

    public function scopeEntradaMedicinaConMarca($query, $searchTerm)
    {
        return $query->whereHas('medicineEntry', function ($q) use($searchTerm) {
            $q->whereHas('medicinaConMarca', function ($q) use($searchTerm){
                $q2->whereHas('marca', function ($q2) use($searchTerm){
                    $q2->where('id', $searchTerm);
                });
            });
        });
    }
}
