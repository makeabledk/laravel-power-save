<?php

namespace Makeable\LaravelPowerSave\Tests\Stubs;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $guarded = [];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class);
    }
}
