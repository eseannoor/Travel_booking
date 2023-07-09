<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class hotel_booking extends Model
{
    protected $fillable = [
        "Reference", "Hotel_name", "checkIn", "checkOut", "Name", "Email", "Tel", "DOB", "Number_of_person", "status","User"
    ];
}
