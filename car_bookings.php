<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class car_bookings extends Model
{
   protected $fillable = ['id', 'Reference', 'CarModel', 'Name', 'Email', 'Pickup_date', 'Return_date', 'Status', 'created_at', 'updated_at','User'];
}
