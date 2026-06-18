# 🛍️ Shining Will Shop

Laravel + Filament によるアイドルグッズ販売ECサイト

---

## ✅ このプロジェクトで証明できること

- ECドメインにおける業務ロジック設計（在庫 / 販売期間 / 商品状態）
- Laravelを用いた実務レベルのバックエンド設計
- Filamentによる管理画面構築
- 商品状態に応じた表示・購入制御
- データと状態を組み合わせた業務フロー設計

---

## 🎯 開発背景

ECサイト開発では以下の課題があります：

- 商品の販売期間管理が複雑
- 在庫切れや販売状態の整合性管理が必要
- 表示状態と購入可能状態の制御が分離されがち

これらを解決するため、

「商品状態 × 販売期間 × 在庫」

を統合して管理できるECシステムを設計・開発しました。

---

## 🧠 コア設計

### ■ 商品状態管理

商品は以下の状態を持ちます：

```text
掲載前 → 販売前 → 販売中 → 販売終了
状態に応じて：

表示可否
購入可否

を制御しています。

■ 購入可能判定ロジック

✅ 判定条件
表示中
↓
販売ON
↓
販売期間内
↓
在庫あり

＝購入可能
👉 複数条件を統合し、業務要件をコードで表現しています。

■ 在庫管理

商品ごとではなく「バリアント単位」で在庫管理
合計在庫を動的集計
public function totalStock(): int
{
    return (int) $this->variants()->sum('stock');
}

■ 販売期間制御
publish_start_at  → 掲載開始
sale_start_at     → 販売開始
sale_end_at       → 販売終了
sale_end_at       → 販売終了
👉 時間を軸にシステム挙動を制御

🛠 技術スタック





































分類技術BackendLaravel 11AdminFilament v3FrontBlade / TailwindCSSDBMySQLWeb ServerNginxOSUbuntuVersion ControlGit / GitHub

⭐ 主な機能
商品管理

商品CRUD
SKU管理
カテゴリ管理
商品画像管理（複数・並び替え）


在庫管理

バリアント単位在庫
在庫合計自動計算
SOLD OUT判定


販売管理

掲載開始日時
販売開始日時
販売終了日時
商品状態ラベル表示


管理画面

Filamentによる管理UI
商品・在庫管理

🖼️ システム構成
Internet
   ↓
Nginx
   ↓
Laravel
   ↓
MySQL

📊 ER図
erDiagram

categories ||--o{ products : has
products ||--o{ product_images : has
products ||--o{ product_variants : has

categories {
    bigint id
    string name
}

products {
    bigint id
    bigint category_id
    string name
    int price
    boolean is_published
    boolean is_active
    datetime publish_start_at
    datetime sale_start_at
    datetime sale_end_at
}

product_variants {
    bigint id
    bigint product_id
    string member_name
    int stock
}

👨‍💻 工夫したポイント
✅ 状態 × データ × 時間の統合

商品状態
販売期間
在庫状態

を組み合わせた業務ロジック設計

✅ ドメインロジックをModelに集約

isAvailableForSale
isSoldOut
saleStatusLabel

👉 業務知識をコードに反映

✅ 管理画面構築
Filamentにより、直感的な商品・在庫管理を実現

🚧 現在の実装状況





































機能状態商品管理✅在庫管理✅販売期間管理✅商品状態判定✅カート未実装注文未実装決済未実装

🚀 今後の改善

カート機能
注文管理
Stripe決済
FanClub会員連携
AWS構成（S3 / CloudFront / RDS）


🔗 GitHub
https://github.com/your-repo-url


📝 まとめ
本プロジェクトでは、

在庫
販売期間
商品状態

を組み合わせた 業務ロジック設計 を実装しました。
単なるCRUDではなく、
「条件に応じてシステムの振る舞いを制御する設計」
を意識した、実務志向のECシステムです。

🙏 ご覧いただきありがとうございました
