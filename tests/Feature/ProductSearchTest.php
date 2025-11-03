<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test categories
        $this->category1 = Category::create(['name' => 'Electronics']);
        $this->category2 = Category::create(['name' => 'Books']);
        
        // Create test products
        Product::create([
            'name' => 'Laptop Computer',
            'description' => 'High-performance laptop for developers',
            'price' => 150000,
            'stock' => 10,
            'category_id' => $this->category1->id,
            'is_active' => true,
        ]);
        
        Product::create([
            'name' => 'Programming Book',
            'description' => 'Learn Laravel framework',
            'price' => 3000,
            'stock' => 50,
            'category_id' => $this->category2->id,
            'is_active' => true,
        ]);
        
        Product::create([
            'name' => 'Wireless Mouse',
            'description' => 'Ergonomic wireless mouse',
            'price' => 2500,
            'stock' => 30,
            'category_id' => $this->category1->id,
            'is_active' => true,
        ]);
        
        // Inactive product (should not appear in search)
        Product::create([
            'name' => 'Old Product',
            'description' => 'Discontinued item',
            'price' => 1000,
            'stock' => 0,
            'category_id' => $this->category1->id,
            'is_active' => false,
        ]);
    }

    public function test_can_view_products_index()
    {
        $response = $this->get(route('store.index'));
        
        $response->assertStatus(200);
        $response->assertSee('Laptop Computer');
        $response->assertSee('Programming Book');
        $response->assertSee('Wireless Mouse');
        $response->assertDontSee('Old Product'); // Inactive product should not appear
    }

    public function test_can_search_products_by_keyword()
    {
        $response = $this->get(route('store.index', ['search' => 'laptop']));
        
        $response->assertStatus(200);
        $response->assertSee('Laptop Computer');
        $response->assertDontSee('Programming Book');
        $response->assertDontSee('Wireless Mouse');
    }

    public function test_search_works_with_description()
    {
        $response = $this->get(route('store.index', ['search' => 'Laravel']));
        
        $response->assertStatus(200);
        $response->assertSee('Programming Book');
        $response->assertDontSee('Laptop Computer');
    }

    public function test_can_filter_by_category()
    {
        $response = $this->get(route('store.index', ['category' => $this->category1->id]));
        
        $response->assertStatus(200);
        $response->assertSee('Laptop Computer');
        $response->assertSee('Wireless Mouse');
        $response->assertDontSee('Programming Book');
    }

    public function test_can_filter_by_minimum_price()
    {
        $response = $this->get(route('store.index', ['min_price' => 10000]));
        
        $response->assertStatus(200);
        $response->assertSee('Laptop Computer');
        $response->assertDontSee('Programming Book');
        $response->assertDontSee('Wireless Mouse');
    }

    public function test_can_filter_by_maximum_price()
    {
        $response = $this->get(route('store.index', ['max_price' => 5000]));
        
        $response->assertStatus(200);
        $response->assertSee('Programming Book');
        $response->assertSee('Wireless Mouse');
        $response->assertDontSee('Laptop Computer');
    }

    public function test_can_filter_by_price_range()
    {
        $response = $this->get(route('store.index', [
            'min_price' => 2000,
            'max_price' => 5000
        ]));
        
        $response->assertStatus(200);
        $response->assertSee('Programming Book');
        $response->assertSee('Wireless Mouse');
        $response->assertDontSee('Laptop Computer');
    }

    public function test_can_combine_search_and_filters()
    {
        $response = $this->get(route('store.index', [
            'search' => 'wireless',
            'category' => $this->category1->id,
            'max_price' => 5000
        ]));
        
        $response->assertStatus(200);
        $response->assertSee('Wireless Mouse');
        $response->assertDontSee('Laptop Computer');
        $response->assertDontSee('Programming Book');
    }

    public function test_returns_empty_when_no_matches()
    {
        $response = $this->get(route('store.index', ['search' => 'nonexistent']));
        
        $response->assertStatus(200);
        $response->assertSee('該当する商品が見つかりませんでした');
    }

    public function test_pagination_works_with_filters()
    {
        // Create more products to test pagination
        for ($i = 1; $i <= 15; $i++) {
            Product::create([
                'name' => "Test Product $i",
                'description' => 'Test description',
                'price' => 1000 * $i,
                'stock' => 10,
                'category_id' => $this->category1->id,
                'is_active' => true,
            ]);
        }
        
        $response = $this->get(route('store.index', ['category' => $this->category1->id]));
        
        $response->assertStatus(200);
        // Should see pagination links
        $response->assertSee('page=2');
    }
}
