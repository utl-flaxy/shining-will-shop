<!-- ====================================================== -->
<!-- Hero -->
<!-- ====================================================== -->

# 🛍️ Shining Will Shop

> **Laravel 11 × AWS × Square API を用いて開発した実践的なECサイト**

商品販売だけではなく、

- 商品管理
- 注文管理
- Amazon S3による画像管理
- Square決済
- 検索・絞り込み
- ページネーション
- トランザクションによる安全な在庫更新

など、実務を意識した構成・設計で開発しました。

---

![Laravel](https://img.shields.io/badge/Laravel-11-red?logo=laravel)

![PHP](https://img.shields.io/badge/PHP-8.3-blue?logo=php)

![AWS](https://img.shields.io/badge/AWS-EC2%20%7C%20S3-orange?logo=amazonaws)

![MariaDB](https://img.shields.io/badge/Database-MariaDB-blue)

![Nginx](https://img.shields.io/badge/WebServer-Nginx-green?logo=nginx)

![Square](https://img.shields.io/badge/Payment-Square-black)

![License](https://img.shields.io/badge/Portfolio-Personal-success)

---

# 📑 目次

- プロジェクト概要
- 開発背景
- このプロジェクトの特徴
- 技術スタック
- AWS構成
- システム構成
- 機能一覧
- データベース設計
- 注文処理
- Amazon S3画像管理
- 検索機能
- ローカル構築
- 今後改善したい点
- 学んだこと

---

# 🌐 デモ

## アプリケーション

https://（デプロイURL）

---

## 管理画面

https://（管理画面URL）

---

## GitHub

https://github.com/utl-flaxy/shining-will-shop

---

# 📸 スクリーンショット

## トップページ

> （docs/images/top.png）

---

## 商品一覧

> （docs/images/products.png）

---

## 商品詳細

> （docs/images/detail.png）

---

## 管理画面

> （docs/images/admin-dashboard.png）

---

## 注文一覧

> （docs/images/orders.png）

---

# 💡 プロジェクト概要

Shining Will Shop は、

アイドル・アーティスト向けの公式オンラインショップを想定して開発したECサイトです。

一般的な商品販売だけではなく、

- 商品管理
- 注文管理
- カテゴリー管理
- Amazon S3画像管理
- Square決済
- 在庫管理

まで実装しています。

「画面が動くこと」を目的とするのではなく、

**保守性・拡張性・データ整合性**

を意識した設計を目標として開発しました。

---

# 🎯 開発背景

前職では業務システムを利用する立場でしたが、

「サービスを利用する側」ではなく、

**自分自身で設計・開発・運用できるエンジニア**

を目指し、本プロジェクトを制作しました。

Laravelの基本機能を利用するだけではなく、

- AWS
- 外部API
- Database設計
- Storage
- Transaction

まで実装し、

実務で利用されるWebサービスに近い構成を意識しています。

---

# ⭐ このプロジェクトの特徴

## ① Amazon S3による画像管理

商品画像・カテゴリ画像をAmazon S3へ保存しています。

Laravel Storageを利用することで、

ローカル環境とAWS環境をコード変更なしで切り替えられる構成にしました。

---

## ② トランザクションによる安全な注文処理

注文作成時には

- 注文作成
- 注文明細作成
- 在庫更新

を1つのTransactionで管理しています。

途中で例外が発生した場合はロールバックされるため、

データ整合性を維持できます。

---

## ③ Laravelらしい設計

検索処理はControllerへ直接記述せず、

ModelのQuery Scopeへ分離しました。

```php
Product::query()
    ->published()
    ->keyword($keyword)
    ->category($category)
    ->sort($sort)
    ->paginate(12);
```

Controllerをシンプルに保ち、

再利用しやすい構成にしています。

---

## ④ 保守性を意識した画像管理

画像URLを直接生成せず、

Storage Facadeを利用しています。

```php
Storage::disk('s3')->url($image->url);
```

保存先を変更しても、

View側の修正を最小限に抑えられる設計です。

---

## ⑤ 実務を意識したECサイト構成

本プロジェクトでは、

以下のような実務で利用される機能を実装しています。

- 商品検索
- カテゴリー検索
- ページネーション
- 注文ステータス管理
- Amazon S3
- Square決済
- Transaction
- 管理画面
- 商品画像複数登録
- 商品バリエーション管理

---

# 🎯 このポートフォリオで特にアピールしたいポイント

- Laravel標準機能を活用した設計
- AWS（EC2・S3）を利用した運用
- データ整合性を考慮したTransaction
- Storage Facadeによるストレージ抽象化
- Eloquent Scopeによる責務分離
- 実務を意識した保守性・拡張性の高い設計

# 🏗️ システム構成

本アプリケーションは、AWS上へデプロイし、Laravelを中心とした構成で運用しています。

画像はAmazon S3へ保存し、決済にはSquare Payments APIを利用しています。

---

# ☁️ AWS構成図

```text
                    Internet
                        │
                        ▼
              +------------------+
              |      Nginx       |
              |    Amazon EC2    |
              +------------------+
                        │
                        ▼
              +------------------+
              |    Laravel 11    |
              |      PHP 8.3      |
              +------------------+
                 │            │
                 │            │
        Database │            │ Storage
                 ▼            ▼

        +---------------+   +--------------------+
        |   MariaDB     |   |    Amazon S3       |
        | Products      |   | Products Images    |
        | Orders        |   | Category Images    |
        | Categories    |   +--------------------+
        +---------------+
                 │
                 │
                 ▼
        +--------------------+
        | Square Payments API|
        +--------------------+
```

---

# 🖥️ システム構成

```text
                  Browser

                      │

                      ▼

               Laravel Routing

                      │

      ┌───────────────┼────────────────┐
      ▼               ▼                ▼

 StoreController  ProductController  CheckoutController

      │               │                │

      ▼               ▼                ▼

        Product Model / Order Model

                      │

                      ▼

                MariaDB Database

                      │

          ┌───────────┴────────────┐

          ▼                        ▼

     Amazon S3                Square API
```

---

# 📦 技術スタック

## Backend

|技術|用途|
|----|----|
|PHP 8.3|バックエンド|
|Laravel 11|Webアプリケーション|
|Eloquent ORM|データ操作|
|Blade|テンプレートエンジン|

---

## Frontend

|技術|用途|
|----|----|
|Tailwind CSS|UI|
|Blade|画面表示|
|Swiper.js|商品画像スライダー|
|JavaScript|フロント処理|

---

## Database

|技術|用途|
|----|----|
|MariaDB|商品・注文・会員データ|

---

## Infrastructure

|技術|用途|
|----|----|
|Amazon EC2|アプリケーションサーバ|
|Amazon S3|商品画像・カテゴリ画像保存|
|Nginx|Webサーバ|
|Laravel Storage|ストレージ抽象化|

---

## External Service

|技術|用途|
|----|----|
|Square Payments API|オンライン決済|

---

## Development

|技術|用途|
|----|----|
|Git|バージョン管理|
|GitHub|ソースコード管理|
|Composer|PHPパッケージ管理|
|npm|フロントエンド管理|

---

# 📂 ディレクトリ構成

```text
app
├── Http
│   └── Controllers
│       ├── CheckoutController.php
│       ├── ProductController.php
│       └── StoreController.php
│
├── Models
│   ├── Category.php
│   ├── Order.php
│   ├── OrderItem.php
│   ├── Product.php
│   ├── ProductImage.php
│   └── ProductVariant.php
│
├── Services
│   └── SquarePaymentService.php
│
resources
├── views
│   ├── shop
│   ├── products
│   ├── cart
│   └── layouts
│
routes
└── web.php
```

---

# 🎯 採用した設計

本プロジェクトでは、Laravelの標準機能を活用しながら、保守性・拡張性を意識した構成を採用しています。

- MVCアーキテクチャに沿った責務分離
- Eloquent ORMによるデータ操作
- Query Scopeを利用した検索処理
- Laravel Storageによるストレージ抽象化
- Amazon S3による画像管理
- Database Transactionによる在庫整合性の維持

---

# 💡 インフラで工夫したこと

画像はAmazon S3へ保存し、Laravel Storage Facade経由で取得しています。

```php
Storage::disk('s3')->url($image->url);
```

これにより、

- ローカルストレージ
- Amazon S3

を環境変数の切り替えだけで利用できる構成としました。

```env
FILESYSTEM_DISK=public
```

↓

```env
FILESYSTEM_DISK=s3
```

ViewやControllerの修正を最小限に抑えられるため、保守性・拡張性の向上につながっています。

# 🚀 機能一覧

本プロジェクトでは、一般利用者向けのEC機能と、管理者向けの管理画面を実装しています。

---

# 👤 ユーザー機能

|機能|概要|
|----|----|
|トップページ|新着商品・カテゴリー一覧を表示|
|商品一覧|公開中の商品を一覧表示|
|商品詳細|商品画像・価格・説明・在庫状況を表示|
|商品検索|商品名・説明文によるキーワード検索|
|カテゴリー検索|カテゴリーごとの商品一覧表示|
|並び替え|新着・価格昇順・価格降順・名前順|
|ページネーション|1ページ12件表示|
|カート追加|商品をカートへ追加|
|数量変更|カート内の商品数量変更|
|商品削除|カートから削除|
|注文確認|購入内容の確認|
|Square決済|クレジットカード決済|
|注文完了|注文情報を保存|
|在庫更新|購入時に在庫を減算|
|SOLD OUT表示|在庫切れ商品の購入制御|

---

# 🛠️ 管理者機能

|機能|概要|
|----|----|
|商品管理|商品登録・編集・削除|
|カテゴリー管理|カテゴリー登録・編集|
|商品画像管理|複数画像アップロード|
|商品公開設定|公開・非公開切替|
|在庫管理|商品在庫の更新|
|商品バリエーション管理|サイズ・種類ごとの在庫管理|
|注文一覧|注文履歴確認|
|注文詳細|注文内容確認|
|注文ステータス管理|受付・発送準備・発送済み等|
|画像管理|Amazon S3へ保存|

---

# 📸 画面一覧

## 🏠 トップページ

- 新着商品表示
- カテゴリー一覧
- 商品詳細への導線

> docs/images/top.png

---

## 🛍️ 商品一覧

- キーワード検索
- カテゴリー検索
- 並び替え
- ページネーション

> docs/images/products.png

---

## 📦 商品詳細

- 商品画像スライダー
- 商品説明
- 価格表示
- 在庫状況
- SOLD OUT表示
- カート追加

> docs/images/detail.png

---

## 🛒 カート

- 数量変更
- 削除
- 合計金額表示

> docs/images/cart.png

---

## 💳 決済画面

- Square Payments API
- クレジットカード決済

> docs/images/payment.png

---

## 📋 注文一覧（管理画面）

管理者は注文情報を一覧で確認できます。

表示内容

- 注文番号
- 購入者
- 合計金額
- 注文日時
- 注文ステータス

> docs/images/orders.png

---

## 📄 注文詳細

注文ごとの詳細情報を確認できます。

表示内容

- 注文商品
- 数量
- 金額
- 注文日時
- ステータス

> docs/images/order-detail.png

---

## 📦 商品管理

管理画面では

- 商品追加
- 編集
- 削除
- 公開設定

を行えます。

> docs/images/admin-products.png

---

## 🖼️ 商品画像管理

商品画像は複数登録に対応しています。

アップロードした画像はAmazon S3へ保存されます。

> docs/images/admin-images.png

---

## 🏷️ カテゴリー管理

カテゴリーごとに

- 名前
- 並び順
- 画像
- 公開状態

を管理できます。

> docs/images/admin-categories.png

---

# ⭐ 実装した主な機能

## 商品検索

キーワード検索はLaravelのQuery Scopeを利用しています。

```php
Product::query()
    ->published()
    ->keyword($keyword)
    ->category($category)
    ->sort($sort)
    ->paginate(12);
```

---

## ページネーション

大量の商品データでも表示速度を維持するため、

Laravel標準のPaginationを利用しています。

```php
->paginate(12)
```

---

## Amazon S3画像管理

商品画像・カテゴリー画像はAmazon S3へ保存しています。

Laravel Storageを利用することで、

```php
Storage::disk('s3')->url($image->url)
```

のみで画像URLを取得できます。

---

## 在庫管理

購入時にはTransactionを利用して

- 注文作成
- 注文明細作成
- 在庫更新

を1つの処理として実行しています。

途中で失敗した場合はロールバックされるため、

在庫と注文情報の不整合を防止しています。

---

# 💡 開発で工夫したポイント

- 商品画像はStorage Facadeで抽象化
- 商品検索はQuery Scopeへ分離
- ページネーションで大量データに対応
- Amazon S3への画像保存
- Square決済との連携
- トランザクションによる安全な注文処理
- 商品バリエーション対応
- 注文ステータス管理

# 🗄️ データベース設計

本プロジェクトでは、ECサイトとして必要なデータを正規化し、保守性・拡張性を考慮したテーブル設計を行いました。

商品・注文・画像・カテゴリーをそれぞれ独立したテーブルとして管理し、将来的な機能追加にも対応しやすい構成を採用しています。

---

# 📊 ER図

```text
                     Category
                        │
                  (1 : N)
                        │
                        ▼
                    Product
         ┌────────────┼────────────┐
         │            │            │
     (1:N)        (1:N)        (1:N)
         ▼            ▼            ▼
 ProductImage  ProductVariant  OrderItem
                                      │
                                  (N:1)
                                      ▼
                                    Order
```

---

# 📋 テーブル一覧

|テーブル|役割|
|---------|----------------------------|
|categories|カテゴリー管理|
|products|商品管理|
|product_images|商品画像管理|
|product_variants|商品バリエーション管理|
|orders|注文情報|
|order_items|注文明細|

---

# 📂 categories

カテゴリー情報を管理します。

|カラム|内容|
|------|----------------|
|id|ID|
|name|カテゴリー名|
|image|カテゴリー画像|
|sort_order|表示順|
|is_active|公開状態|

### リレーション

```php
Category
    hasMany(Product::class)
```

---

# 📦 products

商品情報を管理します。

|カラム|内容|
|------|----------------|
|id|ID|
|category_id|カテゴリー|
|name|商品名|
|description|説明|
|price|価格|
|stock|在庫|
|sku|SKU|
|is_active|公開状態|
|is_published|販売状態|
|is_stock_managed|在庫管理|

### リレーション

```php
belongsTo(Category::class)

hasMany(ProductImage::class)

hasMany(ProductVariant::class)

hasMany(OrderItem::class)
```

---

# 🖼️ product_images

商品画像を管理します。

1商品に対して複数画像を登録できる構成です。

|カラム|内容|
|------|----------------|
|id|ID|
|product_id|商品ID|
|url|画像パス|

画像ファイル自体はAmazon S3へ保存しています。

データベースには

```
products/login-logo.png
```

のようなキーのみ保存しています。

実際のURL生成はLaravel Storage Facadeが担当します。

```php
Storage::disk('s3')->url($image->url);
```

---

# 📦 product_variants

サイズや種類ごとの在庫管理用テーブルです。

|カラム|内容|
|------|----------------|
|id|ID|
|product_id|商品ID|
|name|サイズ・種類|
|stock|在庫数|

商品ごとの在庫ではなく、

バリエーション単位で在庫を持てる設計としています。

---

# 🧾 orders

注文情報を管理します。

|カラム|内容|
|------|----------------|
|id|注文ID|
|customer_name|購入者|
|email|メールアドレス|
|total_price|合計金額|
|status|注文ステータス|
|square_payment_id|Square決済ID|

### 注文ステータス

- Pending
- Paid
- Preparing
- Shipped
- Completed
- Cancelled

---

# 📦 order_items

注文された商品を管理します。

|カラム|内容|
|------|----------------|
|id|ID|
|order_id|注文ID|
|product_id|商品ID|
|price|購入価格|
|quantity|数量|

価格を保持している理由は、

商品価格が後から変更されても、

購入時点の価格を保持するためです。

---

# 🔗 リレーション一覧

```php
Category
    hasMany(Product)

Product
    belongsTo(Category)

Product
    hasMany(ProductImage)

Product
    hasMany(ProductVariant)

Order
    hasMany(OrderItem)

OrderItem
    belongsTo(Order)

OrderItem
    belongsTo(Product)
```

---

# 💡 設計で工夫したポイント

## 商品画像を独立テーブル化

画像をproductsテーブルへ直接持たせず、

product_imagesテーブルを作成しました。

これにより、

- 複数画像対応
- メイン画像追加
- ギャラリー機能

などへ拡張しやすい構成になっています。

---

## バリエーション管理

在庫をproductsだけで管理せず、

product_variantsを用意しました。

これにより、

- サイズ
- 色
- 種類

ごとの在庫管理へ対応できます。

---

## 注文明細の独立

注文商品をorder_itemsへ分離することで、

1回の注文で複数商品を購入できる構成にしています。

---

## データ整合性

注文時には、

- orders
- order_items
- 在庫更新

をDatabase Transactionでまとめて実行しています。

途中でエラーが発生した場合はロールバックされるため、

データの不整合を防止できます。

---

# 🎯 この設計で意識したこと

本プロジェクトでは、

「現在必要な機能」だけではなく、

今後の機能追加も見据えた設計を意識しました。

例えば、

- 商品画像追加
- 商品バリエーション追加
- カテゴリー追加
- 注文履歴追加

などを大きなテーブル変更なしで実装できる構成としています。

# 💳 注文処理・決済フロー

ECサイトでは、

- 注文情報
- 注文明細
- 在庫更新
- 決済

の整合性が非常に重要になります。

本プロジェクトでは、Square Payments APIとLaravel Transactionを組み合わせることで、安全な注文処理を実現しています。

---

# 📋 注文フロー

```text
商品選択
      │
      ▼
カート追加
      │
      ▼
注文確認
      │
      ▼
Square決済
      │
      ▼
決済成功
      │
      ▼
Transaction開始
      │
      ├── Order作成
      │
      ├── OrderItem作成
      │
      ├── 在庫減算
      │
      ▼
Commit
      │
      ▼
注文完了
```

---

# 🔄 シーケンス図

```text
User

 │

 ▼

CheckoutController

 │

 ▼

Square API

 │

 ▼

決済成功

 │

 ▼

DB::transaction()

 │

 ├───────────────┐

 ▼               ▼

Order        OrderItems

 │               │

 └───────┬───────┘

         ▼

     Stock Update

         │

         ▼

      Commit
```

---

# 🛒 注文処理の流れ

注文時には以下の順番で処理を行っています。

① Square Payments APIで決済

↓

② 決済成功を確認

↓

③ Transaction開始

↓

④ 注文作成

↓

⑤ 注文明細作成

↓

⑥ 商品在庫更新

↓

⑦ Commit

途中で例外が発生した場合は、

すべての処理をロールバックします。

---

# 💾 Database Transaction

LaravelのDatabase Transactionを利用し、

複数テーブルへの更新を一つの処理として実行しています。

```php
DB::transaction(function () {

    // 注文作成

    // 注文明細作成

    // 在庫更新

});
```

これにより、

途中で処理が失敗した場合でも、

データベースの整合性を維持できます。

---

# 📦 在庫更新

注文確定後、

商品在庫を減算しています。

在庫管理対象の商品については、

在庫数が0になると

```
SOLD OUT
```

を表示し、

購入できないよう制御しています。

---

# 🚫 在庫不足対策

購入前に現在の在庫数を確認しています。

```php
$product->totalStock()
```

在庫不足の場合は注文処理を中止します。

---

# 🏷️ バリエーション在庫

商品バリエーションが存在する場合は、

商品の在庫ではなく、

各バリエーションの在庫数を利用します。

```php
$product->variants()->sum('stock')
```

バリエーションが存在しない場合のみ、

通常商品の在庫を利用します。

---

# 💰 Square Payments API

オンライン決済には

Square Payments API

を利用しています。

決済成功時のみ

注文情報を保存することで、

決済失敗時に注文だけ残ることを防いでいます。

---

# 🔐 データ整合性

ECサイトでは

以下3つのデータが一致している必要があります。

- 注文情報
- 注文明細
- 在庫

そのため、

Transactionを利用し、

すべて成功した場合のみ

Commitしています。

---

# 💡 この実装で意識したこと

## ① 決済成功後のみ注文作成

決済前に注文を保存すると、

決済失敗時に不要な注文データが残ります。

そのため、

Square APIの決済成功を確認した後に、

注文データを保存しています。

---

## ② Transactionによる安全な更新

注文作成・在庫更新・注文明細作成を

1つのTransactionとして実行することで、

途中でエラーが発生した場合でも

データ不整合が発生しないようにしています。

---

## ③ 在庫管理の抽象化

商品在庫だけではなく、

商品バリエーションにも対応できるよう、

Model側へ在庫計算ロジックを実装しています。

```php
$product->totalStock()
```

ViewやControllerでは

在庫の計算方法を意識せず利用できます。

---

# ⭐ 実務を意識したポイント

本プロジェクトでは、

「購入できたのに在庫が減っていない」

「注文だけ残って決済されていない」

といったECサイトで発生しやすい問題を防ぐため、

Transactionを利用して安全な注文処理を実装しました。

また、Square Payments APIとの連携により、

実際の決済フローを想定した構成となっています。

# ☁️ Amazon S3による画像管理

本プロジェクトでは、

商品画像・カテゴリー画像をAmazon S3へ保存しています。

Laravel Storage Facadeを利用することで、

ローカルストレージとAmazon S3を切り替えられる構成を採用しました。

---

# 🎯 導入目的

商品画像をサーバー内へ保存するのではなく、

Amazon S3へ保存することで、

- サーバー容量の削減
- 画像配信の効率化
- 運用性向上
- 保守性向上

を実現しています。

---

# 🏗️ 構成図

```text
管理画面

      │

      ▼

画像アップロード

      │

      ▼

Laravel Storage

      │

      ▼

Amazon S3

      │

      ▼

画像URL生成

      │

      ▼

ブラウザ表示
```

---

# 📂 保存構成

Amazon S3では以下のようなディレクトリ構成で管理しています。

```text
Bucket

├── products
│     ├── login-logo.png
│     ├── sample.jpg
│     └── default.jpg
│
└── categories
      ├── 01KW2DY62VZNTC9SZ3VYRE7J4R.jpg
      ├── 01KW2EBWCCCBW7NM8A3TEYBV0C.jpg
      └── ...
```

---

# 💾 データベース

画像そのものはAmazon S3へ保存し、

データベースにはキーのみ保持しています。

例

```text
products/login-logo.png

categories/01KW2DY62VZNTC9SZ3VYRE7J4R.jpg
```

URLを直接保存しないことで、

保存先変更にも柔軟に対応できます。

---

# 🗄️ Storage Facade

画像URLはStorage Facadeから取得しています。

```php
Storage::disk('s3')->url($image->url)
```

ViewではStorageのみを利用するため、

保存先を意識する必要がありません。

---

# 🔄 Storageの抽象化

Laravel Storageを利用しているため、

環境変数のみで保存先を切り替えられます。

ローカル

```env
FILESYSTEM_DISK=public
```

AWS

```env
FILESYSTEM_DISK=s3
```

アプリケーションコードの修正は不要です。

---

# 📸 商品画像

商品画像は

```
product_images
```

テーブルで管理しています。

1商品に対して

複数画像登録へ対応しています。

```php
Product

↓

hasMany(ProductImage)
```

---

# 🏷️ カテゴリー画像

カテゴリー画像も

Amazon S3へ保存しています。

カテゴリーテーブルには

画像パスのみ保存しています。

```text
categories/01KW2DY62VZNTC9SZ3VYRE7J4R.jpg
```

表示時は

```php
Storage::disk('s3')->url($category->image)
```

のみで取得できます。

---

# 🔐 IAM

Amazon S3専用のIAMユーザーを作成し、

Laravelからアクセスしています。

利用権限は

- GetObject
- PutObject
- DeleteObject

など、

必要最小限の権限のみ付与しています。

アクセスキーは

環境変数で管理しています。

```env
AWS_ACCESS_KEY_ID=

AWS_SECRET_ACCESS_KEY=
```

GitHubへは含めていません。

---

# ⚙️ Filesystem設定

Laravel Filesystemを利用しています。

```php
'default' => env('FILESYSTEM_DISK', 'public');
```

S3設定

```php
's3' => [

    'driver' => 's3',

    'key' => env('AWS_ACCESS_KEY_ID'),

    'secret' => env('AWS_SECRET_ACCESS_KEY'),

    'region' => env('AWS_DEFAULT_REGION'),

    'bucket' => env('AWS_BUCKET'),

],
```

---

# 🚀 Storageを採用した理由

Storage Facadeを利用することで、

以下のメリットがあります。

- Storage先を簡単に変更できる
- AWS依存コードを書かなくて済む
- テストしやすい
- 保守性が高い
- Laravel標準機能を活用できる

---

# 💡 実装で工夫したポイント

画像URLをデータベースへ保存するのではなく、

画像キーのみ保存しています。

例えば

```text
products/login-logo.png
```

だけ保持し、

URL生成はStorageへ任せています。

```php
Storage::disk('s3')->url($image->url)
```

これにより、

Amazon S3以外のストレージへ変更する場合でも、

ViewやControllerの修正を最小限に抑えられる設計としました。

---

# ⭐ 学んだこと

Amazon S3を導入したことで、

単に画像を表示するだけではなく、

- IAMユーザー作成
- Bucket Policy
- Filesystem設定
- Storage Facade
- 環境変数管理

など、

実際のクラウド環境で利用される運用方法を学ぶことができました。

また、

LaravelのStorage Facadeを利用することで、

クラウドストレージを意識せずに開発できることを学び、

保守性・拡張性の高い実装につながりました。

# 🧩 検索機能・設計

本プロジェクトでは、

「動作すること」だけではなく、

**保守性・拡張性・責務分離**

を意識した設計を採用しました。

Laravelの標準機能を積極的に活用し、

Controllerへロジックを書きすぎない構成を目指しています。

---

# 🔍 商品検索

商品一覧では以下の条件で検索できます。

- キーワード検索
- カテゴリー検索
- 並び替え
- ページネーション

複数条件を組み合わせた検索にも対応しています。

---

# キーワード検索

商品名・商品説明から検索できます。

```php
Product::query()
    ->keyword($keyword)
```

Scope内では

```php
where('name', 'like', "%{$keyword}%")
->orWhere('description', 'like', "%{$keyword}%")
```

として実装しています。

---

# カテゴリー検索

カテゴリーを選択すると、

該当カテゴリーのみ表示します。

```php
Product::query()
    ->category($category)
```

---

# 並び替え

以下の並び替えに対応しています。

- 新着順
- 価格が安い順
- 価格が高い順
- 商品名順

Modelでは

```php
match ($sort)
```

を利用して実装しています。

```php
match ($sort) {

    'price_asc'
        => $query->orderBy('price'),

    'price_desc'
        => $query->orderByDesc('price'),

    'name'
        => $query->orderBy('name'),

    default
        => $query->latest(),

};
```

Laravelらしくシンプルな実装を意識しました。

---

# ページネーション

商品数が増えても表示速度を維持できるよう、

Laravel標準のPaginationを利用しています。

```php
->paginate(12)
```

また、

検索条件を保持したままページ遷移できるよう、

```php
->withQueryString()
```

も利用しています。

---

# Query Scope

検索処理はControllerへ直接書かず、

Modelへ切り出しています。

実装しているScope

```php
published()

keyword()

category()

sort()
```

Controller側では

```php
Product::query()

    ->published()

    ->keyword($keyword)

    ->category($category)

    ->sort($sort)

    ->paginate(12);
```

のみとなり、

責務を分離しています。

---

# MVCを意識した構成

Laravel標準のMVCアーキテクチャを採用しています。

```text
Browser

↓

Route

↓

Controller

↓

Model

↓

Database

↓

Blade
```

それぞれの役割を明確に分離しています。

---

# Controller

Controllerでは、

HTTPリクエストを受け取り、

Modelへ処理を依頼し、

Viewへデータを渡す役割のみとしています。

できるだけビジネスロジックを書かないよう意識しました。

---

# Model

Modelには

- Query Scope
- リレーション
- 在庫計算

など、

商品に関するロジックを集約しています。

例

```php
$product->totalStock()

$product->isSoldOut()

$product->isAvailableForSale()
```

View側では

在庫計算方法を意識する必要がありません。

---

# View

Bladeテンプレートでは、

表示処理のみを担当しています。

画像表示も

```php
Storage::disk('s3')->url($image->url)
```

を利用し、

複雑な処理を書かないよう意識しました。

---

# Eloquent ORM

データ取得には

Laravel Eloquent ORM

を利用しています。

例

```php
Product::with([
    'images',
    'category',
])
```

Eager Loadingを利用することで、

N+1問題の発生を抑えています。

---

# 保守性を意識した設計

本プロジェクトでは、

機能追加や修正がしやすい構成を意識しました。

例えば検索条件を追加する場合でも、

Controllerを修正するのではなく、

新しいScopeを追加するだけで対応できます。

例

```php
scopePrice()

scopeStock()

scopeTag()
```

今後も柔軟に拡張できる構成となっています。

---

# 💡 実装で工夫したポイント

## Query Scopeによる責務分離

検索条件をModelへ集約することで、

Controllerをシンプルに保ち、

再利用しやすい設計としました。

---

## Eager Loading

一覧画面では

```php
with('images', 'category')
```

を利用し、

不要なSQL発行を抑えています。

---

## Pagination

商品数が増えても快適に利用できるよう、

Laravel標準のPaginationを利用しています。

---

## Modelへ業務ロジックを集約

在庫計算や販売可否判定など、

商品固有のロジックはModelへ集約しました。

```php
totalStock()

isSoldOut()

isAvailableForSale()
```

これにより、

ControllerやViewの責務を軽くし、

保守性・可読性を向上させています。

---

# ⭐ この章で伝えたいこと

本プロジェクトでは、

Laravelの標準機能を活用しながら、

単に機能を実装するだけではなく、

- 責務分離
- 保守性
- 拡張性
- 可読性

を意識した設計を行いました。

今後も新機能を追加しやすく、

長期的に運用できるアプリケーションを目指しています。

# 🚀 開発環境・デプロイ

本プロジェクトは、

ローカル開発環境だけでなく、

AWS EC2へデプロイし、

実際にWebアプリケーションとして公開しています。

---

# 🖥️ 開発環境

|項目|内容|
|----|----|
|OS|Ubuntu 24.04 LTS|
|PHP|8.3|
|Laravel|11|
|Database|MariaDB|
|Web Server|Nginx|
|Storage|Amazon S3|
|Package Manager|Composer|
|Frontend Build|Vite|
|Node.js|22.x|
|Git|GitHub|

---

# ☁️ 本番環境

|項目|内容|
|----|----|
|Cloud|AWS|
|Server|Amazon EC2|
|OS|Ubuntu Server|
|Web Server|Nginx|
|Application|Laravel 11|
|Database|MariaDB|
|Storage|Amazon S3|
|Payment|Square Payments API|

---

# 📂 デプロイ構成

```text
Internet

      │

      ▼

Amazon EC2

      │

      ▼

Nginx

      │

      ▼

Laravel

      │

 ┌────┴─────┐

 ▼          ▼

MariaDB     Amazon S3
```

---

# 📥 リポジトリ取得

```bash
git clone https://github.com/utl-flaxy/shining-will-shop.git

cd shining-will-shop
```

---

# 📦 Composer

依存パッケージをインストールします。

```bash
composer install
```

---

# 📦 Node.js

```bash
npm install

npm run build
```

---

# 🔑 環境変数

`.env` を作成します。

```bash
cp .env.example .env
```

Laravelキー生成

```bash
php artisan key:generate
```

---

# 🗄️ Database

MariaDBを作成し、

`.env`へ設定します。

```env
DB_CONNECTION=mysql

DB_HOST=127.0.0.1

DB_PORT=3306

DB_DATABASE=shining_will_shop

DB_USERNAME=xxxx

DB_PASSWORD=xxxx
```

---

# ☁️ Amazon S3

画像保存にはAmazon S3を利用しています。

```env
FILESYSTEM_DISK=s3

AWS_ACCESS_KEY_ID=*****

AWS_SECRET_ACCESS_KEY=*****

AWS_DEFAULT_REGION=ap-northeast-1

AWS_BUCKET=shining-will-shop-images
```

---

# 💳 Square

決済にはSquare Payments APIを利用しています。

```env
SQUARE_APPLICATION_ID=*****

SQUARE_ACCESS_TOKEN=*****

SQUARE_LOCATION_ID=*****
```

※ 実際の値は公開していません。

---

# 🔄 Migration

```bash
php artisan migrate
```

Seederを利用する場合

```bash
php artisan db:seed
```

---

# 🔗 Storage

ローカル環境の場合

```bash
php artisan storage:link
```

Amazon S3利用時は

Storage Facade経由で取得します。

```php
Storage::disk('s3')->url(...)
```

---

# ⚙️ キャッシュ

本番環境では

```bash
php artisan optimize

php artisan config:cache

php artisan route:cache

php artisan view:cache
```

更新時

```bash
php artisan optimize:clear
```

---

# 🌐 Nginx

Nginxから

```
public/
```

をDocumentRootとして公開しています。

Laravelの

```
storage

bootstrap/cache
```

には適切な権限を付与しています。

---

# 📤 デプロイ手順

更新時は

```bash
git pull

composer install

npm run build

php artisan migrate

php artisan optimize

sudo systemctl reload nginx
```

の順でデプロイしています。

---

# 🔒 セキュリティ

環境変数は

```
.env
```

で管理しています。

GitHubには

- AWSキー
- Squareトークン
- DBパスワード

などの秘密情報は含めていません。

---

# 💡 運用面で意識したこと

本番環境では、

画像をAmazon S3へ保存することで、

Webサーバーと画像ストレージを分離しています。

また、

Laravel Storageを利用することで、

ローカル環境とAWS環境の切り替えを容易にしています。

---

# ⭐ この章で伝えたいこと

本プロジェクトでは、

単にローカル環境で動作するだけではなく、

AWS EC2へデプロイし、

Nginx・MariaDB・Amazon S3・Square Payments APIを組み合わせた、

実際の運用を想定した構成で構築しました。

開発だけでなく、

環境構築・デプロイ・クラウド運用まで一貫して経験しています。

# 🚀 今後の改善予定

本プロジェクトではECサイトとして必要な機能を実装しました。

一方で、実務ではサービスを継続的に改善していくことが重要であると考えています。

今後は、より保守性・可用性・パフォーマンスを意識した改善に取り組みたいと考えています。

---

# 🐳 Docker対応

現在はローカル環境と本番環境をそれぞれ構築しています。

今後はDocker・Docker Composeを利用し、

開発環境をコンテナ化する予定です。

```text
Docker

├── PHP

├── Nginx

├── MariaDB

└── Node
```

これにより、

- 環境差異の解消
- セットアップ時間短縮
- 開発効率向上

を目指します。

---

# ⚙️ GitHub Actions

現在は

```
git pull

↓

composer install

↓

npm run build

↓

deploy
```

でデプロイしています。

今後はGitHub Actionsを利用して、

```text
Push

↓

Test

↓

Build

↓

Deploy
```

まで自動化したいと考えています。

---

# 🧪 自動テスト

現在は手動テストを中心に確認しています。

今後は

- PHPUnit
- Pest

を利用して、

- Model
- Controller
- Service

の自動テストを追加したいと考えています。

---

# ⚡ キャッシュ

商品一覧はアクセス数が最も多い画面になります。

今後はRedisを導入し、

キャッシュを利用することで、

レスポンス速度の向上を目指します。

---

# 🌍 CloudFront

現在はAmazon S3から直接画像を取得しています。

今後はCloudFrontを導入し、

```text
Browser

↓

CloudFront

↓

Amazon S3
```

の構成へ変更し、

- CDN配信
- 高速表示
- 負荷分散

を実現したいと考えています。

---

# 🔍 検索機能の強化

現在対応している検索

- キーワード
- カテゴリー
- 並び替え

今後追加予定

- 価格帯検索
- 在庫ありのみ
- タグ検索
- ブランド検索

---

# ❤️ お気に入り機能

ユーザーがお気に入り商品を保存できる機能を追加予定です。

予定機能

- お気に入り登録
- お気に入り一覧
- ログインユーザー管理

---

# ⭐ レビュー機能

レビュー投稿機能を追加予定です。

実装予定

- ★評価
- コメント
- 購入者限定レビュー
- 投稿日時

---

# 📧 通知機能

Laravel Notificationを利用し、

注文時に

- 購入者
- 管理者

へメール通知を送信する予定です。

---

# 📈 管理画面分析

管理画面では、

売上分析機能を追加予定です。

例

- 月別売上
- 人気商品ランキング
- 注文数推移
- カテゴリー別売上

---

# 🔒 セキュリティ

今後は

- Rate Limit
- CSP最適化
- Security Header
- Validation強化

など、

より安全なWebアプリケーションを目指します。

---

# ☁️ AWS改善

今後学習予定

- Amazon RDS
- Application Load Balancer
- ECS
- ECR
- CloudWatch

AWSをより活用した構成へ改善したいと考えています。

---

# 🏗️ Infrastructure as Code

今後はTerraformを学習し、

AWSインフラもコードで管理できるようにしたいと考えています。

---

# 📖 API設計

現在はWebアプリケーションとして開発しています。

今後は

OpenAPI

を利用してAPI仕様を明文化し、

SPAやモバイルアプリから利用できる構成も検討しています。

---

# 💡 このプロジェクトで感じた課題

開発当初は、

「機能を完成させること」

を目標としていました。

しかし開発を進める中で、

重要なのは

- 保守性
- 可読性
- 拡張性
- 運用性

であることを学びました。

今後は、

より長く運用できるアプリケーションを意識した設計・実装へ取り組みたいと考えています。

---

# 🎯 今後学びたい技術

- Docker
- GitHub Actions
- PHPUnit
- Pest
- Redis
- CloudFront
- Amazon RDS
- ECS
- Terraform
- OpenAPI

---

# ⭐ エンジニアとして目指す姿

本プロジェクトを通して、

単に機能を実装するだけではなく、

「なぜその設計にするのか」

「どのようにすれば保守しやすいか」

を考えることの重要性を学びました。

今後も継続的に改善を重ねながら、

ユーザーに価値を届けられるWebアプリケーションを開発できるエンジニアを目指します。

# 🎓 このプロジェクトを通して学んだこと

本プロジェクトでは、

Laravelを利用したECサイトを一から設計・実装・デプロイすることを通して、

Webアプリケーション開発に必要な知識だけでなく、

設計・保守・運用まで含めた開発プロセスを学ぶことができました。

当初は

「動くものを作る」

ことだけを目標としていましたが、

開発を進める中で、

より重要なのは

- 保守しやすいこと
- 拡張しやすいこと
- データが壊れないこと
- 長く運用できること

であると実感しました。

---

# 📚 技術面で学んだこと

## Laravel

本プロジェクトでは、

Laravel標準機能を積極的に活用しました。

- MVCアーキテクチャ
- Eloquent ORM
- Query Scope
- Blade
- Storage Facade
- Database Transaction
- Pagination

Laravelらしい設計を意識しながら実装しました。

---

## AWS

AWSでは

- Amazon EC2
- Amazon S3
- IAM

を利用しました。

単にサーバーへアップロードするだけではなく、

Amazon S3へ画像を保存し、

Laravel Storage Facadeを利用した構成を経験できました。

---

## データベース設計

テーブル設計では

- 商品
- カテゴリー
- 商品画像
- 商品バリエーション
- 注文
- 注文明細

を分離し、

拡張しやすい構成を意識しました。

---

## 決済

Square Payments APIを利用し、

実際の決済処理を体験しました。

決済成功後のみ注文を保存することで、

データ整合性を意識した設計を学ぶことができました。

---

# 💡 開発で特に意識したこと

## 保守性

Controllerへロジックを書きすぎないようにし、

ModelのQuery Scopeやメソッドを利用して責務を分離しました。

検索機能もScopeとして実装することで、

再利用しやすい構成を意識しています。

---

## 拡張性

将来的な機能追加を考慮し、

- 商品画像
- 商品バリエーション
- カテゴリー

を独立したテーブルとして設計しました。

また、

Storage Facadeを利用することで、

ローカルストレージとAmazon S3を容易に切り替えられる構成としています。

---

## データ整合性

ECサイトでは

- 注文
- 在庫
- 決済

の整合性が重要です。

そのため、

Database Transactionを利用し、

途中で処理が失敗した場合は

ロールバックするよう実装しました。

---

# 🌱 今後の目標

本プロジェクトを通して、

Webアプリケーション開発の楽しさと奥深さを実感しました。

今後は、

- Docker
- GitHub Actions
- PHPUnit / Pest
- Redis
- CloudFront
- Terraform

なども学習し、

より実務に近い開発へ挑戦していきたいと考えています。

また、

単に機能を追加するだけではなく、

パフォーマンスや保守性も意識した開発を継続していきます。

---

# 🎯 このポートフォリオで伝えたかったこと

本プロジェクトでは、

ECサイトとして必要な機能を実装するだけではなく、

- 商品検索
- ページネーション
- 注文ステータス管理
- トランザクションによる安全な注文処理
- Amazon S3による画像管理
- Square Payments APIとの連携

など、

実務で利用される技術や設計を意識して開発しました。

また、

Laravel標準機能を活用し、

保守性・拡張性を意識した実装を心掛けています。

---

# 📝 おわりに

最後までご覧いただき、ありがとうございます。

このプロジェクトは、

私自身が「設計から運用まで経験したい」という思いで制作したポートフォリオです。

実装だけでなく、

設計・クラウド・データベース・決済・保守性まで考えながら開発を進めることで、多くの学びを得ることができました。

今後も継続的に改善を重ねながら、

より品質の高いWebアプリケーションを開発できるエンジニアを目指していきます。

ご意見・ご指摘などがございましたら、ぜひお気軽にご連絡ください。

---

# 📄 ライセンス

本プロジェクトはポートフォリオとして公開しています。

ソースコード・画像・デザイン等の無断転載・再配布はご遠慮ください。

© 2026 Yuu Iwamoto
