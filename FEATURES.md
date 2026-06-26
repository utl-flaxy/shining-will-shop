# 実装済み機能ドキュメント

このドキュメントは、shining-will-shopプロジェクトに実装された主要なエンドユーザー向け機能について説明します。

## 1. 商品検索機能

### 概要
ユーザーがキーワード、カテゴリ、価格帯で商品を検索できる機能です。

### 機能詳細

#### キーワード検索
- 商品名または商品説明でキーワード検索が可能
- 部分一致検索に対応
- 検索クエリパラメータ: `?search=キーワード`

#### カテゴリフィルタ
- ドロップダウンメニューから特定のカテゴリを選択して絞り込み
- 全カテゴリを表示する「すべてのカテゴリ」オプション付き
- 検索クエリパラメータ: `?category=カテゴリID`

#### 価格帯フィルタ
- 最低価格と最高価格を指定して絞り込み
- どちらか一方のみの指定も可能
- 検索クエリパラメータ: `?min_price=最低価格&max_price=最高価格`

#### ページネーション
- 1ページあたり12商品を表示
- フィルタ条件を保持したままページ遷移可能
- LaravelデフォルトのページネーションUIを使用

### 使用例

```
# キーワード検索
/products?search=ラップトップ

# カテゴリフィルタ
/products?category=1

# 価格帯フィルタ
/products?min_price=1000&max_price=5000

# 複合検索
/products?search=マウス&category=1&max_price=3000
```

### 実装ファイル
- Controller: `app/Http/Controllers/Front/ProductController.php`
- Model: `app/Models/Product.php` (scopeメソッド: search, byCategory, priceRange, active)
- View: `resources/views/front/products/index.blade.php`
- Migration: `database/migrations/2025_11_03_000000_add_search_fields_to_products_table.php`
- Tests: `tests/Feature/ProductSearchTest.php`

---

## 2. カート機能

### 概要
商品をカートに追加し、数量変更や削除ができる機能です。カートの状態はセッションに保存されます。

### 機能詳細

#### カートへの追加
- 商品詳細ページから商品をカートに追加
- 数量を指定して追加可能
- 同じ商品を再度追加すると数量が加算される
- ルート: `POST /cart/add`

#### カートの表示
- カート内の全商品を一覧表示
- 各商品の画像、名前、カテゴリ、価格、数量、小計を表示
- 注文合計（小計 + 送料）を計算して表示
- ルート: `GET /cart`

#### 数量の更新
- カート内の各商品の数量を個別に変更可能
- 「数量を更新」ボタンで一括更新
- ルート: `POST /cart/update`

#### 商品の削除
- カート内の商品を個別に削除可能
- 削除ボタンで即座に削除
- ルート: `POST /cart/remove`

#### レビュー画面
- カート画面が注文前のレビュー画面として機能
- 注文内容（商品、数量、価格）を確認
- 合計金額を表示
- 「購入手続きへ進む」ボタンで決済フローへ遷移

### セッション構造

```php
session('cart') = [
    商品ID => [
        'qty' => 数量
    ],
    // ...
]
```

### 実装ファイル
- Controller: `app/Http/Controllers/Front/CartController.php`
- View: `resources/views/front/cart/index.blade.php`
- Tests: `tests/Feature/CartTest.php`

---

## 3. 決済フロー

### 概要
Stripeを使用した外部決済プロバイダーによる決済機能です。カートの内容を基に請求情報を生成し、ユーザーに確認ページを表示します。

### 機能詳細

#### 決済の開始
- カート画面から「購入手続きへ進む」ボタンをクリック
- 認証が必要（ログインしていない場合はログインページへリダイレクト）
- カート内容を基にOrderとOrderItemを作成
- Stripe Checkout Sessionを作成
- Stripe決済ページへリダイレクト
- ルート: `POST /checkout/start` (認証必須)

#### Stripe連携
- Stripe Checkout APIを使用
- 決済方法: カード決済
- 通貨: JPY（日本円）
- 決済モード: payment（一括払い）

#### 注文の作成
決済開始時に以下の情報で注文を作成:
- ユーザーID
- ユーザー名
- メールアドレス
- 送料
- 合計金額
- ステータス: 'pending'
- Stripe Session ID

#### 注文明細の作成
各カート商品について注文明細を作成:
- 商品ID
- 商品名
- 価格
- 数量

#### 決済成功ページ
- Stripe決済完了後にリダイレクト
- 注文番号を表示
- 注文内容（商品、数量、金額）を表示
- 合計金額を表示
- カートをクリア
- ルート: `GET /checkout/success?session_id={CHECKOUT_SESSION_ID}` (認証必須)

#### 決済キャンセルページ
- Stripe決済をキャンセルした場合にリダイレクト
- キャンセルメッセージを表示
- カートに戻るリンク
- 買い物を続けるリンク
- ルート: `GET /checkout/cancel`

### Webhook対応
Stripeからのwebhookを受信して注文ステータスを更新:
- エンドポイント: `POST /stripe/webhook`
- 開発環境用エンドポイント: `POST /stripe/webhook-unthrottled` (ENABLE_LOCAL_WEBHOOK_UNTHROTTLED=trueの場合)

### 実装ファイル
- Controller: `app/Http/Controllers/Front/CheckoutController.php`
- Webhook Controller: `app/Http/Controllers/Webhook/StripeWebhookController.php`
- Views:
  - `resources/views/front/checkout/success.blade.php`
  - `resources/views/front/checkout/cancel.blade.php`
- Models:
  - `app/Models/Order.php`
  - `app/Models/OrderItem.php`
- Tests: `tests/Feature/CheckoutTest.php`

---

## セキュリティ考慮事項

### 認証
- 決済フローは認証必須（checkout.start と checkout.success）
- カート機能は認証不要（ゲストユーザーも利用可能）

### バリデーション
- カート追加時に商品IDと数量をバリデーション
- 商品の存在確認
- 数量の最小値チェック

### アクティブ商品のみ表示
- 商品一覧は `is_active = true` の商品のみ表示
- 非公開商品へのアクセスは404エラー

---

## テスト

すべての主要機能に対して自動テストを実装:

1. **ProductSearchTest**: 商品検索機能のテスト
   - キーワード検索
   - カテゴリフィルタ
   - 価格帯フィルタ
   - 複合検索
   - ページネーション

2. **CartTest**: カート機能のテスト
   - カート追加
   - 数量更新
   - 商品削除
   - 合計金額計算
   - バリデーション

3. **CheckoutTest**: 決済フローのテスト
   - 空カートでの決済試行
   - 認証チェック
   - 成功/キャンセルページ表示

テスト実行:
```bash
php artisan test
```

---

## 設定

### 環境変数

Stripe設定（`.env`ファイル）:
```
STRIPE_SECRET=sk_test_...
STRIPE_PUBLISHABLE_KEY=pk_test_...
```

開発環境でのwebhook設定:
```
ENABLE_LOCAL_WEBHOOK_UNTHROTTLED=true
```

### データベースマイグレーション

```bash
php artisan migrate
```

必要なテーブル:
- products
- categories
- orders
- order_items
- users

---

## 今後の改善案

1. **商品検索の拡張**
   - ソート機能（価格順、人気順など）
   - タグによる検索
   - 在庫有無フィルタ

2. **カート機能の拡張**
   - お気に入り機能
   - カート保存期間の設定
   - クーポン対応

3. **決済フローの拡張**
   - 配送先住所の管理
   - 複数配送先対応
   - ゲスト決済対応
   - 決済方法の追加（コンビニ払い、銀行振込など）

4. **その他**
   - 商品レビュー機能
   - 注文履歴表示
   - メール通知
   - 在庫管理の強化
