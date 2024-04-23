<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pollutant extends Model
{
    use HasFactory;
    protected $fillable = ['name'];

    public function emissions(){
        return $this->hasMany(Emission::class);
    }
}
