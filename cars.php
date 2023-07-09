<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class cars extends Model
{
protected $fillable = ['Id', 'Reference', 'CarModel', 'Location', 'Date','price', 'image', 'Company', 'Created_at', 'Updated_at','Fuel'];
}
