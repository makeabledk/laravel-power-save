<?php

namespace Makeable\LaravelPowerSave\Tests\Stubs;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $guarded = [];

    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }
}
