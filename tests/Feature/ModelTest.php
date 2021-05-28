<?php

namespace Makeable\LaravelPowerSave\Tests\Feature;

use Makeable\LaravelPowerSave\PowerSave;
use Makeable\LaravelPowerSave\Tests\Stubs\Post;
use Makeable\LaravelPowerSave\Tests\TestCase;

class ModelTest extends TestCase
{
    /** @test **/
    public function it_creates_models()
    {
        $post = PowerSave::make(Post::class)->save(['name' => 'My new blog post']);

        $this->assertInstanceOf(Post::class, $post);
        $this->assertEquals('My new blog post', $post->name);
    }

    /** @test **/
    public function it_updates_models()
    {
        $post = Post::create();

        $updated = PowerSave::make($post)->save(['name' => 'My new blog post']);

        $this->assertEquals($post->id, $updated->id);
    }

    /** @test **/
    public function it_fills_using_custom_function()
    {
        $post = PowerSave::make(Post::class)
            ->fillUsing(fn (Post $model, array $attributes) => $model->fill(array_merge($attributes, [
                'body' => 'Some text',
            ])))
            ->save(['name' => 'My new blog post']);

        $this->assertEquals('My new blog post', $post->name);
        $this->assertEquals('Some text', $post->body);
    }
}
