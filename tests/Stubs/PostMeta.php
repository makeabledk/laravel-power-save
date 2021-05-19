<?php

namespace Makeable\LaravelPowerSave\Tests\Stubs;

use Illuminate\Database\Eloquent\Model;

class PostMeta extends Model
{
    protected $guarded = [];

    protected $table = 'post_meta';

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
