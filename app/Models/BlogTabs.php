<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogTabs extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'blog_id',
        'title',
        'description',
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function userName()
    {
        return $this->hasOne('App\User', 'id', 'blog_author');
    }
}
