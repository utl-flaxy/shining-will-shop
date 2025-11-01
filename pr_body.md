English:
Use firstOrCreate for users and products, and use "title" for products to match the current schema.
This makes php artisan db:seed safe to run repeatedly in development without creating duplicate records.

日本語:
users と products のシーダーを firstOrCreate にして再実行可能（冪等）にし、
products のカラム名を現在のスキーマに合わせて title を使うよう修正しました。
開発環境で何度 db:seed を実行しても重複やエラーが発生しないようにする変更です。

Changes:
- database/seeders/DatabaseSeeder.php: switch to firstOrCreate for admin/test users and product seeding (sku: TEST-001)
- README.md: note about the change (added to create a visible diff for this PR)

Testing steps:
1. Run seeder:
   - ./vendor/bin/sail exec laravel.test php artisan db:seed
   - or locally: php artisan db:seed
2. Verify users exist:
   - SELECT email FROM users WHERE email IN ('admin@example.com','test@example.com');
3. Verify product exists:
   - SELECT sku,title,price FROM products WHERE sku = 'TEST-001';
4. Re-run db:seed and confirm:
   - No duplicate users or products are created; counts remain stable.

Review checklist:
- [ ] db:seed を何度実行しても重複レコードが作成されないこと
- [ ] products テーブルに title カラムが存在し、正しく使用されていること
- [ ] スキーマを変更しない（マイグレーション不要）こと
- [ ] シーダーの意図が README またはコメントで分かること
