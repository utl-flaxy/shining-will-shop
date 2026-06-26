## ローカルで Stripe webhook を受ける手順（開発用）

1. 環境変数をセット (.env)
   ENABLE_LOCAL_WEBHOOK_UNTHROTTLED=true

2. ローカル proxy を起動（プロジェクトにある docker-compose.proxy.yml を使用）
   docker compose -f compose.yaml -f docker-compose.proxy.yml -f docker-compose.override.yml up -d --force-recreate --no-deps proxy

3. Stripe CLI で転送
   stripe listen --forward-to http://shining-will-shop.com:8000/stripe/webhook-unthrottled
   stripe trigger checkout.session.completed

注意: このフラグは開発専用です。production では .env に true を入れないでください。
