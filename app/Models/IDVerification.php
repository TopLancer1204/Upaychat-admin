<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IDVerification extends Model
{
    //
    protected $table = "id_verifications";
    protected $fillable = ['user_id', 'verify_code', 'street', 'city', 'state', 'zipcode', 'country', 'status'];

    public function metadata()
    {
        return $this->hasMany('App\Models\IDVerificationMeta','verify_id');
    }
    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}
