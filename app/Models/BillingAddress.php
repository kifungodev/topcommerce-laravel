<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingAddress extends Model
{
    use HasFactory;

    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function countryState(){
        return $this->belongsTo(CountryState::class,'state_id');
    }

    public function city(){
        return $this->belongsTo(City::class);
    }




}
