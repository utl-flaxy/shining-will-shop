# Shining-Will-Shop 要件仕様書（正式版）

本書は、AIコード生成システム（GitHub Actions / Copilot / ChatGPT など）が  
本プロジェクトの構造・仕様・命名規則を正確に理解するための指針とする。

---

## 🧩 プロジェクト概要
アイドルグッズ販売プラットフォーム「Shining-Will-Shop」は、  
**管理者が完全ノーコード（GUI操作）で運用できる** Laravel + Filament ベースのECシステムである。  
ユーザー側は Blade ベースで構成し、スマートフォンでも快適に操作可能な軽量UIとする。

---

## 🔐 管理者サイト（Admin Panel / Filament）

### 目的
- コード操作不要で、全ての販売・在庫・入金・発送・返金をGUI上で完結できる。
- Excel出力・集計・メール送信も Filament 内で可能とする。

### 主な機能

#### 1. 認証
- 管理者ログイン機能（Filament Auth）
- ロールは `admin` のみを想定（将来的に拡張可）

#### 2. 決済方法
- **クレジットカード**：Stripe API で即時決済  
- **銀行振込**：手動で入金確認ボタン押下  
- **現場払い**：イベント現場で支払い、管理者がステータス変更

#### 3. 商品管理
- 販売開始日時・終了日時を設定可能  
- 在庫数を設定／未設定を選択可能  
- メンバー（タレント）ごとに在庫を別管理  
- 商品画像・価格・説明を登録可能  

#### 4. 在庫構造
- 商品は `products` テーブル  
- メンバー別在庫は `product_variants` テーブルで管理  

#### 5. 注文管理
注文はステータスで一元管理。  
Filament では「タブ切り替え」または「フィルター」で分類。

| ステータス名 | 説明 |
|---------------|------|
| `pending_payment` | 入金待ち（銀行振込など） |
| `paid` | 入金完了（カード or 手動確認済み） |
| `awaiting_shipment` | 発送待ち |
| `shipped` | 発送完了（発送メール送信済） |
| `refunded` | 返金済（未発送のみ許可） |
| `cancelled` | キャンセル済 |

#### 6. 売上集計
- 月ごと・過去月の売上合計を自動集計  
- Filament ChartWidgetでグラフ表示  
- 税込金額・手数料を自動計算（Stripe手数料など）

#### 7. 入金予定金額
- 各ステータスから入金予定を自動計算  
- ステータス`pending_payment` + 支払方法`bank_transfer`を集計対象とする

#### 8. 返金処理
- 未発送(`awaiting_shipment`)注文のみ返金可  
- 支払方法が `card` の場合、Stripe Refund APIで自動返金  
- それ以外は手動でステータス変更＋メモ登録

#### 9. データ出力
Excel 出力対応項目：
- 商品名  
- 注文個数  
- 購入日時  
- 顧客名  
- 郵便番号  
- 住所  
- 備考欄  
- 支払方法  
- ステータス  

🔸 `products` テーブルも「在庫一覧Excel出力」に対応すること。

---

## 🛒 ユーザーサイト（User Front / Blade）

### 目的
- タレント別に商品を表示し、シンプルかつ直感的に購入できる。
- `stores.jp` 風のUIを参考にする。

### 主なページ構成

| ページ | 説明 |
|--------|------|
| **ホーム画面** | タレント（カテゴリー）を表示 |
| **カテゴリー画面** | 選択したタレントの商品を一覧表示 |
| **商品詳細画面** | 商品画像・説明・在庫・価格を表示し「カートに追加」ボタンを設置 |
| **カート画面** | 商品名・画像・個数・金額・合計金額を表示 |
| **決済画面** | 支払方法（カード／口座振込／現場払い）選択 |
| **注文完了画面** | 注文番号とメッセージ表示、メール送信あり |

---

## 🧱 データモデル（ER定義）

### `users`
| カラム | 型 | 説明 |
|--------|----|------|
| id | bigint | 主キー |
| name | string | 氏名 |
| email | string | メールアドレス |
| password | string | パスワード |
| address | text | 住所 |
| postal_code | string | 郵便番号 |
| phone | string | 電話番号（任意） |

---

### `products`
| カラム | 型 | 説明 |
|--------|----|------|
| id | bigint | 主キー |
| name | string | 商品名 |
| description | text | 説明文 |
| price | integer | 価格 |
| start_at | datetime | 販売開始日時 |
| end_at | datetime | 販売終了日時 |
| image | string | 商品画像URL |
| stock_enabled | boolean | 在庫管理ON/OFF |

---

### `product_variants`
| カラム | 型 | 説明 |
|--------|----|------|
| id | bigint | 主キー |
| product_id | bigint (FK) | 商品ID |
| member_name | string | タレント名 |
| stock | integer | 在庫数 |

---

### `orders`
| カラム | 型 | 説明 |
|--------|----|------|
| id | bigint | 主キー |
| user_id | bigint (FK) | 購入者 |
| status | enum | 注文ステータス |
| total_amount | integer | 合計金額 |
| payment_method | enum(`card`, `bank_transfer`, `on_site`) | 支払方法 |
| paid_at | datetime | 入金日時 |
| shipped_at | datetime | 発送日時 |
| refunded_at | datetime | 返金日時 |
| memo | text | 備考・メモ |

---

### `order_items`
| カラム | 型 | 説明 |
|--------|----|------|
| id | bigint | 主キー |
| order_id | bigint (FK) | 注文ID |
| product_id | bigint (FK) | 商品ID |
| variant_id | bigint (FK) | メンバー別在庫ID |
| quantity | integer | 数量 |
| price | integer | 単価 |

---

### `payments`
| カラム | 型 | 説明 |
|--------|----|------|
| id | bigint | 主キー |
| order_id | bigint (FK) | 注文ID |
| payment_method | enum(`card`, `bank_transfer`, `on_site`) | 支払方法 |
| amount | integer | 支払金額 |
| transaction_id | string | StripeトランザクションIDなど |
| status | enum(`pending`, `completed`, `refunded`) | 支払ステータス |
| processed_at | datetime | 処理日時 |

---

## ⚙️ AI生成時のルール
1. **命名規則**：上記テーブル・カラムを正確に使用すること。  
2. **モデル名・Resource名**：単数形（例：`Product`, `Order`）で作成。  
3. **リレーション定義**：  
   - `User` → `hasMany(Order)`  
   - `Order` → `hasMany(OrderItem)`  
   - `OrderItem` → `belongsTo(ProductVariant)`  
   - `Product` → `hasMany(ProductVariant)`  
4. **Filament Resource構成**：  
   - 全モデルをResource化する  
   - 各Resourceに RelationManager を設定する  
   - `OrderResource` にステータス更新ボタン（入金確認・発送完了・返金）を配置  
   - `ExportAction` を導入しExcel出力対応  
   - 売上は ChartWidget で月次グラフ表示  
5. **Stripe Refund**：`refunded` ステータス時は自動API連携する  
6. **バリデーション**：販売期間内のみ購入可  
7. **メール通知**：注文完了・発送完了時に送信  

---

## 🧾 バージョン管理
- Laravel 11.x  
- PHP 8.2+  
- MySQL 8.x  
- Filament 3.x  
- Stripe PHP SDK 最新版  

---

## ✅ 補足
この仕様は AI 自動コード生成・テスト・修正の基準ドキュメントであり、  
開発工程中も変更が発生した場合は常にこのファイルを最優先として参照する。

---
