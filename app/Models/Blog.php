<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'blog_title',
        'blog_description',
        'blog_tags',
        'blog_type',
        'blog_content',
        'blog_image',
        'blog_author',
        'blog_slug',
        'blog_categoryId',
        'blog_status',
        'updated_at',
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function userName()
    {
        return $this->hasOne('App\Models\User', 'id', 'blog_author');
    }
}
