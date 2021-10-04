<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingSms extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mobile',
        'message',
    ];
}
