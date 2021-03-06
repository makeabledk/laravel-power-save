<?php

namespace Makeable\LaravelPowerSave\Tests\Stubs;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $guarded = [];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function servers()
    {
        return $this->belongsToMany(Server::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
