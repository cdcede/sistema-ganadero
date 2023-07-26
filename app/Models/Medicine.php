<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class Medicine extends Model
{
    use HasFactory;
    //use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'mark_id',
        'category_id',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(\App\Models\Category::class);
    }

    public function mark()
    {
        return $this->belongsTo(\App\Models\Mark::class);
    }

    public function medicineEntry()
    {
        return $this->hasMany(\App\Models\MedicineEntry::class);
    }

    public function scopeMarca($query, $searchTerm)
    {
        return $query->where('mark_id', 'LIKE', "%$searchTerm%");
    }

    public function scopeCategoria($query, $searchTerm)
    {
        return $query->where('category_id', 'LIKE', "%$searchTerm%");
    }

}
