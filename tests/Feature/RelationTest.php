<?php

namespace Makeable\LaravelPowerSave\Tests\Feature;

use Makeable\LaravelPowerSave\PowerSave;
use Makeable\LaravelPowerSave\Tests\Stubs\User;
use Makeable\LaravelPowerSave\Tests\TestCase;

class RelationTest extends TestCase
{
    /** @test **/
    public function it_works_with_nested_relations()
    {
        $user = PowerSave::make(User::class)
            ->with('posts.comments')
            ->save([
                'name' => 'Makeable',
                'posts' => [
                    ['name' => 'My first post'],
                    [
                        'name' => 'My second post',
                        'comments' => [
                            ['body' => 'Awesome post'],
                        ],
                    ],
                ],
            ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals(2, $user->posts->count());
        $this->assertEquals(0, $user->posts->get(0)->comments->count());
        $this->assertEquals(1, $user->posts->get(1)->comments->count());
        $this->assertEquals('Awesome post', $user->posts->get(1)->comments->get(0)->body);
    }

    /** @test **/
    public function it_can_apply_all_given_relations_using_with_all()
    {
        $user = PowerSave::make(User::class)
            ->withAllNested()
            ->save([
                'name' => 'Makeable',
                'posts' => [
                    [
                        'name' => 'My first post',
                        'comments' => [
                            ['body' => 'Awesome post'],
                        ],
                    ],
                    [
                        'name' => 'My second post',
                    ],
                ],
            ]);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals(2, $user->posts->count());
        $this->assertEquals(1, $user->posts->get(0)->comments->count());
        $this->assertEquals('Awesome post', $user->posts->get(0)->comments->get(0)->body);
        $this->assertEquals(0, $user->posts->get(1)->comments->count());
    }
}
