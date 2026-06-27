<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected $product;

    protected function setUp(): void
    {
        parent::setUp();
        
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

    public function test_can_view_empty_cart()
    {
        $response = $this->get(route('cart.index'));
        
        $response->assertStatus(200);
        $response->assertSee('カートは空です');
    }

    public function test_can_add_product_to_cart()
    {
        $response = $this->post(route('cart.add'), [
            'product_id' => $this->product->id,
            'qty' => 2,
        ]);
        
        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('ok', 'カートに追加しました');
        
        // Verify cart contains the product
        $this->assertNotEmpty(session('cart'));
        $this->assertEquals(2, session('cart')[$this->product->id]['qty']);
    }

    public function test_can_view_cart_with_items()
    {
        // Add product to cart
        $this->post(route('cart.add'), [
            'product_id' => $this->product->id,
            'qty' => 1,
        ]);
        
        $response = $this->get(route('cart.index'));
        
        $response->assertStatus(200);
        $response->assertSee($this->product->name);
        $response->assertSee('¥1,000');
    }

    public function test_can_update_cart_quantity()
    {
        // Add product to cart
        $this->post(route('cart.add'), [
            'product_id' => $this->product->id,
            'qty' => 1,
        ]);
        
        // Update quantity
        $response = $this->post(route('cart.update'), [
            'lines' => [
                $this->product->id => 3,
            ],
        ]);
        
        $response->assertSessionHas('ok', '数量を更新しました');
        $this->assertEquals(3, session('cart')[$this->product->id]['qty']);
    }

    public function test_can_remove_product_from_cart()
    {
        // Add product to cart
        $this->post(route('cart.add'), [
            'product_id' => $this->product->id,
            'qty' => 1,
        ]);
        
        // Remove product
        $response = $this->post(route('cart.remove'), [
            'product_id' => $this->product->id,
        ]);
        
        $response->assertSessionHas('ok', '削除しました');
        $this->assertEmpty(session('cart'));
    }

    public function test_cart_calculates_total_correctly()
    {
        $product2 = Product::create([
            'name' => 'Test Product 2',
            'description' => 'Test description 2',
            'price' => 2000,
            'stock' => 50,
            'category_id' => $this->product->category_id,
            'is_active' => true,
        ]);
        
        // Add products to cart
        $this->post(route('cart.add'), [
            'product_id' => $this->product->id,
            'qty' => 2,
        ]);
        
        $this->post(route('cart.add'), [
            'product_id' => $product2->id,
            'qty' => 1,
        ]);
        
        $response = $this->get(route('cart.index'));
        
        // Total should be (1000 * 2) + (2000 * 1) = 4000
        $response->assertSee('¥4,000');
    }

    public function test_adding_same_product_increases_quantity()
    {
        // Add product first time
        $this->post(route('cart.add'), [
            'product_id' => $this->product->id,
            'qty' => 1,
        ]);
        
        // Add same product again
        $this->post(route('cart.add'), [
            'product_id' => $this->product->id,
            'qty' => 2,
        ]);
        
        // Quantity should be 1 + 2 = 3
        $this->assertEquals(3, session('cart')[$this->product->id]['qty']);
    }

    public function test_cart_validation_requires_product_id()
    {
        $response = $this->post(route('cart.add'), [
            'qty' => 1,
        ]);
        
        $response->assertSessionHasErrors('product_id');
    }

    public function test_cart_validation_requires_valid_product()
    {
        $response = $this->post(route('cart.add'), [
            'product_id' => 99999, // Non-existent product
            'qty' => 1,
        ]);
        
        $response->assertSessionHasErrors('product_id');
    }
}
