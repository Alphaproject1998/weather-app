<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    //public function test_user_can_be_created()
    //{
    //    $user = User::factory()->count(1)->make()->first();
//
    //    $this->assertTrue(isset($user));
    //}
    public function test_user_can_login()
    {
        User::factory()->create([
            'username' => 'John.Doe',
            'password' => bcrypt('password')
        ]);

        $response = $this->post('/api/auth/login', [
            'username' => 'John.Doe',
            'password' => 'password'
        ]);

        $response->assertSuccessful();
    }
    //public function test_user_can_register()
    //{
    //    $response = $this->postJson('/api/auth/register', array([
    //        'name' => 'John Doe',
    //        'username' => 'John.Doe',
    //        'email' => 'john@doe.com',
    //        'password' => 'Password123',
    //        'password_confirmation' => 'Password123',
    //    ]));
//
    //    $response->assertStatus(201);
    //}
}
