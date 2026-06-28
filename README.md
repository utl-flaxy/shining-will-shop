# 🛍️ Shining Will Shop

> Laravel 11で開発した、アイドル・アーティスト向けECサイトです。  
> 商品販売だけではなく、認証・注文・在庫管理・管理画面・自動テスト・CIまで実装し、実務を意識したWebアプリケーションとして設計しました。

---

## デモ

🌐 **本番環境**

https://shining-will-shop.com

---

## GitHub

https://github.com/utl-flaxy/shining-will-shop

---

# プロジェクト概要

Shining Will Shopは、アイドル・アーティスト向けのグッズ販売を想定したECサイトです。

一般的なECサイトの実装だけではなく、

- 商品管理
- カテゴリー管理
- 注文管理
- 在庫管理
- 会員認証
- 管理画面
- 自動テスト
- CI

までを一つのシステムとして構築しています。

本プロジェクトでは、

**「動作すること」ではなく、保守しやすく継続的に改善できる設計**

を重視しています。

---

# 技術スタック

| 分類 | 技術 |
|------|------|
| Backend | Laravel 11 / PHP 8.3 |
| Frontend | Blade / Tailwind CSS / Vite |
| Database | MySQL |
| ORM | Eloquent ORM |
| Authentication | Laravel Breeze |
| Admin Panel | Filament v3 |
| Testing | PHPUnit |
| CI | GitHub Actions |
| Development | Docker |
| Infrastructure | AWS EC2 |

---

# システム全体像

このプロジェクトは、Laravelを中心にAWS上へデプロイし、GitHub Actionsによる継続的インテグレーションを行う構成としています。

システム全体像は、以下の3つの図を見ることで把握できます。

---

# AWS構成図

> flowchart LR

    User[👤 ユーザー]

    GitHub[GitHub Repository]

    Actions[GitHub Actions]

    EC2[AWS EC2]

    Docker[Docker]

    Laravel[Laravel 11]

    MySQL[(MySQL)]

    User --> EC2

    GitHub --> Actions

    Actions --> EC2

    EC2 --> Docker

    Docker --> Laravel

    Laravel --> MySQL

### 構成概要

- GitHubでソースコードを管理
- GitHub Actionsで自動テストを実行
- AWS EC2へデプロイ
- Docker上でLaravel・MySQLを動作
- Viteでフロントエンドをビルド

---

# ER図

> erDiagram

    USERS ||--o{ ORDERS : places

    CATEGORIES ||--o{ PRODUCTS : has

    PRODUCTS ||--o{ PRODUCT_IMAGES : has

    PRODUCTS ||--o{ ORDER_ITEMS : ordered

    ORDERS ||--|{ ORDER_ITEMS : contains

    USERS {

        bigint id

        string name

        string email

    }

    CATEGORIES {

        bigint id

        string name

    }

    PRODUCTS {

        bigint id

        bigint category_id

        string name

        integer price

        integer stock

    }

    PRODUCT_IMAGES {

        bigint id

        bigint product_id

        string image_path

    }

    ORDERS {

        bigint id

        bigint user_id

        string order_number

        integer total_amount

        string status

    }

    ORDER_ITEMS {

        bigint id

        bigint order_id

        bigint product_id

        integer quantity

        integer unit_price

    }

### 主なテーブル

- users
- categories
- products
- product_images
- orders
- order_items

商品・注文・ユーザーの関係を正規化し、注文時の商品情報を保持できる構成としています。

---

# 注文フロー図

> **（ここに注文フロー図を配置）**

### 注文処理

1. 商品をカートへ追加
2. 注文内容を確認
3. 注文情報を保存
4. 注文明細を保存
5. 在庫を減算
6. 注文完了メールを送信
7. 注文完了画面を表示

注文処理はデータ整合性を保つため、トランザクションを利用して実装しています。

---

# このプロジェクトで重視したこと

このプロジェクトでは、機能数を増やすことよりも、以下の4点を重視しました。

- 保守しやすい設計
- Laravel標準機能を活用した実装
- 品質を維持するための自動テスト
- 継続的に改善できる開発体制

また、「実装方法」だけではなく、

**「なぜその設計を選択したのか」**

を重視して開発しています。

---

# READMEの構成

このREADMEでは、実装した機能を紹介するだけではなく、設計上の意思決定や品質への取り組みについても説明します。

1. 開発背景
2. 技術選定
3. アーキテクチャ
4. 設計レビュー
5. 実装機能
6. 品質保証
7. 今後の改善
8. セットアップ

コードだけでは伝わらない設計意図やトレードオフについても記載しています。

