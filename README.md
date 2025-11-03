# Shining Will Shop

This repository now has idempotent DatabaseSeeder changes in chore/idempotent-seeders.

## Local Stripe Webhook Testing

To test Stripe webhooks locally using the Stripe CLI:

1. **Enable the unthrottled webhook route** by setting the environment variable in `.env`:
   ```
   ENABLE_LOCAL_WEBHOOK_UNTHROTTLED=true
   ```

2. **Start the proxy** to access the app via HTTP (required for Stripe CLI):
   ```bash
   docker compose -f compose.yaml -f docker-compose.proxy.yml -f docker-compose.override.yml up -d --force-recreate --no-deps proxy
   ```

3. **Forward Stripe events** to your local webhook endpoint:
   ```bash
   stripe listen --forward-to http://shining-will-shop.com:8000/stripe/webhook-unthrottled
   ```

4. **Trigger test events** from Stripe CLI:
   ```bash
   stripe trigger checkout.session.completed
   ```

5. **Verify** the webhook was processed by checking:
   - Application logs
   - `processed_stripe_events` table in the database

**⚠️ Important**: This unthrottled webhook route is for development only. Do NOT set `ENABLE_LOCAL_WEBHOOK_UNTHROTTLED=true` in production or CI environments.
