<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name'  => 'Test User',
            'email' => 'test@example.com',
        ]);

        $category = Category::create([
            'name' => 'Test Category',
        ]);

        $this->product = Product::create([
            'name'              => 'Test Product',
            'description'       => 'Test description',
            'price'             => 1000,
            'stock'             => 50,
            'category_id'       => $category->id,
            'is_active'         => true,
            'is_published'      => true,
            'is_stock_managed'  => true,
        ]);
    }

    public function test_cannot_checkout_with_empty_cart()
    {
        $response = $this->actingAs($this->user)
            ->post(route('checkout.start'));

        $response
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas(
                'error',
                'カートが空です。'
            );
    }

    public function test_can_view_success_page()
    {
        $response = $this->get(route('checkout.success'));

        $response->assertStatus(200);

        $response->assertSee('ご注文ありがとうございました');
    }

    public function test_can_view_cancel_page()
    {
        $response = $this->get(route('checkout.cancel'));

        $response->assertStatus(200);

        $response->assertSee('お支払いがキャンセルされました');
    }

    public function test_checkout_requires_authentication()
    {
        session([
            'cart' => [
                $this->product->id => [
                    'id'       => $this->product->id,
                    'name'     => $this->product->name,
                    'price'    => $this->product->price,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response = $this->post(route('checkout.start'));

        $response->assertRedirect(route('login'));
    }
}
