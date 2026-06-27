# Shining Will Shop

> Laravel 11 × AWS を用いて開発した、アイドル向けECサイトです。

商品販売だけではなく、

- 在庫管理
- 注文管理
- Square決済
- Amazon S3画像管理

など、実務で利用されるECサイトの構成を意識して設計・実装しました。

---

## URL

https://（デプロイ後に更新）

管理画面

https://（管理画面URL）

---

## GitHub

https://github.com/utl-flaxy/shining-will-shop

---

# 開発背景

前職では業務システムを利用する立場でしたが、

「実際にサービスを設計・開発・運用できるエンジニア」

を目指し、本プロジェクトを制作しました。

単に画面を作るだけではなく、

- データ整合性
- 保守性
- 拡張性
- AWSを利用した運用

まで考慮した設計を意識しています。

---

# このプロジェクトで特に意識したこと

## 在庫整合性

注文処理では

- 注文作成
- 在庫更新

をトランザクションで管理し、

途中で失敗した場合はロールバックされるようにしています。

---

## 保守性

検索処理はControllerへ直接記述せず、

Eloquent Scopeへ分離しました。

```php
Product::query()
    ->published()
    ->keyword($keyword)
    ->category($category)
    ->sort($sort)
    ->paginate(12);
```

これにより

- Controllerをシンプルに保つ
- 再利用しやすい設計

を実現しています。

---

## AWS利用

画像はAmazon S3へ保存しています。

Laravel Storageを利用することで

ローカルストレージとS3を切り替えられる構成にしました。

---

## 主な技術

|技術|内容|
|----|----|
|Laravel 11|Webアプリケーション|
|PHP 8.3|バックエンド|
|MariaDB|データベース|
|AWS EC2|アプリケーションサーバ|
|AWS S3|画像保存|
|Nginx|Webサーバ|
|Square API|決済|
|Filament|管理画面|
|Tailwind CSS|UI|

---

## スクリーンショット

（ここにTOP画像）

（商品一覧）

（商品詳細）

（管理画面）

（注文一覧）
# システム構成

本アプリケーションは AWS 上へデプロイし、Laravelを中心とした構成で運用しています。

```
                     Internet
                         │
                         ▼
                 Nginx (EC2)
                         │
              ┌──────────┴──────────┐
              │                     │
              ▼                     ▼
        Laravel 11             MariaDB
              │
              │ Storage
              ▼
         Amazon S3
      （商品画像・カテゴリ画像）

              │
              ▼
        Square Payments
```

---

# AWS構成

|サービス|用途|
|---------|-----------------------------|
|Amazon EC2|Laravelアプリケーション実行環境|
|Amazon S3|商品画像・カテゴリ画像保存|
|Nginx|Webサーバ|
|MariaDB|データベース|
|Square API|オンライン決済|

---

# アーキテクチャ

```
Browser
    │
    ▼
Nginx
    │
    ▼
Laravel

 ├── Product
 ├── Order
 ├── Cart
 ├── Checkout
 ├── Category
 └── Admin

    │
    ├───────────────► MariaDB

    └───────────────► Amazon S3
```

---

# ディレクトリ構成

```
app
├── Http
│   └── Controllers
│       ├── Admin
│       ├── CheckoutController.php
│       ├── ProductController.php
│       └── StoreController.php
│
├── Models
│   ├── Product.php
│   ├── ProductImage.php
│   ├── ProductVariant.php
│   ├── Order.php
│   ├── OrderItem.php
│   └── Category.php
│
└── Services
    └── SquarePaymentService.php

resources
└── views
    ├── shop
    ├── products
    ├── cart
    └── admin
```

---

# 採用した技術

|カテゴリ|技術|
|---------|----------------|
|Backend|Laravel 11 / PHP 8.3|
|Frontend|Blade / Tailwind CSS|
|Database|MariaDB|
|Server|Nginx|
|Cloud|AWS EC2 / Amazon S3|
|Payment|Square API|
|Admin|Filament|
|Version Control|Git / GitHub|

---

# インフラで意識したこと

画像ファイルは Laravel Storage を利用して管理しています。

保存先を Storage Facade に統一することで、

- ローカルストレージ
- Amazon S3

をコードの変更を最小限に切り替えられる構成にしました。

```php
Storage::disk('s3')->url($image->url);
```

また、ControllerやViewでストレージの実装を意識しない構成とすることで、保守性と拡張性を高めています。

# 機能一覧

本アプリケーションでは、利用者向け機能と管理者向け機能を分けて実装しています。

---

# 利用者向け機能

|機能|内容|
|----|----|
|商品一覧|公開中の商品を一覧表示|
|商品検索|商品名・説明文によるキーワード検索|
|カテゴリー検索|カテゴリー別の商品一覧表示|
|並び替え|新着・価格順・名前順で表示|
|商品詳細|画像・価格・説明・在庫を表示|
|画像スライダー|Swiperを利用した商品画像表示|
|カート機能|数量を指定して商品を追加|
|注文確認|注文内容を確認して決済へ進む|
|Square決済|Square Payments APIによるオンライン決済|
|注文完了|決済完了後に注文データを保存|
|在庫表示|在庫状況に応じてSOLD OUTを表示|

---

# 管理者向け機能

|機能|内容|
|----|----|
|ログイン認証|管理画面へのアクセス制御|
|商品管理|商品の登録・編集・削除|
|カテゴリー管理|カテゴリーの登録・編集・削除|
|商品画像管理|複数画像アップロード|
|Amazon S3画像保存|商品画像・カテゴリ画像をS3へ保存|
|公開管理|公開・非公開の切り替え|
|在庫管理|通常在庫・メンバー別在庫管理|
|注文一覧|受注情報を一覧表示|
|注文詳細|購入内容・購入者情報を確認|
|注文ステータス管理|注文受付・発送準備・発送済み等を管理|

---

# 検索機能

商品検索では、Eloquent Scopeを利用して検索条件を分離しています。

- キーワード検索
- カテゴリー検索
- 並び替え

```php
Product::query()
    ->published()
    ->keyword($keyword)
    ->category($category)
    ->sort($sort)
    ->paginate(12);
```

Controllerへ検索ロジックを書かず、Modelへ責務を分離することで保守性を高めています。

---

# 商品画像管理

商品画像はLaravel Storageを利用して保存しています。

保存先を変更してもアプリケーション側のコード変更を最小限に抑えられるよう設計しています。

```
Browser
     │
     ▼
Laravel Storage
     │
 ┌───┴─────────┐
 │             │
 ▼             ▼
Local       Amazon S3
```

---

# 注文処理

注文時には以下の処理を実装しています。

1. カート内容取得
2. 注文データ作成
3. 注文明細作成
4. 在庫減算
5. 決済情報保存
6. カート削除

データ整合性を保つため、トランザクションを利用しています。

---

# 実装で意識したこと

- MVCアーキテクチャを意識した責務分離
- Eloquent Scopeによる検索処理の共通化
- Storage Facadeによるストレージ抽象化
- Amazon S3による画像管理
- トランザクションを利用した安全な在庫更新
- 保守性・拡張性を考慮した実装

# データベース設計

本アプリケーションでは、商品・注文・カテゴリ・画像をそれぞれ独立したテーブルとして設計し、正規化を意識したデータ構造を採用しています。

---

# ER図

```text
Category
──────────────
id
name
image
sort_order
is_active
created_at
updated_at
        │ 1
        │
        │
        ▼
Product
──────────────
id
category_id
name
description
price
stock
sku
is_active
is_published
is_stock_managed
created_at
updated_at
        │
 ┌──────┴────────┐
 │               │
 ▼               ▼

ProductImage     ProductVariant
────────────     ──────────────
id               id
product_id       product_id
url              name
sort_order       stock
created_at       created_at

        │
        ▼

OrderItem
──────────────
id
order_id
product_id
product_name
price
quantity

        ▲
        │

Order
──────────────
id
order_number
customer_name
email
phone
subtotal
shipping_fee
total
status
square_payment_id
created_at
```

---

# テーブル設計

## categories

カテゴリー情報を管理します。

|カラム|内容|
|------|----------------|
|name|カテゴリー名|
|image|カテゴリー画像|
|sort_order|表示順|
|is_active|公開状態|

---

## products

商品情報を管理します。

|カラム|内容|
|------|----------------|
|category_id|所属カテゴリ|
|name|商品名|
|description|商品説明|
|price|販売価格|
|stock|通常在庫|
|sku|商品コード|
|is_active|販売状態|
|is_published|公開状態|
|is_stock_managed|在庫管理有無|

---

## product_images

商品の画像を管理します。

1商品に対して複数画像を登録できる構成です。

|カラム|内容|
|------|----------------|
|product_id|対象商品|
|url|画像パス|
|sort_order|表示順|

---

## product_variants

メンバー別商品やサイズ違いなどを想定した在庫管理テーブルです。

|カラム|内容|
|------|----------------|
|product_id|対象商品|
|name|バリエーション名|
|stock|在庫数|

---

## orders

注文情報を管理します。

|カラム|内容|
|------|----------------|
|order_number|注文番号|
|customer_name|購入者名|
|email|メールアドレス|
|phone|電話番号|
|subtotal|商品合計|
|shipping_fee|送料|
|total|請求金額|
|status|注文ステータス|
|square_payment_id|Square決済ID|

---

## order_items

注文時点の商品情報を保持します。

商品情報をコピーして保存することで、

商品マスタが変更されても過去の注文内容が変わらないよう設計しています。

---

# モデル構成

```text
Category
   │
   └── hasMany(Product)

Product
   ├── belongsTo(Category)
   ├── hasMany(ProductImage)
   ├── hasMany(ProductVariant)
   └── hasMany(OrderItem)

Order
   └── hasMany(OrderItem)

OrderItem
   ├── belongsTo(Order)
   └── belongsTo(Product)
```

---

# 設計で意識したこと

## 責務の分離

商品画像は products テーブルへ保存せず、

product_images テーブルへ分離しています。

これにより

- 複数画像対応
- 表示順変更
- 将来的な動画対応

などへ柔軟に対応できます。

---

## 注文情報の保持

注文時の商品名・価格を OrderItem に保存しています。

これにより、

商品価格を変更しても過去の注文履歴には影響しません。

---

## 拡張性

在庫管理は ProductVariant テーブルを用意し、

将来的に

- サイズ
- カラー
- メンバー別商品

などへ対応できる設計としています。

# 注文処理

本アプリケーションでは、注文処理中にデータ不整合が発生しないよう、データベーストランザクションを利用しています。

例えば、

- 注文は作成された
- 在庫だけ減らなかった

あるいは

- 在庫だけ減った
- 注文が作成されなかった

といった状態にならないよう設計しています。

---

# 注文フロー

```text
ユーザー
    │
    ▼
商品詳細
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
注文作成
    │
    ▼
在庫更新
    │
    ▼
注文完了
```

---

# シーケンス図

```text
User

 │
 │ 商品購入
 ▼

CheckoutController

 │
 │ Square決済
 ▼

Square API

 │
 │ Success
 ▼

Laravel

 │
 ├───────────────┐
 │               │
 ▼               ▼

Orders      OrderItems

 │
 ▼

Product

 │
 ▼

Stock Update

 │
 ▼

Commit
```

---

# トランザクション

注文処理は Transaction で管理しています。

```php
DB::transaction(function () {

    // 注文作成

    // 注文明細作成

    // 在庫減算

});
```

途中で例外が発生した場合は、

すべての更新内容をロールバックします。

---

# なぜTransactionを利用したのか

ECサイトでは、

注文情報と在庫情報は必ず一致している必要があります。

例えば、

```
注文作成

↓

在庫更新失敗
```

となると、

注文だけ存在し、

在庫が残ったままになります。

逆に

```
在庫更新

↓

注文失敗
```

になると、

在庫だけ減ってしまいます。

そのため、

注文処理全体を1つのTransactionとして管理しています。

---

# 在庫更新

商品には2種類の在庫管理を実装しています。

## 通常商品

```
products.stock
```

を更新します。

---

## バリエーション商品

```
product_variants.stock
```

を更新します。

これにより、

- サイズ別
- カラー別
- メンバー別商品

などへ拡張できる構成としています。

---

# SOLD OUT判定

商品一覧・カテゴリ一覧・商品詳細では、

共通メソッドで在庫判定しています。

```php
$product->isSoldOut();
```

内部では

```php
$product->totalStock()
```

を利用し、

通常在庫・バリエーション在庫の違いを意識せず利用できます。

---

# Square決済

決済には Square Payments API を採用しています。

```
Browser

↓

Square Web SDK

↓

Square API

↓

Laravel

↓

注文保存
```

決済成功後のみ注文データを保存する構成です。

---

# エラーハンドリング

注文処理中に例外が発生した場合は、

- Transactionをロールバック
- 在庫を元に戻す
- 注文を作成しない

よう実装しています。

これによりデータ整合性を維持しています。

---

# この設計で意識したこと

## データ整合性

注文・在庫・決済情報が必ず一致するよう設計しました。

---

## 責務分離

Controllerには業務ロジックを極力書かず、

Model・Serviceへ責務を分離しています。

---

## 保守性

在庫判定は

```php
totalStock()

isSoldOut()
```

へ集約することで、

View側は

```php
$product->isSoldOut()
```

だけで利用できます。

在庫管理方法が変更されても、

画面側の修正は不要です。

---

# この機能で学んだこと

- Database Transaction
- データ整合性
- 決済フロー
- 在庫管理
- Service層による責務分離
- 保守性を考慮した設計
- 将来的な機能追加を考慮した拡張性

# Amazon S3による画像管理

本アプリケーションでは、商品画像・カテゴリー画像を Amazon S3 に保存しています。

Laravel Storage を利用することで、ローカルストレージと S3 を切り替えられる構成にしています。

---

# 採用理由

画像データをEC2へ保存すると、

- ディスク容量が増える
- サーバー移行が難しい
- 複数台構成に対応しづらい

といった課題があります。

そのため画像は Amazon S3 に保存する構成を採用しました。

---

# システム構成

```text
Browser
    │
    ▼
Laravel

    │
    ▼

Storage Facade

    │
 ┌──┴──────────┐
 │             │
 ▼             ▼

Local        Amazon S3

```

Laravel側では保存先を意識せず、

Storage Facade経由でアクセスしています。

---

# アップロード処理

商品登録時には画像をS3へ保存しています。

```php
$path = $request
    ->file('image')
    ->store(
        'products',
        's3'
    );
```

保存されたパスをデータベースへ登録しています。

```
products/login-logo.png
```

---

# データベース

ProductImage

|カラム|内容|
|------|----------------|
|product_id|対象商品|
|url|S3保存パス|
|sort_order|表示順|

DBにはURLではなく、

```
products/login-logo.png
```

のみ保存しています。

---

# 画像表示

画面表示時は Storage を利用してURLを取得しています。

```php
Storage::disk('s3')
    ->url($image->url);
```

生成されるURL

```
https://bucket-name.s3.ap-northeast-1.amazonaws.com/products/login-logo.png
```

保存先が変わってもViewを修正する必要がありません。

---

# カテゴリー画像

カテゴリー画像も同様に

```
categories/
```

配下へ保存しています。

```
categories/member1.jpg

categories/member2.jpg
```

商品画像と分けることで管理しやすくしています。

---

# ディレクトリ構成

```
Amazon S3

products
├── product1.jpg
├── product2.jpg
└── sample.jpg

categories
├── member1.jpg
├── member2.jpg
└── member3.jpg
```

---

# Storage Facadeを利用した理由

画像URLを

```php
asset(...)
```

で直接生成すると、

保存先が変わるたびに修正が必要になります。

本プロジェクトでは

```php
Storage::url(...)
```

または

```php
Storage::disk('s3')->url(...)
```

を利用しています。

これにより

- Local
- Amazon S3

を簡単に切り替えられる構成としました。

---

# 実装時に苦労した点

S3へ切り替える際、

- Flysystem Adapterの導入
- IAMユーザー設定
- バケットポリシー
- Public Access設定
- Storage Facadeへの統一
- ローカル保存データの移行

などを行い、ローカル環境と本番環境の両方で画像が表示される構成を実現しました。

---

# この機能で学んだこと

- Amazon S3
- IAM
- Bucket Policy
- Laravel Storage
- Flysystem
- Public Object
- クラウドストレージ運用
- ストレージ抽象化
- 保守性・拡張性を考慮した設計

# 検索機能・設計思想

本アプリケーションでは、検索機能を単に実装するだけではなく、保守性・再利用性を意識した設計を採用しました。

---

# 実装した検索機能

利用者は以下の条件で商品を検索できます。

- 商品名検索
- 商品説明検索
- カテゴリー検索
- 新着順
- 価格が安い順
- 価格が高い順
- 名前順

複数条件を組み合わせて検索できるようになっています。

---

# 検索フロー

```text
Browser
     │
     ▼
StoreController
     │
     ▼
Product Model

 ├── scopePublished()
 ├── scopeKeyword()
 ├── scopeCategory()
 └── scopeSort()

     │
     ▼

MariaDB
```

---

# Controller

Controllerでは検索条件を取得するだけにしています。

```php
$products = Product::query()
    ->published()
    ->keyword($keyword)
    ->category($category)
    ->sort($sort)
    ->paginate(12)
    ->withQueryString();
```

ControllerへSQLを書かないようにしています。

---

# Model

検索処理はEloquent Scopeへ分離しました。

```php
scopePublished()

scopeKeyword()

scopeCategory()

scopeSort()
```

これにより、

- Controllerをシンプルに保つ
- 再利用しやすい
- テストしやすい

設計になっています。

---

# Scope例

```php
public function scopeKeyword(
    Builder $query,
    ?string $keyword
): Builder {

    if (blank($keyword)) {
        return $query;
    }

    return $query->where(function ($q) use ($keyword) {

        $q->where('name', 'like', "%{$keyword}%")
          ->orWhere('description', 'like', "%{$keyword}%");

    });
}
```

検索条件が入力されていない場合は何も適用しないようにしています。

---

# 並び替え

並び替えもScopeへ集約しています。

```php
public function scopeSort(
    Builder $query,
    ?string $sort
): Builder {

    return match ($sort) {

        'price_asc'
            => $query->orderBy('price'),

        'price_desc'
            => $query->orderByDesc('price'),

        'name'
            => $query->orderBy('name'),

        default
            => $query->latest(),

    };
}
```

新しい並び順を追加する場合でも、このScopeのみ修正すれば対応できます。

---

# ページネーション

検索結果は1ページ12件で表示しています。

```php
->paginate(12)
->withQueryString();
```

検索条件を保持したままページ移動できるようにしています。

---

# View

検索フォームはGETリクエストで送信しています。

```text
Keyword

Category

Sort

↓

StoreController

↓

Product Scope

↓

一覧表示
```

URLに検索条件が反映されるため、

- ブックマーク
- URL共有
- リロード

にも対応しています。

---

# 設計で意識したこと

## Controllerを薄くする

Controllerには検索ロジックを書かず、

Modelへ責務を分離しています。

---

## Eloquentを活かす

Laravel標準のQuery Scopeを利用することで、

Laravelらしい実装を意識しました。

---

## 保守性

検索条件が追加されても、

Controllerの修正は最小限です。

例えば

- メーカー検索
- 価格帯検索
- タグ検索

などもScopeを追加するだけで対応できます。

---

# この機能で学んだこと

- Eloquent Scope
- Query Builder
- GET検索
- ページネーション
- 責務分離
- 保守性を意識した設計
- Laravelらしい実装

# ローカル開発環境

## 開発環境

|項目|バージョン|
|----|----------|
|PHP|8.3|
|Laravel|11|
|MariaDB|10.x|
|Composer|2.x|
|Node.js|22.x|
|npm|10.x|
|Nginx|最新版|
|AWS EC2|Amazon Linux 2023|
|Amazon S3|画像保存|
|Square API|Sandbox|

---

# 必要なソフトウェア

以下をインストールしてください。

- PHP 8.3
- Composer
- Node.js
- npm
- MariaDB
- Git

---

# インストール

リポジトリを取得します。

```bash
git clone https://github.com/utl-flaxy/shining-will-shop.git

cd shining-will-shop
```

---

# Composer

```bash
composer install
```

---

# Node.js

```bash
npm install
```

---

# .env

```bash
cp .env.example .env
```

---

# APP_KEY

```bash
php artisan key:generate
```

---

# データベース

MariaDBへデータベースを作成します。

```sql
CREATE DATABASE shining_will_shop;
```

.env

```env
DB_CONNECTION=mysql

DB_HOST=127.0.0.1

DB_PORT=3306

DB_DATABASE=shining_will_shop

DB_USERNAME=root

DB_PASSWORD=password
```

---

# Migration

```bash
php artisan migrate
```

---

# Seed

必要に応じて

```bash
php artisan db:seed
```

---

# Storage

ローカル環境では

```bash
php artisan storage:link
```

を実行してください。

---

# Vite

```bash
npm run dev
```

---

# Laravel

```bash
php artisan serve
```

ブラウザ

```
http://127.0.0.1:8000
```

---

# Amazon S3

本番環境では画像保存先としてAmazon S3を利用しています。

```
FILESYSTEM_DISK=s3
```

.env

```env
AWS_ACCESS_KEY_ID=

AWS_SECRET_ACCESS_KEY=

AWS_DEFAULT_REGION=ap-northeast-1

AWS_BUCKET=xxxxxxxxxxxx
```

Laravel Storageを利用しているため、

コードを変更せず保存先を切り替えられます。

---

# Square

決済には Square Sandbox を利用しています。

```env
SQUARE_APPLICATION_ID=

SQUARE_ACCESS_TOKEN=

SQUARE_LOCATION_ID=
```

---

# 本番環境

本番環境はAWSへデプロイしています。

```
Internet

↓

Amazon EC2

↓

Nginx

↓

Laravel

↓

MariaDB

↓

Amazon S3
```

---

# デプロイ手順

コード取得

```bash
git pull
```

Composer

```bash
composer install --no-dev --optimize-autoloader
```

Migration

```bash
php artisan migrate --force
```

Cache

```bash
php artisan optimize
```

Frontend

```bash
npm install

npm run build
```

---

# 動作確認

以下の機能を確認しています。

- 商品一覧
- 商品検索
- カテゴリー検索
- 商品詳細
- カート
- Square決済
- 注文作成
- 注文管理
- Amazon S3画像表示

---

# ディレクトリ構成

```
app
bootstrap
config
database
public
resources
routes
storage
tests
vendor
```

---

# 今後追加予定

- GitHub ActionsによるCI
- PHPUnitによる自動テスト
- Docker対応
- Amazon CloudFront
- メール通知
- レビュー機能

# 今後の改善予定

本プロジェクトは、ECサイトとして必要な基本機能に加え、AWS・決済・画像管理などを実装しています。

今後は、より実務を意識したアーキテクチャや運用面の改善にも取り組みたいと考えています。

---

# 1. Docker対応

現在はローカル環境と本番環境をそれぞれ構築しています。

今後は Docker / Docker Compose を利用し、

- PHP
- Nginx
- MariaDB

をコンテナ化することで、環境差異の少ない開発環境を構築したいと考えています。

---

# 2. CI/CD

現在はGitHubへPush後、EC2へデプロイしています。

今後は GitHub Actions を利用し、

```
Push

↓

Test

↓

Build

↓

Deploy
```

まで自動化する予定です。

これにより、

- デプロイミス防止
- 品質向上
- 開発効率向上

を目指します。

---

# 3. 自動テスト

現在は手動テストを中心に確認しています。

今後は

- PHPUnit
- Pest

を利用して

- Model
- Controller
- Service

の自動テストを追加したいと考えています。

---

# 4. CloudFront

現在はS3から直接画像を配信しています。

今後はCloudFrontを導入し、

```
Browser

↓

CloudFront

↓

Amazon S3
```

の構成に変更することで、

- 表示速度改善
- CDNによる高速配信
- 負荷分散

を実現したいと考えています。

---

# 5. キャッシュ

商品一覧はアクセス頻度が高いため、

Redisを利用したキャッシュ機構を導入し、

レスポンス速度の改善を行いたいと考えています。

---

# 6. 検索機能の拡張

現在は

- キーワード
- カテゴリー
- 並び替え

に対応しています。

今後は

- 価格帯
- 在庫有無
- タグ検索
- ブランド検索

などを追加予定です。

---

# 7. 通知機能

注文完了後に

- 購入者
- 管理者

へメール通知を送信する機能を追加予定です。

Laravel Notification を利用した実装を検討しています。

---

# 8. レビュー機能

商品レビュー機能を追加し、

- ★評価
- コメント
- 購入者限定レビュー

などへ対応したいと考えています。

---

# 9. お気に入り機能

会員が気になる商品を保存できる

Favorite機能を追加予定です。

---

# 10. セキュリティ強化

今後は

- Rate Limit
- CSP最適化
- Security Header追加
- Validation強化

など、

より安全なWebアプリケーションを目指します。

---

# このプロジェクトを通して感じた課題

開発当初は

「機能を作ること」

を優先していましたが、

開発を進める中で、

- 保守性
- 拡張性
- データ整合性
- クラウド運用

を考慮した設計の重要性を学びました。

特にAmazon S3への画像保存やトランザクションによる在庫管理を実装したことで、

実務に近い設計・運用を経験できたと感じています。

---

# 今後学習したい技術

- Docker
- GitHub Actions
- PHPUnit / Pest
- Redis
- CloudFront
- AWS RDS
- AWS ALB
- ECS
- Terraform
- OpenAPI

# このプロジェクトで学んだこと

本プロジェクトは、Laravelを用いたECサイトとして開発を開始しました。

当初は「商品を表示し、購入できるサイト」を目標としていましたが、開発を進める中で、単に機能を実装するだけではなく、「保守しやすい設計」「運用を見据えた構成」の重要性を強く実感しました。

---

# 技術面で学んだこと

このプロジェクトでは以下の技術・考え方を学びました。

## Laravel

- MVCアーキテクチャ
- Eloquent ORM
- Query Scope
- Storage Facade
- Service層による責務分離
- Database Transaction

---

## AWS

- Amazon EC2へのデプロイ
- Amazon S3への画像保存
- IAMユーザーの作成
- Bucket Policyの設定
- Flysystemを利用したストレージ管理

---

## Web開発

- RESTを意識したルーティング
- Bladeテンプレート
- Tailwind CSS
- Pagination
- 検索機能
- バリデーション

---

## 外部サービス連携

Square Payments APIを利用し、

決済成功時のみ注文を確定するフローを実装しました。

外部APIを利用する際のエラーハンドリングや例外処理についても学ぶことができました。

---

# 開発で特に意識したこと

## 保守性

Controllerへロジックを書きすぎないよう意識し、

- Query Scope
- Model
- Service

へ責務を分離しました。

これにより、機能追加や修正がしやすい構成を目指しました。

---

## データ整合性

ECサイトでは

- 注文
- 在庫
- 決済

が必ず一致する必要があります。

そのためDatabase Transactionを利用し、

途中で処理が失敗した場合はロールバックする構成にしています。

---

## 拡張性

将来的な機能追加を考慮し、

- 商品画像
- 商品バリエーション
- カテゴリー

を独立したテーブルとして設計しました。

また、Storage Facadeを利用することで、

ローカルストレージとAmazon S3を切り替えられる構成にしています。

---

# 開発を通して成長できたこと

最初は

「動けば良い」

という考え方でした。

しかし、このプロジェクトを通して

- なぜこの設計にするのか
- なぜ責務を分けるのか
- なぜTransactionを利用するのか
- なぜAWSを利用するのか

といった設計意図を考えながら開発するようになりました。

技術だけではなく、

保守性・拡張性・運用まで意識して設計する重要性を学ぶことができました。

---

# 今後の目標

今後はさらに実務に近い開発を経験するため、

- Docker
- GitHub Actions
- PHPUnit / Pest
- Redis
- CloudFront
- Terraform

などを学習し、

より品質の高いWebアプリケーションを開発できるエンジニアを目指しています。

---

# 最後に

本プロジェクトでは、ECサイトに必要な基本機能だけでなく、

- 商品検索
- ページネーション
- 注文ステータス管理
- トランザクションによる在庫更新
- Amazon S3を利用した画像管理
- Square決済

など、実務を意識した機能を実装しました。

このプロジェクトを通して、

「動くものを作る」だけではなく、

**保守性・拡張性・運用性を意識した設計と実装**

を学ぶことができました。

今後も継続的に改善を行いながら、より実践的なWebアプリケーション開発に取り組んでいきます。
