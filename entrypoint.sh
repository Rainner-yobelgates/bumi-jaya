#!/bin/sh

mkdir -p resources/svg

# Login ke Infisical dan ambil token
export INFISICAL_TOKEN=$(infisical login \
  --method=universal-auth \
  --client-id=$INFISICAL_MACHINE_CLIENT_ID \
  --client-secret=$INFISICAL_MACHINE_CLIENT_SECRET \
  --domain=$INFISICAL_DOMAIN \
  --plain --silent)


eval $(infisical export \
  --token $INFISICAL_TOKEN \
  --projectId $INFISICAL_PROJECT_ID \
  --env $INFISICAL_SECRET_ENV \
  --domain $INFISICAL_DOMAIN \
  --format dotenv | sed 's/^/export /')


# Jalankan perintah Laravel
php artisan optimize:clear
php artisan migrate --force
php artisan storage:link
php artisan optimize
php artisan filament:optimize

php artisan vendor:publish --force --tag=livewire:assets

# Start aplikasi
php artisan octane:frankenphp
