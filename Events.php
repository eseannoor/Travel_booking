<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    



    protected $fillable = [
        'Type','Categorie','Reference', 'image','E_title','price','bedroom','bathroom','Street_Address','City','State','sqft','Furnishing','build_year','about','checkIn','checkOut','postor'];

}
