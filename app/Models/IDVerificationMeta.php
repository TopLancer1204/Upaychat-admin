<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IDVerificationMeta extends Model
{
    //
    protected $table = "id_verifications_meta";
    protected $fillable = ['verify_id', 'path', 'type'];
}
