<?php

namespace Tests;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Models\User;

class UsersTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_login_success()
    {
        $response = $this->call('POST', '/api/login', ['email' => 'admin@gmail.com', 'password' => '12345678']);
        $this->assertEquals(200, $response->status());
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_login_failed()
    {
        $response = $this->call('POST', '/api/login', ['email' => 'test', 'password' => 'test']);
        $this->assertEquals(401, $response->status());
    }


    /**
     *
     */
    public function test_follow_success()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $response = $this->actingAs($user)->call('POST', '/api/user/follow', ['follower_id' => $user2->id]);
        $this->assertEquals(200, $response->status());
    }

    /**
     *
     */
    public function test_follow_failed()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->call('POST', '/api/user/follow', ['follower_id' => 1000]);
        $this->assertEquals(500, $response->status());
    }

    public function test_unfollow_success()
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        $this->actingAs($user)->call('POST', '/api/user/follow', ['follower_id' => $user2->id]);
        $response = $this->actingAs($user)->call('POST', '/api/user/unfollow', ['user_id' => $user2->id]);

        $this->assertEquals(200, $response->status());
    }

    public function test_unfollow_failed()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->call('POST', '/api/user/unfollow', ['user_id' => 1000]);

        $this->assertEquals(500, $response->status());
    }

    public function test_create_success() {
        $response = $this->call('POST', '/api/user/create', ['name' => 'quang_test', 'email' => 'quang_email' . rand(1, 1000) . '@gmail.com', 'password' => '12345678']);
        $this->assertEquals(200, $response->status());
    }

    public function test_create_failed() {
        $response = $this->call('POST', '/api/user/create', ['name' => 'quang_test', 'email' => 'quang_email@gmail.com']);
        $this->assertEquals(500, $response->status());
    }

}
