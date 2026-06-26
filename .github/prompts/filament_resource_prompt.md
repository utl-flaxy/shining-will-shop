# Filament Resource 自動生成ルール

目的：Shining-Will-Shop を完全ノーコード運用できる管理画面にする

必須要件：
1) データ構造は /docs/spec.md 準拠（テーブル・カラム名は厳守）
2) 各Resourceに RelationManager を設定し、関連をGUIで横断できること
3) OrderResource には状態変更アクション（入金確認・発送完了・返金）を用意
4) ExportAction で「注文一覧」「在庫一覧」をExcel出力可能にする
5) 月次売上は ChartWidget で可視化
6) Stripe 決済（カード）および Refund API（返金）対応
7) 期間外購入は不可（販売期間バリデーション）
8) メール通知：注文完了・発送完了
