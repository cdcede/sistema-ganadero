<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class Livestock extends Model
{
    use HasFactory;
    //use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'identification',
        'description',
        'sex',
        'birth_date',
        'purchase_date',
        'death_date',
        'breed_id',
        'user_id',
        'image',
        'status',
    ];

    public function breed()
    {
        return $this->belongsTo(\App\Models\Breed::class);
    }

    public function livestock_medicines()
    {
        return $this->hasMany(\App\Models\LivestockMedicine::class);
    }

    public function scopeRaza($query, $searchTerm)
    {
        return $query->where('breed_id', 'LIKE', "%$searchTerm%");
    }
}
