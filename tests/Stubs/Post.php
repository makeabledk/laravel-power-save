<?php

namespace Makeable\LaravelPowerSave\Tests\Stubs;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $guarded = [];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function meta()
    {
        return $this->hasMany(PostMeta::class);
    }

    public function images()
    {
        return $this->belongsToMany(Image::class);
    }

    public function tags()
    {
        return $this->morphMany(Tag::class, 'taggable');
    }

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }
}
