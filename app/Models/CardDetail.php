<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CardDetail extends Model
{
    //
    protected $fillable = ['user_id','card_number','expire_date','cvv','card_holder'];
}
