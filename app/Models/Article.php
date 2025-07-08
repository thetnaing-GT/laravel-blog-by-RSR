<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
    'title',
    'body',
    'category_id',
    'image',
];

    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    // public function scopeWithCapitalizedTitle($query)
    // {
    //     // no query
    //     // to indicate ->withCapitalizedTitle() 

    //     return $query;
    // }



    public function scopeWithCapitalizedTitle($query)
    {
        return $query;
    }


    public function getCapitalizedTitleAttribute()
    {
        return ucfirst(strtolower($this->title));
    }

}
