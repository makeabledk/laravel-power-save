<?php

namespace Makeable\LaravelPowerSave\Tests\Feature;

use Makeable\LaravelPowerSave\PowerSave;
use Makeable\LaravelPowerSave\Tests\Stubs\Post;
use Makeable\LaravelPowerSave\Tests\Stubs\User;
use Makeable\LaravelPowerSave\Tests\TestCase;

class BelongsToTest extends TestCase
{
    /** @test **/
    public function it_creates_belongs_to_models()
    {
        $post = PowerSave::make(Post::class)
            ->with('author')
            ->save([
                'name' => 'My new blog post',
                'author' => [
                    'name' => 'Makeable'
                ]
            ]);

        $this->assertInstanceOf(Post::class, $post);
        $this->assertEquals('My new blog post', $post->name);

        $this->assertInstanceOf(User::class, $post->author);
        $this->assertEquals('Makeable', $post->author->name);
    }

    /** @test **/
    public function it_updates_existing_belongs_to_models()
    {
        $post = factory(Post::class)->with('author')->create();

        $data = $post->load('author')->toArray();
        $data['name'] = 'My new blog post';
        $data['author']['name'] = 'Makeable';

        $updated = PowerSave::make($post)
            ->with('author')
            ->save($data);

        $this->assertEquals($data['id'], $updated->id);
        $this->assertEquals($data['name'], $updated->name);
        $this->assertEquals($data['author']['id'], $updated->author->id);
        $this->assertEquals($data['author']['name'], $updated->author->name);
    }

    /** @test **/
    public function it_disassociates_existing_belongs_to_models()
    {
        $post = factory(Post::class)->with('author')->create();

        $data = $post->load('author')->toArray();
        $data['author'] = null;

        $updated = PowerSave::make($post)
            ->with('author')
            ->save($data);

        $this->assertEquals($data['id'], $updated->id);
        $this->assertNull($updated->author);
    }

//
//    /** @test **/
//    public function it_fills_using_custom_function()
//    {
//        $post = PowerSave::make(Post::class)
//            ->fillUsing(fn (Post $model, array $attributes) => $model->fill(array_merge($attributes, [
//                'body' => 'Some text'
//            ])))
//            ->save(['name' => 'My new blog post']);
//
//        $this->assertEquals('My new blog post', $post->name);
//        $this->assertEquals('Some text', $post->body);
//    }
}