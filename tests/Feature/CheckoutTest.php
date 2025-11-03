<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        
        $category = Category::create(['name' => 'Test Category']);
        
        $this->product = Product::create([
            'name' => 'Test Product',
            'description' => 'Test description',
            'price' => 1000,
            'stock' => 50,
            'category_id' => $category->id,
            'is_active' => true,
        ]);
    }

    public function test_cannot_checkout_with_empty_cart()
    {
        $response = $this->actingAs($this->user)
            ->post(route('checkout.start'));
        
        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('error', 'カートが空です');
    }

    public function test_can_view_success_page()
    {
        $response = $this->get(route('checkout.success'));
        
        // Should redirect if no session_id provided
        $response->assertRedirect(route('store.index'));
    }

    public function test_can_view_cancel_page()
    {
        $response = $this->get(route('checkout.cancel'));
        
        $response->assertStatus(200);
        $response->assertSee('お支払いがキャンセルされました');
    }

    public function test_checkout_requires_authentication()
    {
        // Add product to session cart
        session(['cart' => [
            $this->product->id => ['qty' => 1]
        ]]);

        // Try to checkout without authentication
        $response = $this->post(route('checkout.start'));
        
        // Should redirect to login
        $response->assertRedirect(route('login'));
    }
}
