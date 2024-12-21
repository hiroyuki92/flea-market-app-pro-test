<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;

class AddressUpdateTest extends TestCase
{
    use RefreshDatabase;

    private $user;
    private $item;
    private $newAddress;
    private $paymentMethod = 'credit_card';

    protected function setUp(): void
{
    parent::setUp();
    $this->seed(\Database\Seeders\CategoriesTableSeeder::class);
    $this->user = User::factory()->create();
    $this->seed(\Database\Seeders\ItemsTableSeeder::class);
    $this->item = Item::first();
}

public function test_user_can_update_address_and_see_it_in_checkout()
{
    $this->actingAs($this->user);
    $response = $this->get(route('address.edit', ['item_id' => $this->item->id]));
    $response->assertStatus(200);

    $newAddress = [
        'postal_code' => '123-4567',
        'address_line' => '東京都千代田区',
        'building' => 'テストビル101',
    ];
    
    $response = $this->put(route('address.update', ['item_id' => $this->item->id]), $newAddress);
    $response->assertStatus(302);

    $response = $this->get(route('purchase.index', ['item_id' => $this->item->id]));
    $response->assertStatus(200);
    $response->assertSee($newAddress['postal_code']);
    $response->assertSee($newAddress['address_line']);
    $response->assertSee($newAddress['building']);
}

public function test_order_has_correct_shipping_address()
{
    $this->actingAs($this->user);
    
    $newShippingAddress = [
        'shipping_postal_code' => '123-4567',
        'shipping_address_line' => '東京都千代田区',
        'shipping_building' => 'テストビル101',
    ];
    
    $response = $this->put(route('address.update', ['item_id' => $this->item->id]), $newShippingAddress);
    $response->assertStatus(302);

    $sessionData = [
        'shipping_postal_code' => $newShippingAddress['shipping_postal_code'],
        'shipping_address_line' => $newShippingAddress['shipping_address_line'],
        'shipping_building' => $newShippingAddress['shipping_building']
    ];
    session(['shipping_address' => $sessionData]);

    $response = $this->get(route('purchase.index', ['item_id' => $this->item->id]));
    $response->assertStatus(200);

    $purchaseData = [
        'user_id' => $this->user->id,
        'item_id' => $this->item->id,
        'shipping_postal_code' => $newShippingAddress['shipping_postal_code'],
        'shipping_address_line' => $newShippingAddress['shipping_address_line'],
        'shipping_building' => $newShippingAddress['shipping_building'],
        'payment_method' => 'card',
    ];

    $response = $this->post(route('purchase.store', $this->item->id), $purchaseData);
    $response->assertStatus(302);

    $this->assertDatabaseHas('purchases', [
        'shipping_postal_code' => $newShippingAddress['shipping_postal_code'],
        'shipping_address_line' => $newShippingAddress['shipping_address_line'],
        'shipping_building' => $newShippingAddress['shipping_building'],
    ]);
}
}