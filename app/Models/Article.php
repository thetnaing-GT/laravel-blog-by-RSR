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
    'user_id',
];

    public function category()
    {
        return $this->belongsTo('App\Models\Category');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function user()
    {
           return $this->belongsTo(User::class);
    }

    public function scopeWithCapitalizedTitle($query)
    {
        return $query->selectRaw('*, CONCAT(UPPER(LEFT(title, 1)), SUBSTRING(title, 2)) as title');
    }


    public function getCapitalizedTitleAttribute()
    {
        return ucfirst(strtolower($this->title));
    }

}
