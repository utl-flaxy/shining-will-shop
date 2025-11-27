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
    /**
     * 商品一覧
     * - 最新順
     * - 簡易検索（商品名）
     */
    public function index(Request $request)
    {
        $q = Product::query()->withCount('variants');

        if ($kw = $request->string('q')->toString()) {
            $q->where('name', 'like', "%{$kw}%");
        }

        $products = $q->latest()->paginate(20)->withQueryString();

        return view('admin.products.index', compact('products'));
    }

    /**
     * 新規作成フォーム
     */
    public function create()
    {
        return view('admin.products.create');
    }

    /**
     * 保存
     * - 複数画像の保存
     * - メンバー別在庫（variants）を配列で受け取り保存
     * - 販売期間 start_at / end_at
     * - is_stock_managed で在庫管理ON/OFF
     */
    public function store(Request $request)
    {
        $data = $this->validateProduct($request);

        DB::transaction(function () use ($request, $data) {
            // 商品作成
            /** @var Product $product */
            $product = Product::create([
                'name'             => $data['name'],
                'description'      => $data['description'] ?? null,
                'price'            => $data['price'],
                'is_stock_managed' => $data['is_stock_managed'] ?? false,
                'start_at'         => $this->parseDate($data['start_at'] ?? null),
                'end_at'           => $this->parseDate($data['end_at'] ?? null),
                'is_published'     => (bool)($data['is_published'] ?? true),
            ]);

            // 画像（複数）
            if ($request->hasFile('images')) {
                foreach ((array) $request->file('images') as $i => $file) {
                    $path = $file->store('products', 'public'); // storage/app/public/products
                    ProductImage::create([
                        'product_id' => $product->id,
                        'path'       => $path,
                        'sort'       => $i + 1,
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

    /**
     * 編集フォーム
     */
    public function edit(Product $product)
    {
        $product->load(['images' => fn($q) => $q->orderBy('sort'), 'variants' ]);
        return view('admin.products.edit', compact('product'));
    }

    /**
     * 更新
     * - 画像追加／削除
     * - バリアント（既存更新・新規作成・削除同期）
     */
    public function update(Request $request, Product $product)
    {
        $data = $this->validateProduct($request, isUpdate: true);

        DB::transaction(function () use ($request, $product, $data) {
            $product->update([
                'name'             => $data['name'],
                'description'      => $data['description'] ?? null,
                'price'            => $data['price'],
                'is_stock_managed' => $data['is_stock_managed'] ?? false,
                'start_at'         => $this->parseDate($data['start_at'] ?? null),
                'end_at'           => $this->parseDate($data['end_at'] ?? null),
                'is_published'     => (bool)($data['is_published'] ?? true),
            ]);

            // 画像 追加
            if ($request->hasFile('images')) {
                $maxSort = (int) $product->images()->max('sort');
                foreach ((array) $request->file('images') as $i => $file) {
                    $path = $file->store('products', 'public');
                    $product->images()->create([
                        'path' => $path,
                        'sort' => $maxSort + $i + 1,
                    ]);
                }
            }

            // 画像 削除
            $deleteImageIds = array_filter((array) $request->input('delete_image_ids', []));
            if ($deleteImageIds) {
                $images = $product->images()->whereIn('id', $deleteImageIds)->get();
                foreach ($images as $img) {
                    Storage::disk('public')->delete($img->path);
                    $img->delete();
                }
            }

            // バリアント同期（idが来たら更新／無ければ作成／送られなかった既存は削除）
            $payload = collect((array)($data['variants'] ?? []))
                ->filter(fn($v) => trim($v['name'] ?? '') !== '');

            $keepIds = [];
            foreach ($payload as $v) {
                if (!empty($v['id'])) {
                    /** @var ProductVariant $variant */
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

            // 送られてこなかった既存バリアントは削除
            $product->variants()->whereNotIn('id', $keepIds ?: [0])->delete();
        });

        return back()->with('success', '商品を更新しました');
    }

    /**
     * 削除
     */
    public function destroy(Product $product)
    {
        DB::transaction(function () use ($product) {
            foreach ($product->images as $img) {
                Storage::disk('public')->delete($img->path);
            }
            $product->images()->delete();
            $product->variants()->delete();
            $product->delete();
        });

        return redirect()->route('admin.products.index')->with('success', '商品を削除しました');
    }

    /**
     * プレビュー（ドラフト表示用・任意）
     */
    public function preview(Product $product)
    {
        $product->load(['images' => fn($q) => $q->orderBy('sort'), 'variants']);
        return view('products.show', compact('product')); // ユーザー側テンプレートで表示
    }

    private function validateProduct(Request $request, bool $isUpdate = false): array
    {
        return $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'integer', 'min:0'],
            'is_stock_managed' => ['nullable', 'boolean'],
            'start_at' => ['nullable', 'date'],
            'end_at'   => ['nullable', 'date', 'after_or_equal:start_at'],
            'is_published' => ['nullable', 'boolean'],

            // 画像（複数）
            'images.*' => [$isUpdate ? 'nullable' : 'sometimes', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],

            // バリアント（メンバー在庫）
            'variants'            => ['nullable', 'array'],
            'variants.*.id'       => ['nullable', 'integer'],
            'variants.*.name'     => ['nullable', 'string', 'max:120'],
            'variants.*.sku'      => ['nullable', 'string', 'max:120'],
            'variants.*.stock'    => ['nullable', 'integer', 'min:0'],

            // 画像削除（更新時）
            'delete_image_ids'    => ['nullable', 'array'],
            'delete_image_ids.*'  => ['integer'],
        ]);
    }

    private function parseDate(?string $val): ?Carbon
    {
        return $val ? Carbon::parse($val) : null;
    }
}
