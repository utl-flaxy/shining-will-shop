#!/usr/bin/env bash
# Laravel Sail setup script for local environment
set -euo pipefail

echo "🚀 Installing Laravel Sail..."
composer require laravel/sail --dev --no-interaction

echo "🛠️  Installing Sail services (mysql, redis, mailpit)..."
php artisan sail:install --with=mysql,redis,mailpit

echo "📦  Building Docker containers..."
./vendor/bin/sail build

echo "✅  Starting containers..."
./vendor/bin/sail up -d

echo "🧩  Copying .env.local to .env..."
cp -n .env.local .env || echo ".env.local not found or already copied."

echo "🔑  Generating app key..."
./vendor/bin/sail artisan key:generate

echo "🧱  Migrating and seeding database..."
./vendor/bin/sail artisan migrate --seed

echo "👤  Creating default test user (test@example.com / password)..."
./vendor/bin/sail artisan tinker --execute="\App\Models\User::factory()->create(['email'=>'test@example.com','password'=>bcrypt('password')]);"

echo "🎉  Sail setup complete!"
