<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;

class Category extends Model
{
    //
    use Sluggable;

    protected $fillable = ['title', 'status', 'description', 'public_date', 'un_public_date'];
    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable()
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public static function statusList()
    {
        return [ '0' => 'не опубликованный', '1' => 'черновик', '2' => 'опубликованный',];
    }
}
