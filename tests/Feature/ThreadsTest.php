<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ThreadsTest extends TestCase
{
    use DatabaseMigrations;

    /* @test */
    public function test_a_user_can_browse_threads()
    {
        $thread = factory('App\Thread')->create();
        $response = $this->get('/threads');
        $response->assertSee($thread->title);
    }

    public function test_a_user_can_read_a_threads()
    {
        $thread = factory('App\Thread')->create();
        $response = $this->get('/threads/'.$thread->id);
        $response->assertSee($thread->title);
    }
}
