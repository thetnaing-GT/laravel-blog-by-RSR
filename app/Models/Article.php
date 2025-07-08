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

    public function scopeWithCapitalizedTitle($query)
    {
        // define scope logic in model
        // define the scope withCapitalizedTitle(), we are telling Laravel to modify the query to create a new virtual field (not a real field in the database) that holds the capitalized version of the article's title.
        return $query->selectRaw('*, CONCAT(UPPER(LEFT(title, 1)), LOWER(SUBSTRING(title, 2))) as capitalized_title');
    }

}
