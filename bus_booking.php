<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class bus_booking extends Model
{
        protected $fillable = [ 'Reference','Bus_company','Pickup','Dropoff','User','status','Created_at','Updated_at'];

}
