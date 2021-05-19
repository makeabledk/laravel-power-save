<?php

namespace Makeable\LaravelPowerSave\Tests\Feature;

use Makeable\LaravelPowerSave\PowerSave;
use Makeable\LaravelPowerSave\Tests\Stubs\Post;
use Makeable\LaravelPowerSave\Tests\Stubs\User;
use Makeable\LaravelPowerSave\Tests\TestCase;
use function Makeable\LaravelFactory\sequence;

class HasManyTest extends TestCase
{
    /** @test **/
    public function it_creates_has_many_models()
    {
        $user = PowerSave::make(User::class)
            ->with('posts')
            ->save([
                'name' => 'Makeable',
                'posts' => [
                    ['name' => 'My first post'],
                    ['name' => 'My second post'],
                ]
            ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals(2, $user->posts->count());

        $this->assertEquals('My first post', $user->posts->get(0)->name);
        $this->assertEquals('My second post', $user->posts->get(1)->name);
    }

    /** @test **/
    public function it_syncs_has_many_models()
    {
        $user = factory(User::class)
            ->with(1, 'posts', ['name' => 'My first post'])
            ->andWith(1, 'posts', ['name' => 'My second post'])
            ->create();

        $originalIds = $user->posts->pluck('id');

        $data = $user->toArray();
        unset($data['posts'][0]); // Remove first entry
        $data['posts'][1]['name'] = 'My second post amended'; // Update second
        $data['posts'][] = ['name' => 'My third post']; // Add new entry

        $user = PowerSave::make($user)
            ->with('posts')
            ->save($data);

        $this->assertEquals(2, $user->posts->count());

        $this->assertEquals($originalIds[1], $user->posts->get(0)->id);
        $this->assertEquals('My second post amended', $user->posts->get(0)->name);

        $this->assertEquals($originalIds[1] + 1, $user->posts->get(1)->id);
        $this->assertEquals('My third post', $user->posts->get(1)->name);

        $this->assertNull(Post::find($originalIds[0]));
    }

}