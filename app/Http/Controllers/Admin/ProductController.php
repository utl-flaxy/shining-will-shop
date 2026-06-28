<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | 商品一覧
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $query = Product::query()
            ->withCount('variants');

        if ($keyword = $request->string('q')->toString()) {

            $query->where(function ($q) use ($keyword) {

                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");

            });
        }

        $products = $query
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view(
            'admin.products.index',
            compact('products')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 作成画面
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('admin.products.create');
    }

    /*
    |--------------------------------------------------------------------------
    | 商品登録
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $data = $this->validateProduct($request);

        DB::transaction(function () use ($request, $data) {

            $product = Product::create([

                'name' => $data['name'],

                'description' => $data['description'] ?? null,

                'price' => $data['price'],

                'is_stock_managed' =>
                    $data['is_stock_managed'] ?? true,

                'start_at' =>
                    $this->parseDate(
                        $data['start_at'] ?? null
                    ),

                'end_at' =>
                    $this->parseDate(
                        $data['end_at'] ?? null
                    ),

                'is_published' =>
                    (bool) ($data['is_published'] ?? true),

            ]);

            /*
            |--------------------------------------------------------------------------
            | 商品画像
            |--------------------------------------------------------------------------
            */

            if ($request->hasFile('images')) {

                foreach (
                    (array) $request->file('images')
                    as $index => $file
                ) {

                    $path = Storage::disk(
                        config('filesystems.default')
                    )->putFile(
                        'products',
                        $file
                    );

                    ProductImage::create([

                        'product_id' => $product->id,

                        'url' => $path,

                        'sort_order' => $index + 1,

                    ]);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | メンバー別在庫
            |--------------------------------------------------------------------------
            */

            if (
                ! empty($data['variants']) &&
                is_array($data['variants'])
            ) {
                foreach ($data['variants'] as $variant) {

                    if (! trim($variant['name'] ?? '')) {
                        continue;
                    }

                    ProductVariant::create([

                        'product_id' => $product->id,

                        'name' => $variant['name'],

                        'sku' => $variant['sku'] ?? null,

                        'stock' => (int) ($variant['stock'] ?? 0),

                    ]);
                }
            }

        });

        return redirect()
            ->route('admin.products.index')
            ->with('success', '商品を登録しました');
    }

    /*
    |--------------------------------------------------------------------------
    | 編集画面
    |--------------------------------------------------------------------------
    */

    public function edit(Product $product)
    {
        $product->load([
            'images' => fn ($query) => $query->orderBy('sort_order'),
            'variants',
        ]);

        return view(
            'admin.products.edit',
            compact('product')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 商品更新
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        Product $product
    ) {

        $data = $this->validateProduct(
            $request,
            isUpdate: true
        );

        DB::transaction(function () use (
            $request,
            $product,
            $data
        ) {

            $product->update([

                'name' => $data['name'],

                'description' =>
                    $data['description'] ?? null,

                'price' => $data['price'],

                'is_stock_managed' =>
                    $data['is_stock_managed'] ?? true,

                'start_at' =>
                    $this->parseDate(
                        $data['start_at'] ?? null
                    ),

                'end_at' =>
                    $this->parseDate(
                        $data['end_at'] ?? null
                    ),

                'is_published' =>
                    (bool) ($data['is_published'] ?? true),

            ]);

            /*
            |--------------------------------------------------------------------------
            | 画像追加（S3対応）
            |--------------------------------------------------------------------------
            */

            if ($request->hasFile('images')) {

                $maxSort = (int) $product
                    ->images()
                    ->max('sort_order');

                foreach (
                    (array) $request->file('images')
                    as $index => $file
                ) {

                    $path = Storage::disk(
                        config('filesystems.default')
                    )->putFile(
                        'products',
                        $file
                    );

                    $product->images()->create([

                        'url' => $path,

                        'sort_order' => $maxSort + $index + 1,

                    ]);
                }
            }

            /*
            |--------------------------------------------------------------------------
            | 画像削除
            |--------------------------------------------------------------------------
            */

            $deleteImageIds = array_filter(
                (array) $request->input(
                    'delete_image_ids',
                    []
                )
            );

            if ($deleteImageIds) {
                $images = $product->images()
                    ->whereIn('id', $deleteImageIds)
                    ->get();

                foreach ($images as $image) {

                    Storage::disk(
                        config('filesystems.default')
                    )->delete($image->url);

                    $image->delete();
                }
            }

            /*
            |--------------------------------------------------------------------------
            | バリアント同期
            |--------------------------------------------------------------------------
            */

            $payload = collect(
                (array) ($data['variants'] ?? [])
            )->filter(function ($variant) {

                return trim($variant['name'] ?? '') !== '';

            });

            $keepIds = [];

            foreach ($payload as $variant) {

                if (! empty($variant['id'])) {

                    $currentVariant = $product->variants()
                        ->whereKey($variant['id'])
                        ->first();

                    if ($currentVariant) {

                        $currentVariant->update([

                            'name'  => $variant['name'],

                            'sku'   => $variant['sku'] ?? null,

                            'stock' => (int) ($variant['stock'] ?? 0),

                        ]);

                        $keepIds[] = $currentVariant->id;
                    }

                } else {

                    $newVariant = $product->variants()->create([

                        'name'  => $variant['name'],

                        'sku'   => $variant['sku'] ?? null,

                        'stock' => (int) ($variant['stock'] ?? 0),

                    ]);

                    $keepIds[] = $newVariant->id;
                }
            }

            $product->variants()
                ->whereNotIn('id', $keepIds ?: [0])
                ->delete();

        });

        return back()->with(
            'success',
            '商品を更新しました'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | 商品削除
    |--------------------------------------------------------------------------
    */

    public function destroy(Product $product)
    {
        DB::transaction(function () use ($product) {

            foreach ($product->images as $image) {

                Storage::disk(
                    config('filesystems.default')
                )->delete($image->url);

                $image->delete();
            }

            $product->variants()->delete();

            $product->delete();

        });

        return redirect()
            ->route('admin.products.index')
            ->with('success', '商品を削除しました');
    }

    /*
    |--------------------------------------------------------------------------
    | プレビュー
    |--------------------------------------------------------------------------
    */

    public function preview(Product $product)
    {
        $product->load([
            'images' => fn ($query) => $query->orderBy('sort_order'),
            'variants',
        ]);

        return view(
            'products.show',
            compact('product')
        );
    }
    /*
    |--------------------------------------------------------------------------
    | バリデーション
    |--------------------------------------------------------------------------
    */

    private function validateProduct(
        Request $request,
        bool $isUpdate = false
    ): array
    {
        return $request->validate([

            /*
            |--------------------------------------------------------------------------
            | 基本情報
            |--------------------------------------------------------------------------
            */

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'price' => [
                'required',
                'integer',
                'min:0',
            ],

            'is_stock_managed' => [
                'nullable',
                'boolean',
            ],

            'start_at' => [
                'nullable',
                'date',
            ],

            'end_at' => [
                'nullable',
                'date',
                'after_or_equal:start_at',
            ],

            'is_published' => [
                'nullable',
                'boolean',
            ],

            /*
            |--------------------------------------------------------------------------
            | 商品画像
            |--------------------------------------------------------------------------
            */

            'images.*' => [

                $isUpdate
                    ? 'nullable'
                    : 'sometimes',

                'image',

                'mimes:jpg,jpeg,png,webp',

                'max:8192',

            ],

            /*
            |--------------------------------------------------------------------------
            | メンバー別在庫
            |--------------------------------------------------------------------------
            */

            'variants' => [
                'nullable',
                'array',
            ],

            'variants.*.id' => [
                'nullable',
                'integer',
            ],

            'variants.*.name' => [
                'nullable',
                'string',
                'max:120',
            ],

            'variants.*.sku' => [
                'nullable',
                'string',
                'max:120',
            ],

            'variants.*.stock' => [
                'nullable',
                'integer',
                'min:0',
            ],

            /*
            |--------------------------------------------------------------------------
            | 削除画像
            |--------------------------------------------------------------------------
            */

            'delete_image_ids' => [
                'nullable',
                'array',
            ],

            'delete_image_ids.*' => [
                'integer',
            ],

        ]);
    }
    /*
    |--------------------------------------------------------------------------
    | 日付変換
    |--------------------------------------------------------------------------
    */

    private function parseDate(?string $value): ?Carbon
    {
        return $value
            ? Carbon::parse($value)
            : null;
    }
}
