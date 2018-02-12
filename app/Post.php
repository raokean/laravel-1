<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    //
    use Sluggable;

    protected $fillable = [
        'title',
        'content',
        'user_id',
        'description',
        'public_date',
        'un_public_date'
    ];
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

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comment()
    {
        return $this->hasMany(Comment::class);
    }

    public static function statusList()
    {
        return [ '0' => 'не опубликованный', '1' => 'черновик', '2' => 'опубликованный',];
    }

    public function tags()
    {
        return $this->belongsToMany(
            Tag::class,
            'post_tags',
            'post_id',
            'teg_id'
        );
    }

    public static function add($fields)
    {
        $post = new static;
        $post->fill($fields);
        $post->save();

        return $post;
    }

    protected function removeImage()
    {
        if ($this->image != null)
        {
            Storage::delete('uploads/images/'.$this->image);
        }
    }

    public function edit($fields)
    {
        $this->fill($fields);
        $this->save();
    }

    public function remove()
    {
        $this->removeImage();
        $this->delete();
    }

    public function uploadImage($image)
    {
        if($image == null) { return; }

        $this->removeImage();
        $filename = str_random(10).'.'.$image->extension();
        $image->storeAs('uploads/images/', $filename);
        $this->image = $filename;
        $this->save();
    }

    public function setCategory($id)
    {
        if($id < 1) { return; }

        $this->category_id = $id;
        $this->save();
    }

    public function setTags($ids)
    {
        if (empty($ids)) { return; }

        $this->tags()->sync($ids);
    }

    public function toggleFeatured($featured)
    {

        if ($featured != null)
        {
            $this->is_featured = $featured;
            $this->save();
        }
        else return;
    }

    public function toggleStatus($status)
    {
        if (is_numeric($status))
        {
            $this->status = $status;
            $this->save();
        }
        else return;
    }

    public function setFeature($feature)
    {
        if (is_numeric($feature))
        {
            $this->is_feature = $feature;
            $this->save();
        }
        else return;
    }

    public function getImage()
    {
        $baseUrl = url('/');
        if ($this->image == null)
        {
            return $baseUrl.'/uploads/images/no-image.png';
        }
        return $baseUrl.'/uploads/images/'.$this->image;
    }

    public function getCategoryTitle()
    {
        return ($this->category != null)
            ?   $this->category->title
            :   'Нет категории';
    }

    public function getTagTitles()
    {
        return (!$this->tags->isEmpty())
            ?   implode(', ', $this->tags->pluck('title')->all())
            : 'Нет тегов';
    }

    public function getDate()
    {
        return Carbon::createFromFormat('d/m/y', $this->public_date)->format('F, d Y');
    }

    public function setPublicDateAttribute($value)
    {
        //dd($value);
        if ($value == null | empty($value)) {
            $value = date('Y-m-d H:i:s', time());
        }

        $this->attributes['public_date'] = $value;
        //dd($date);

    }

//    public function getPublicDateAttribute($value)
//    {
//        $date = Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('d.m.Y H:i:s');
//        return $date;
//    }

    public function hasPrevious()
    {
        return self::where('id', '<', $this->id)->max('id');
    }

    public function getPrevious()
    {
        return self::find($this->hasPrevious());
    }

    public function hasNext()
    {
        return self::where('id', '>', $this->id)->min('id');
    }

    public function getNext()
    {
        return self::find($this->hasNext());
    }

    public function related()
    {
        return self::all()->except($this->id);
    }

    public function hasCategory()
    {
        return $this->category != null ? true : false;
    }

    public static function getPopularPosts()
    {
        return self::orderBy('views', 'desc')->take(3)->get();
    }

    public static function getLatestPosts()
    {
        return self::orderBy('public_date', 'desc')->take(4)->get();
    }

    public static function getFeaturesPosts()
    {
        return self::where('is_featured', 1)->take(3)->get();
    }

    public static function getCategoryPosts()
    {
        return Category::all();
    }

    public function getComments()
    {
        return $this->comment()->where('status', 2)->get();
    }


}
