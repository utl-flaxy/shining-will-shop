# Shining Will Shop

> **Laravel 11で開発した、実務を意識したECサイトポートフォリオ**

![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?logo=laravel\&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php\&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8-4479A1?logo=mysql\&logoColor=white)
![AWS](https://img.shields.io/badge/AWS-EC2-FF9900?logo=amazonaws\&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-2496ED?logo=docker\&logoColor=white)
![GitHub Actions](https://img.shields.io/badge/CI-GitHub_Actions-2088FF?logo=githubactions\&logoColor=white)
![PHPUnit](https://img.shields.io/badge/Test-PHPUnit-366488)
![Filament](https://img.shields.io/badge/Admin-Filament-v3-8A2BE2)

---

# Demo

| Item   | URL                                            |
| ------ | ---------------------------------------------- |
| Demo   | https://shining-will-shop.com                  |
| GitHub | https://github.com/utl-flaxy/shining-will-shop |

---

# Screenshots

> トップページ・商品一覧・商品詳細・カート・注文画面・管理画面などのスクリーンショットを掲載予定

---

# Overview

Shining Will Shop は、Laravel 11 を用いて開発したECサイトです。

一般的な商品販売サイトとしての機能だけではなく、

* 商品検索
* カート
* 注文処理
* 在庫管理
* マイページ
* 管理画面
* 自動テスト
* CI（GitHub Actions）

まで実装し、実務を意識した構成で開発しました。

本プロジェクトでは「機能数」よりも、

* 保守性
* 拡張性
* 可読性
* 品質

を重視しています。

---

# Why This Project?

Laravelの基本的なCRUDだけではなく、

実際のWebアプリケーション開発で必要になる

* 認証
* 注文フロー
* 在庫管理
* 管理画面
* テスト
* CI/CD

まで一貫して経験することを目的として制作しました。

また、

**「あとから機能追加しやすい設計」**

を目標とし、

Controllerへロジックを集中させず、

Model・Eloquent・Laravel標準機能を活用した構成を採用しています。

---

# Design Principles

このプロジェクトでは以下の方針で設計しました。

* Laravel標準機能を積極的に利用する
* Fat Controllerを避ける
* ビジネスロジックをModelへ集約する
* Query Scopeを活用して検索処理を共通化する
* 保守性・拡張性を優先する
* 自動テストを前提とした設計にする

これにより、新しい機能追加や仕様変更にも対応しやすい構成を目指しました。

---

# Tech Stack

## Backend

* PHP 8.3
* Laravel 11
* Eloquent ORM
* Laravel Breeze
* Laravel Mail

## Frontend

* Blade
* Tailwind CSS
* Vite
* JavaScript

## Database

* MySQL
* SQLite（Testing）

## Infrastructure

* AWS EC2
* Docker Compose

## Admin

* Filament v3

## Quality

* PHPUnit
* GitHub Actions
* RefreshDatabase

---

# Main Features

## Customer

* 商品一覧
* 商品検索
* カテゴリ検索
* 並び替え
* 商品詳細
* カート
* 数量変更
* 注文確認
* 注文完了
* マイページ
* 注文履歴

---

## Administrator

* Dashboard
* 商品管理
* カテゴリ管理
* 注文管理
* ユーザー管理
* 配送情報管理

---

## Quality

* Feature Test
* GitHub Actions
* SQLite Testing
* CIによる自動テスト

---

# Project Goals

このプロジェクトでは、次の点を特に重視しました。

* Laravelらしい実装
* 可読性の高いコード
* 保守しやすい設計
* 品質を維持する仕組み
* 実務を意識した開発フロー

単に動作するアプリケーションではなく、

**「チーム開発でも保守しやすいコードを書くこと」**

を意識して制作しています。

# Architecture

本アプリケーションは、Laravelの標準アーキテクチャをベースに設計しています。

```text
Request
    │
    ▼
Route
    │
    ▼
Controller
    │
    ▼
Model (Business Logic)
    │
    ▼
Database
```

Controllerはリクエスト処理と画面遷移のみを担当し、ビジネスロジックはModelへ集約しています。

---

# Technical Decisions

## Why Query Scope?

商品検索では、

* キーワード検索
* カテゴリ検索
* 並び替え
* 公開商品のみ取得

など複数条件を組み合わせています。

これらをControllerへ記述すると保守が難しくなるため、検索ロジックはModelのQuery Scopeへ集約しました。

```php
Product::query()
    ->published()
    ->keyword($keyword)
    ->category($category)
    ->sort($sort)
    ->paginate(12);
```

### Benefits

* Controllerをシンプルに保てる
* 検索条件の追加が容易
* テストしやすい
* 再利用しやすい

---

## Why Business Logic in Model?

販売可能かどうかの判定はControllerではなくProduct Modelへ実装しています。

```php
$product->isAvailableForSale();
```

在庫数取得

```php
$product->totalStock();
```

売り切れ判定

```php
$product->isSoldOut();
```

### Benefits

販売ロジックを1箇所へ集約することで、

複数画面から同じ判定を利用できます。

---

## Why Eloquent Relationship?

SQLを直接記述する代わりに、

Laravel標準のRelationshipを利用しています。

```text
Category
    │
    └──── Product
              │
              └──── ProductImage
```

注文では

```text
Order
    │
    └──── OrderItem
                 │
                 └──── Product
```

### Benefits

* 可読性向上
* N+1問題を回避しやすい
* Laravelらしい実装

---

## Why Session Cart?

今回はログイン不要でも利用できるECサイトを想定したため、

カートはSessionで管理しています。

### Trade-off

**メリット**

* 実装がシンプル
* ゲスト購入に対応しやすい

**デメリット**

* デバイス間同期はできない

実運用ではRedisやデータベース管理も検討できますが、本ポートフォリオではシンプルさを優先しました。

---

## Why Filament?

管理画面はFilament v3を採用しました。

理由

* Laravelとの親和性
* CRUDを高速に構築できる
* カスタマイズ性が高い

Filamentを利用することで、業務ロジックの実装に集中できる構成としました。

---

# Database Design

主要テーブル

```text
users

categories

products

product_images

orders

order_items
```

注文情報と商品情報を分離することで、

商品の価格変更後も注文時点の情報を保持できる設計としています。

---

# Design Trade-offs

設計時には以下の点を意識しました。

| 選択             | 理由          |
| -------------- | ----------- |
| Session Cart   | 実装のシンプルさを優先 |
| Query Scope    | 検索ロジックの共通化  |
| Eloquent       | 保守性・可読性を重視  |
| Filament       | 管理画面を効率よく構築 |
| SQLite Testing | テスト速度を向上    |

---

# What I Optimized

本プロジェクトでは「動作すること」だけでなく、

* コードの読みやすさ
* 修正しやすさ
* テストしやすさ

を重視しました。

そのため、

* Fat Controllerを避ける
* Laravel標準機能を活用する
* ビジネスロジックを集約する

という設計方針を採用しています。

# Order Flow

注文処理では、データ整合性を維持するために一連の処理をトランザクションとして実行しています。

```text
Product Detail
      │
      ▼
Add to Cart
      │
      ▼
Checkout
      │
      ▼
Create Order
      │
      ▼
Create Order Items
      │
      ▼
Decrease Stock
      │
      ▼
Send Mail
      │
      ▼
Complete
```

---

# Transaction

注文処理では `DB::transaction()` を利用しています。

```php
DB::transaction(function () {

    // 注文作成

    // 注文明細作成

    // 在庫減算

    // メール送信

});
```

## Why?

注文途中でエラーが発生した場合でも、

* 注文だけ保存される
* 在庫だけ減少する

といったデータ不整合を防ぐためです。

データの整合性を優先し、処理全体を1つのトランザクションとして扱っています。

---

# Inventory Management

在庫管理はProduct Modelへ集約しています。

利用している主なメソッド

```php
$product->isAvailableForSale();

$product->isSoldOut();

$product->totalStock();
```

販売可否判定をControllerへ分散させないことで、

複数画面から同じロジックを利用できます。

---

# Quality Assurance

品質維持のため、

Feature Testを実装しています。

現在実装済み

* Authentication
* Product Search
* Cart
* Checkout
* Profile

```text
48 Tests

130 Assertions

All Passed ✅
```

---

# Why Feature Test?

単体テストだけではなく、

実際のHTTPリクエストを通した動作確認を重視しました。

これにより

* Routing
* Authentication
* Session
* Database

まで含めて確認できます。

---

# SQLite Testing

テストではSQLiteを利用しています。

```text
APP_ENV=testing

DB_CONNECTION=sqlite
```

## Why?

* テスト実行速度が速い
* 毎回クリーンなDBで開始できる
* CIとの相性が良い

---

# RefreshDatabase

各テストでは

```php
use RefreshDatabase;
```

を利用しています。

これにより

* テスト同士が影響しない
* 再現性のあるテスト

を実現しています。

---

# Continuous Integration

GitHub Actionsを利用し、

Push・Pull Request時に自動でテストを実行しています。

```text
Checkout Repository

      │

Composer Install

      │

Generate APP_KEY

      │

Migration

      │

PHPUnit

      │

Success
```

## Why?

ローカル環境だけでなく、

GitHub上でもテストを実行することで、

環境差異による問題を早期に発見できるようにしています。

---

# Infrastructure

本アプリケーションはAWS EC2へデプロイしています。

```text
GitHub

    │

GitHub Actions

    │

AWS EC2

    │

Docker

    │

Laravel

    │

MySQL
```

---

# Development Environment

ローカル環境はDocker Composeで構築しています。

主な構成

* PHP 8.3
* Laravel 11
* MySQL
* Node.js
* Vite

環境構築手順を統一することで、

開発環境の再現性を高めています。

---

# Project Highlights

このプロジェクトで特に注力した点

* Laravel標準機能を活用した設計
* Query Scopeによる検索処理の共通化
* Transactionによる注文処理
* Eloquent Relationshipを利用したデータ設計
* PHPUnitによるFeature Test
* GitHub ActionsによるCI
* Dockerを利用した開発環境
* AWS EC2へのデプロイ

単に機能を実装するだけではなく、

「変更しやすく、継続的に改善できる構成」

を意識して設計しました。

# Local Development

## Requirements

* PHP 8.3
* Composer
* Node.js
* Docker
* MySQL

---

## Installation

Clone the repository.

```bash
git clone https://github.com/utl-flaxy/shining-will-shop.git

cd shining-will-shop
```

Install dependencies.

```bash
composer install

npm install
```

Copy the environment file.

```bash
cp .env.example .env
```

Generate the application key.

```bash
php artisan key:generate
```

Run the migrations.

```bash
php artisan migrate
```

Create the storage symlink.

```bash
php artisan storage:link
```

Start the frontend development server.

```bash
npm run dev
```

Launch the application.

```bash
php artisan serve
```

---

# Future Improvements

今後は以下の機能追加を予定しています。

## Payment

* Square決済対応
* Webhook対応
* 決済履歴管理

---

## Shopping Experience

* お気に入り機能
* 商品レビュー
* レコメンド
* クーポン
* ポイント

---

## Infrastructure

* CloudFront
* S3画像配信
* キャッシュ最適化

---

## Application

* PWA対応
* 多言語対応
* 通知機能

---

# Lessons Learned

このプロジェクトを通して、

Laravelの基本機能だけではなく、

実際のWebアプリケーション開発では

設計・品質・運用まで考えることが重要であると学びました。

特に印象的だったのは、

* Controllerへロジックを書きすぎないこと
* データ整合性を保つこと
* 自動テストを書くこと
* CIを整備すること

です。

また、

「まず動くものを作る」

だけではなく、

「変更しやすい設計」

を意識するようになりました。

---

# What I Would Improve

もし本プロジェクトをさらに発展させる場合は、

以下の改善を検討しています。

* Service Layerの導入
* Repository Patternの採用可否検討
* キャッシュ戦略の見直し
* API化への対応
* フロントエンドのSPA化
* パフォーマンス計測と改善

現時点では、Laravel標準機能を活かしたシンプルな構成を優先していますが、プロジェクト規模に応じて設計を見直す余地があると考えています。

---

# Repository Structure

```text
app/
├── Http/
├── Models/
├── Mail/
├── Filament/

database/
├── migrations/
├── seeders/

resources/
├── views/
├── css/
├── js/

routes/

tests/
├── Feature/
└── Unit/

.github/
└── workflows/
```

---

# Key Takeaways

このプロジェクトでは、

Laravelを利用したECサイト開発だけではなく、

* 設計
* テスト
* CI
* インフラ
* 運用

まで含めた一連の開発を経験しました。

特に以下の点を意識しています。

* Laravel標準機能を活用した設計
* 保守性・可読性を重視した実装
* Feature Testによる品質担保
* GitHub Actionsによる継続的インテグレーション
* Dockerによる再現性のある開発環境
* AWS EC2へのデプロイ

---

# About This Portfolio

このポートフォリオは、

「機能数の多さ」ではなく、

**保守しやすく、継続的に改善できるWebアプリケーションを設計・実装すること**

を目標に制作しました。

実務では新機能の追加や仕様変更が継続的に発生するため、

可読性・拡張性・品質を意識した設計を心がけています。

今後も継続的に改善を重ねながら、

より実践的なアプリケーションへ発展させていく予定です。

---

# Contact

GitHub

https://github.com/utl-flaxy

Portfolio

https://shining-will-shop.com

---

# License

This project is published for portfolio and learning purposes.
