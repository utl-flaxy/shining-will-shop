<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $category = Category::create([
            'name' => 'Test Category',
        ]);

        $this->product = Product::create([
            'name' => 'Test Product',
            'description' => 'Test description',
            'price' => 1000,
            'stock' => 50,
            'category_id' => $category->id,
            'is_active' => true,
            'is_published' => true,
            'is_stock_managed' => true,
        ]);
    }

    public function test_can_view_empty_cart()
    {
        $response = $this->get(route('cart.index'));

        $response->assertStatus(200);

        $response->assertSee('カートに商品が入っていません。');
    }

    public function test_can_add_product_to_cart()
    {
        $response = $this->post(
            route('cart.add', $this->product),
            [
                'quantity' => 2,
            ]
        );

        $response
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas('success');

        $cart = session('cart');

        $this->assertNotEmpty($cart);

        $this->assertEquals(
            2,
            $cart[$this->product->id]['quantity']
        );
    }

    public function test_can_view_cart_with_items()
    {
        $this->post(
            route('cart.add', $this->product),
            [
                'quantity' => 1,
            ]
        );

        $response = $this->get(route('cart.index'));

        $response->assertStatus(200);

        $response->assertSee($this->product->name);

        $response->assertSee('1,000');
    }
    public function test_can_update_cart_quantity()
    {
        $this->post(
            route('cart.add', $this->product),
            [
                'quantity' => 1,
            ]
        );

        $response = $this->postJson(
            route('cart.update'),
            [
                'key' => (string) $this->product->id,
                'quantity' => 3,
            ]
        );

        $response
            ->assertOk()
            ->assertJson([
                'success' => true,
                'quantity' => 3,
            ]);

        $cart = session('cart');

        $this->assertEquals(
            3,
            $cart[$this->product->id]['quantity']
        );
    }

    public function test_can_remove_product_from_cart()
    {
        $this->post(
            route('cart.add', $this->product),
            [
                'quantity' => 1,
            ]
        );

        $response = $this->post(
            route('cart.remove'),
            [
                'key' => (string) $this->product->id,
            ]
        );

        $response
            ->assertRedirect(route('cart.index'))
            ->assertSessionHas('success');

        $this->assertEmpty(session('cart'));
    }

    public function test_cart_calculates_total_correctly()
    {
        $product2 = Product::create([
            'name' => 'Test Product 2',
            'description' => 'Test description',
            'price' => 2000,
            'stock' => 50,
            'category_id' => $this->product->category_id,
            'is_active' => true,
            'is_published' => true,
            'is_stock_managed' => true,
        ]);

        $this->post(
            route('cart.add', $this->product),
            [
                'quantity' => 2,
            ]
        );

        $this->post(
            route('cart.add', $product2),
            [
                'quantity' => 1,
            ]
        );

        $response = $this->get(route('cart.index'));

        $response->assertStatus(200);

        $response->assertSee('4,000');
    }
    public function test_adding_same_product_increases_quantity()
    {
        $this->post(
            route('cart.add', $this->product),
            [
                'quantity' => 1,
            ]
        );

        $this->post(
            route('cart.add', $this->product),
            [
                'quantity' => 2,
            ]
        );

        $cart = session('cart');

        $this->assertEquals(
            3,
            $cart[$this->product->id]['quantity']
        );
    }

    public function test_cart_validation_requires_valid_product()
    {
        $response = $this->post(
            '/cart/add/999999',
            [
                'quantity' => 1,
            ]
        );

        $response->assertStatus(404);
    }

    public function test_cart_requires_quantity()
    {
        $response = $this->post(
            route('cart.add', $this->product),
            []
        );

        $response->assertRedirect(route('cart.index'));

        $cart = session('cart');

        $this->assertEquals(
            1,
            $cart[$this->product->id]['quantity']
        );
    }
}
