<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class UserLogoutTest extends TestCase
{
    public function test_user_can_logout()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/logout');
        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }
}
