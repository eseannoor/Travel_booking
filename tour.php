<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class tour extends Model
{
    protected $fillable = ['Reference', 'Title', 'Package', 'image','period', 'price', 'Start', 'Ends', 'Includes', 'Excludes', 'created_at', 'updated_at'];
}
