<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tour_bookings extends Model
{
    protected $fillable = ['Reference','Title', 'Name', 'Email', 'Pickup_date', 'Return_date', 'User', 'price', 'created_at', 'updated_at'];
}
