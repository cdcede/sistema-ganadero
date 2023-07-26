<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'expiration_date',
        'quantity',
        'medicine_id',
        'status',
    ];

    public function medicine()
    {
        return $this->belongsTo(\App\Models\Medicine::class);
    }

    public function livestock_medicines()
    {
        return $this->hasMany(\App\Models\LivestockMedicine::class);
    }

    /* public function scopeConMedicina($query, $searchTerm)
    {
        return $query->whereHas('medicine', function($query) use ($searchTerm) {
            $query->marca($searchTerm);
        });
    } */

    public function scopeMedicina($query, $searchTerm)
    {
        return $query->where('medicine_id', 'LIKE', "%$searchTerm%");
    }

    public function scopeMedicinaConMarca($query, $searchTerm)
    {

        /* return $query->whereHas('medicine', function ($q) use($searchTerm) {
            $q->whereHas('marca', function ($q) use($searchTerm){
                $q->where('id', $searchTerm);
            });
        }); */
        return $query->whereHas('medicine', function($query) use ($searchTerm) {
            $query->marca($searchTerm);
        });
    }

    public function scopeConCategoria($query, $searchTerm)
    {
        return $query->whereHas('medicine', function($query) use ($searchTerm) {
            $query->categoria($searchTerm);
        });
    }
}
