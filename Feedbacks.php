<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Feedbacks extends Model
{
      protected $fillable = [
        'Reference','Type','Sender','Email','Message','updated_at','User_Ref'
    ];

}
