# 🚀 Guia Rápido - ReproSys

Comece a usar o sistema em 5 minutos!

## 1️⃣ Instalação Rápida

```bash
# Clonar
git clone https://github.com/filipeive/reprosys.git
cd reprosys

# Instalar
composer install
npm install

# Configurar
cp .env.example .env
php artisan key:generate
php artisan migrate --seed

# Iniciar
npm run dev &
php artisan serve
