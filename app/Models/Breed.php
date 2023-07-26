<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class Breed extends Model
{
    use HasFactory;
    //use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'status',
    ];

    public function livestock()
    {
        return $this->hasMany(\App\Models\Livestock::class);
    }
}
