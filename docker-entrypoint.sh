#!/bin/bash
set -e

echo "==> Criando diretórios necessários..."
mkdir -p bootstrap/cache
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
chmod -R 775 bootstrap/cache storage

echo "==> Instalando dependências..."
composer install --no-interaction --optimize-autoloader --no-dev

echo "==> Configurando ambiente..."
if [ ! -f .env ]; then
    cp .env.example .env
fi

php artisan key:generate --force

echo "==> Subindo servidor Laravel em http://0.0.0.0:8000 ..."
exec php artisan serve --host=0.0.0.0 --port=8000
