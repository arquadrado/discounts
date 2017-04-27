<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{

    protected $fillable = [
        'name',
        'since',
        'revenue_in_dmcents'
    ];
    
     /*
    ==========================================================================
       Mutators
    ==========================================================================
    */

    public function getRevenueAttribute()
    {
        return $this->revenue_in_dmcents / 100;
    }
}
