<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    public function parentuser()
    {
        return $this->belongsTo('App\Models\User');
    }
}
