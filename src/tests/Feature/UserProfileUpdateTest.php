<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UserProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_view_profile_with_default_values()
    {
        $this->actingAs($this->user);
        $response = $this->get(route('profile.edit'));
        $response->assertStatus(200)
            ->assertSee($this->user->name)
            ->assertSee($this->user->postal_code)
            ->assertSee($this->user->address_line)
            ->assertSee($this->user->building)
            ->assertSee($this->user->profile_image, false);
    }
}
