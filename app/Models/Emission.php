<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Emission extends Model
{
    use HasFactory;

    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function year(){
        return $this->belongsTo(Year::class);
    }

    public function pollutant(){
        return $this->belongsTo(Pollutant::class);
    }
}
