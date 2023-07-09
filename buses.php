<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class buses extends Model
{
     protected $fillable = ['Reference','pickup','destination','Bus_company','avalable_seat','Departure_time','Arrival_time','duration','updated_at','Date','price
     '];
}
