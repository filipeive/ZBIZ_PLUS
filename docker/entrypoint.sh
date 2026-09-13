#!/bin/bash
set -e

echo "🚀 Iniciando ZBIZ+ Container..."

# Garantir permissões de storage e cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Se não existir .env, copiar do .env.example
if [ ! -f /var/www/html/.env ]; then
    echo "📋 .env não encontrado, criando a partir de .env.example..."
    cp /var/www/html/.env.example /var/www/html/.env
fi

# Gerar APP_KEY se estiver vazio
if ! grep -q "^APP_KEY=base64:" /var/www/html/.env; then
    echo "🔑 Gerando nova APP_KEY..."
    php artisan key:generate --force
fi

# Aguardar conexão com o banco de dados (se não for SQLite)
if [ "${DB_CONNECTION:-mysql}" != "sqlite" ] && [ -n "${DB_HOST}" ]; then
    echo "⏳ Aguardando banco de dados em ${DB_HOST}:${DB_PORT:-3306}..."
    until php -r "
        \$host = '${DB_HOST}';
        \$port = (int)('${DB_PORT:-3306}');
        \$fp = @fsockopen(\$host, \$port, \$errno, \$errstr, 2);
        if (\$fp) { fclose(\$fp); exit(0); }
        exit(1);
    "; do
        sleep 2
    done
    echo "✅ Conexão com banco de dados estabelecida!"
fi

# Executar migrações
echo "🗃️ Executando migrations do banco de dados..."
php artisan migrate --force || echo "⚠️ Atenção: Falha na migração automática ou banco já atualizado."

# Criar link simbólico do storage se necessário
if [ ! -L /var/www/html/public/storage ]; then
    echo "🔗 Criando symlink do storage..."
    php artisan storage:link || true
fi

# Otimizar caches se em produção
if [ "${APP_ENV:-production}" = "production" ]; then
    echo "⚡ Otimizando caches da aplicação..."
    php artisan optimize:clear
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

echo "🟢 ZBIZ+ pronto para conexões na porta 80!"

exec "$@"
