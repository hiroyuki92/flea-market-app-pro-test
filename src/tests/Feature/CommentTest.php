<?php

namespace Tests\Feature;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    private $items;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\CategoriesTableSeeder::class);
        $this->user = User::factory()->create();
        $this->seed(\Database\Seeders\ItemsTableSeeder::class);
        $this->items = Item::all();
    }

    public function test_a_logged_in_user_can_post_a_comment()
    {
        $this->actingAs($this->user);
        $item = $this->items->first();
        $initialCount = $item->comments()->count();
        $response = $this->post(route('comment.store', $item->id), [
            'comment' => 'This is a test comment.',
        ]);
        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('comments', [
            'user_id' => $this->user->id,
            'item_id' => $item->id,
            'content' => 'This is a test comment.',
        ]);
        $this->assertEquals($initialCount + 1, $item->fresh()->comments()->count());
    }

    public function test_guest_user_cannot_post_comment()
    {
        $item = $this->items->first();
        $initialCount = $item->comments()->count();
        $response = $this->post(route('comment.store', $item->id), [
            'comment' => 'This is a test comment.',
        ]);
        $response->assertRedirect(route('login'));
        $this->assertEquals($initialCount, $item->fresh()->comments()->count());
        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'content' => 'This is a test comment.',
        ]);
    }

    public function test_comment_cannot_be_empty()
    {
        $this->actingAs($this->user);
        $item = $this->items->first();
        $response = $this->post(route('comment.store', $item->id), [
            'comment' => '',
        ]);
        $response->assertRedirect();
        $response->assertSessionHasErrors(['comment']);
        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'content' => '',
        ]);
    }

    public function test_comment_cannot_exceed_255_characters()
    {
        $this->actingAs($this->user);
        $item = $this->items->first();
        $longComment = str_repeat('a', 256);
        $response = $this->post(route('comment.store', $item->id), [
            'comment' => $longComment,
        ]);
        $response->assertRedirect();
        $response->assertSessionHasErrors(['comment']);
        $this->assertDatabaseMissing('comments', [
            'item_id' => $item->id,
            'content' => $longComment,
        ]);
    }
}
