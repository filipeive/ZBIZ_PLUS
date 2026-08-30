#!/bin/bash

set -euo pipefail

# Deploy script para o servidor de produção.
# Este script assume que o commit já foi enviado ao remoto.

SERVER="${SERVER:-ubuntu@146.235.224.99}"
KEY="${KEY:-/home/fdev-ms/.ssh/oracle-2025}"
PROJECT_DIR="${PROJECT_DIR:-/var/www/html/reprosys}"
BRANCH="${BRANCH:-main}"

echo "Verificando estado do repositório local..."

LOCAL_HEAD="$(git rev-parse HEAD)"
REMOTE_HEAD="$(git ls-remote origin -h "refs/heads/$BRANCH" | awk '{print $1}')"

if [[ -z "$REMOTE_HEAD" ]]; then
    echo "Erro: não foi possível obter a branch origin/$BRANCH."
    exit 1
fi

if [[ "$LOCAL_HEAD" != "$REMOTE_HEAD" ]]; then
    echo "Erro: o commit local ainda não está em origin/$BRANCH."
    echo "Faça primeiro: git push origin $BRANCH"
    exit 1
fi

echo "Commit confirmado em origin/$BRANCH."
echo "🚀 Iniciando deploy para produção..."

ssh -i "$KEY" "$SERVER" "cd $PROJECT_DIR && \
    echo '🔧 Corrigindo permissões do git...' && \
    sudo chown -R ubuntu:ubuntu .git && \
    echo '📥 Atualizando código (force sync)...' && \
    git fetch origin $BRANCH && \
    git reset --hard origin/$BRANCH && \
    echo '📦 Instalando dependências...' && \
    composer install --optimize-autoloader --no-dev --no-interaction && \
    echo '🧱 Criando tabelas de suporte legadas faltantes...' && \
    php artisan migrate --path=database/migrations/2026_04_15_123000_create_missing_framework_support_tables.php --force && \
    echo '🧭 Sincronizando baseline legado de migrations...' && \
    php artisan migrations:sync-legacy --write && \
    echo '🗃️ Executando migrations...' && \
    php artisan migrate --force && \
    echo '🧹 Limpando caches antigos...' && \
    php artisan optimize:clear && \
    echo '⚡ Regerando caches...' && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    echo '✅ Deploy concluído com sucesso!'"
