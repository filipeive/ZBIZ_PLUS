#!/bin/bash

set -euo pipefail

# ==============================================================================
# Script de Deploy de Produção - ZBIZ+
# Desenvolvido por Fdsmultiservices
# Suporte: (+258) 86 213 4230 | fdsmultiservices@gmail.com
# ==============================================================================

SERVER="${SERVER:-ubuntu@146.235.224.99}"
KEY="${KEY:-/home/fdev-ms/.ssh/oracle-2025}"
PROJECT_DIR="${PROJECT_DIR:-/var/www/html/zbiz_plus}"
BRANCH="${BRANCH:-main}"

echo "🔍 Verificando estado do repositório local..."

LOCAL_HEAD="$(git rev-parse HEAD)"
REMOTE_HEAD="$(git ls-remote origin -h "refs/heads/$BRANCH" | awk '{print $1}')"

if [[ -z "$REMOTE_HEAD" ]]; then
    echo "❌ Erro: não foi possível obter a branch origin/$BRANCH."
    exit 1
fi

if [[ "$LOCAL_HEAD" != "$REMOTE_HEAD" ]]; then
    echo "⚠️ Atenção: o commit local ($LOCAL_HEAD) difere de origin/$BRANCH ($REMOTE_HEAD)."
    echo "Certifique-se de executar 'git push origin $BRANCH' antes de fazer o deploy."
    exit 1
fi

echo "✅ Commit confirmado em origin/$BRANCH ($LOCAL_HEAD)."
echo "🚀 Iniciando deploy do ZBIZ+ para o servidor de produção ($SERVER)..."

ssh -i "$KEY" "$SERVER" "cd $PROJECT_DIR && \
    echo '🔧 Ajustando permissões de trabalho...' && \
    sudo chown -R ubuntu:ubuntu .git && \
    echo '📥 Sincronizando código-fonte (git fetch & reset)...' && \
    git fetch origin $BRANCH && \
    git reset --hard origin/$BRANCH && \
    echo '📦 Instalando dependências PHP otimizadas...' && \
    composer install --optimize-autoloader --no-dev --no-interaction && \
    echo '🗃️ Executando migrações de base de dados...' && \
    php artisan migrate --force && \
    echo '🧹 Limpando caches obsoletos...' && \
    php artisan optimize:clear && \
    echo '⚡ Regerando caches de produção (config, route, view)...' && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    echo '🔒 Protegendo permissões de storage e bootstrap/cache...' && \
    sudo chown -R ubuntu:www-data storage bootstrap/cache && \
    sudo chmod -R 775 storage bootstrap/cache && \
    echo '🔄 Recarregando PHP-FPM e Nginx...' && \
    sudo systemctl reload php8.3-fpm nginx && \
    echo '🎉 Deploy do ZBIZ+ concluído com sucesso!'"
