#!/bin/bash

# Parar o script se houver erro
set -e

echo "Iniciando deploy..."

# 1. Atualizar o repositório
BRANCH=$(git rev-parse --abbrev-ref HEAD)
echo "Branch detectada: $BRANCH"
echo "Baixando atualizações do git..."
git pull origin $BRANCH

# 2. Instalar dependências do PHP
echo "Instalando dependências do Composer..."
composer install --no-dev --optimize-autoloader

# 3. Rodar migrações
echo "Executando migrações..."
php artisan migrate --force

# 4. Limpar e atualizar caches
echo "Limpando caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# 5. Instalar dependências JS e compilar assets (opcional, se rodar no servidor)
# Descomente as linhas abaixo se o build for feito no servidor
# echo "Compilando assets..."
# npm install
# npm run build

# 6. Permissões (ajustar conforme necessidade do servidor, ex: www-data)
# chown -R www-data:www-data storage bootstrap/cache

echo "Deploy concluído com sucesso!"
