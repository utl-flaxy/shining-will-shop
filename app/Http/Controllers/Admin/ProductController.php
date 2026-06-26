<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;   // メンバー在庫用
use App\Models\ProductImage;     // 複数画像
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q = Product::query()->withCount('variants');

        if ($kw = $request->string('q')->toString()) {
            $q->where('name', 'like', "%{$kw}%");
        }

        $products = $q->latest()->paginate(20)->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function store(Request $request)
    {
        $data = $this->validateProduct($request);

        DB::transaction(function () use ($request, $data) {
            /** @var Product $product */
            $product = Product::create([
                'name'               => $data['name'],
                'description'        => $data['description'] ?? null,
                'price'              => $data['price'],
                // マイグレーションに合わせて is_stock_managed を使う
                'is_stock_managed'   => $data['is_stock_managed'] ?? true,
                'start_at'           => $this->parseDate($data['start_at'] ?? null),
                'end_at'             => $this->parseDate($data['end_at'] ?? null),
                'is_published'       => (bool)($data['is_published'] ?? true),
            ]);

            // 画像（複数） — DB のカラム名は url / sort_order
            if ($request->hasFile('images')) {
                foreach ((array) $request->file('images') as $i => $file) {
                    $path = $file->store('products', 'public'); // storage/app/public/products
                    ProductImage::create([
                        'product_id' => $product->id,
                        'url'        => $path,
                        'sort_order' => $i + 1,
                    ]);
                }
            }

            // メンバー在庫（variants）
            if (!empty($data['variants']) && is_array($data['variants'])) {
                foreach ($data['variants'] as $v) {
                    if (!trim($v['name'] ?? '')) continue;
                    ProductVariant::create([
                        'product_id' => $product->id,
                        'name'       => $v['name'],
                        'sku'        => $v['sku'] ?? null,
                        'stock'      => (int)($v['stock'] ?? 0),
                    ]);
                }
            }
        });

        return redirect()->route('admin.products.index')->with('success', '商品を登録しました');
    }

    public function edit(Product $product)
    {
        $product->load(['images' => fn($q) => $q->orderBy('sort_order'), 'variants' ]);
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validateProduct($request, isUpdate: true);

        DB::transaction(function () use ($request, $product, $data) {
            $product->update([
                'name'               => $data['name'],
                'description'        => $data['description'] ?? null,
                'price'              => $data['price'],
                'is_stock_managed'   => $data['is_stock_managed'] ?? true,
                'start_at'           => $this->parseDate($data['start_at'] ?? null),
                'end_at'             => $this->parseDate($data['end_at'] ?? null),
                'is_published'       => (bool)($data['is_published'] ?? true),
            ]);

            // 画像 追加（url / sort_order）
            if ($request->hasFile('images')) {
                $maxSort = (int) $product->images()->max('sort_order');
                foreach ((array) $request->file('images') as $i => $file) {
                    $path = $file->store('products', 'public');
                    $product->images()->create([
                        'url'        => $path,
                        'sort_order' => $maxSort + $i + 1,
                    ]);
                }
            }

            // 画像 削除（url を使用して削除）
            $deleteImageIds = array_filter((array) $request->input('delete_image_ids', []));
            if ($deleteImageIds) {
                $images = $product->images()->whereIn('id', $deleteImageIds)->get();
                foreach ($images as $img) {
                    Storage::disk('public')->delete($img->url);
                    $img->delete();
                }
            }

            // バリアント同期（既存更新/新規/削除）
            $payload = collect((array)($data['variants'] ?? []))
                ->filter(fn($v) => trim($v['name'] ?? '') !== '');

            $keepIds = [];
            foreach ($payload as $v) {
                if (!empty($v['id'])) {
                    $variant = $product->variants()->whereKey($v['id'])->first();
                    if ($variant) {
                        $variant->update([
                            'name'  => $v['name'],
                            'sku'   => $v['sku'] ?? null,
                            'stock' => (int)($v['stock'] ?? 0),
                        ]);
                        $keepIds[] = $variant->id;
                    }
                } else {
                    $nv = $product->variants()->create([
                        'name'  => $v['name'],
                        'sku'   => $v['sku'] ?? null,
                        'stock' => (int)($v['stock'] ?? 0),
                    ]);
                    $keepIds[] = $nv->id;
                }
            }

            $product->variants()->whereNotIn('id', $keepIds ?: [0])->delete();
        });

        return back()->with('success', '商品を更新しました');
    }

    public function destroy(Product $product)
    {
        DB::transaction(function () use ($product) {
            foreach ($product->images as $img) {
                Storage::disk('public')->delete($img->url);
            }
            $product->images()->delete();
            $product->variants()->delete();
            $product->delete();
        });

        return redirect()->route('admin.products.index')->with('success', '商品を削除しました');
    }

    public function preview(Product $product)
    {
        $product->load(['images' => fn($q) => $q->orderBy('sort_order'), 'variants']);
        return view('products.show', compact('product'));
    }

    private function validateProduct(Request $request, bool $isUpdate = false): array
    {
        return $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'integer', 'min:0'],
            // マイグレーションに合わせて is_stock_managed を使う
            'is_stock_managed' => ['nullable', 'boolean'],
            'start_at' => ['nullable', 'date'],
            'end_at'   => ['nullable', 'date', 'after_or_equal:start_at'],
            'is_published' => ['nullable', 'boolean'],

            // 画像（複数）
            'images.*' => [$isUpdate ? 'nullable' : 'sometimes', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],

            'variants'            => ['nullable', 'array'],
            'variants.*.id'       => ['nullable', 'integer'],
            'variants.*.name'     => ['nullable', 'string', 'max:120'],
            'variants.*.sku'      => ['nullable', 'string', 'max:120'],
            'variants.*.stock'    => ['nullable', 'integer', 'min:0'],

            'delete_image_ids'    => ['nullable', 'array'],
            'delete_image_ids.*'  => ['integer'],
        ]);
    }

    private function parseDate(?string $val): ?Carbon
    {
        return $val ? Carbon::parse($val) : null;
    }
}
