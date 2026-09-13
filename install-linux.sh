#!/usr/bin/env bash

# ==============================================================================
# ZBIZ+ Enterprise Cloud & POS Suite v1.0.19
# Instalador e Configurador de Terminal Kiosk / Desktop para Linux
# Desenvolvido por Fdsmultiservices
# Suporte WhatsApp: (+258) 86 213 4230 | Email: fdsmultiservices@gmail.com
# ==============================================================================

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ICON_PATH="${SCRIPT_DIR}/public/favicon.png"
DESKTOP_DIR="${HOME}/Desktop"
[ -d "${HOME}/Área de Trabalho" ] && DESKTOP_DIR="${HOME}/Área de Trabalho"
APP_DIR="${HOME}/.local/share/applications"
AUTOSTART_DIR="${HOME}/.config/autostart"

mkdir -p "${APP_DIR}" "${AUTOSTART_DIR}" "${DESKTOP_DIR}"

clear
echo "=============================================================================="
echo "       ZBIZ+ ENTERPRISE CLOUD & POS SUITE - MOÇAMBIQUE"
echo "       Instalador de Terminal de Venda / Farmácia / Restauração (Linux)"
echo "       Desenvolvido por: Fdsmultiservices"
echo "       Suporte Técnico: (+258) 86 213 4230 | fdsmultiservices@gmail.com"
echo "=============================================================================="
echo ""
echo "Escolha a opção de instalação:"
echo ""
echo "  [1] Configurar Terminal POS Kiosk (Modo App Nativo para Balcão/Caixa)"
echo "      - Recomendado para operadores de caixa em Farmácias e Lojas."
echo "      - Cria lançador no Desktop sem barra de navegador ou abas."
echo "      - Ativa suporte a impressão térmica automática (--kiosk-printing)."
echo ""
echo "  [2] Instalar Servidor Local Completo (Modo Offline / On-Premise)"
echo "      - Instala dependências PHP 8.3, banco de dados e serviço contínuo de segundo plano."
echo ""
echo "  [3] Abrir ZBIZ+ Nuvem imediatamente no navegador"
echo "  [0] Sair"
echo ""
read -rp "Digite a opção desejada [1, 2, 3 ou 0]: " OPTION

case "$OPTION" in
    1)
        echo ""
        echo "--- Configuração do Terminal POS Kiosk ---"
        read -rp "Informe a URL do ZBIZ+ [ENTER para Nuvem: http://146.235.224.99/zbiz_plus]: " TARGET_URL
        TARGET_URL="${TARGET_URL:-http://146.235.224.99/zbiz_plus}"

        echo ""
        echo "Formato de exibição:"
        echo "  1) Modo App Nativo (Janela limpa, barra de título compacta, redimensionável) [Padrão]"
        echo "  2) Modo Kiosk Total (Tela inteira travada para postos de caixa dedicados)"
        read -rp "Escolha [1 ou 2]: " MODE_CHOICE
        MODE_CHOICE="${MODE_CHOICE:-1}"

        echo ""
        read -rp "Deseja iniciar o ZBIZ+ automaticamente ao ligar o computador? [S/n]: " AUTOSTART_CHOICE
        AUTOSTART_CHOICE="${AUTOSTART_CHOICE:-S}"

        # Detectar navegador
        BROWSER=""
        if command -v google-chrome &>/dev/null; then
            BROWSER="google-chrome"
        elif command -v chromium-browser &>/dev/null; then
            BROWSER="chromium-browser"
        elif command -v chromium &>/dev/null; then
            BROWSER="chromium"
        elif command -v brave-browser &>/dev/null; then
            BROWSER="brave-browser"
        elif command -v msedge &>/dev/null; then
            BROWSER="msedge"
        elif command -v firefox &>/dev/null; then
            BROWSER="firefox"
        fi

        if [ -z "$BROWSER" ]; then
            echo "❌ Nenhum navegador compatível encontrado (Chrome/Chromium/Firefox)."
            exit 1
        fi

        echo "🔍 Navegador detectado: ${BROWSER}"

        if [ "$MODE_CHOICE" = "2" ]; then
            EXEC_CMD="${BROWSER} --kiosk ${TARGET_URL} --kiosk-printing --no-first-run --disable-pinch"
        else
            if [ "$BROWSER" = "firefox" ]; then
                EXEC_CMD="firefox --kiosk ${TARGET_URL}"
            else
                EXEC_CMD="${BROWSER} --app=${TARGET_URL} --no-first-run"
            fi
        fi

        DESKTOP_ENTRY="[Desktop Entry]
Version=1.0
Type=Application
Name=ZBIZ+ Terminal POS
Comment=Terminal de Venda e Gestão Empresarial ZBIZ+
Exec=${EXEC_CMD}
Icon=${ICON_PATH}
Terminal=false
StartupNotify=true
Categories=Office;Finance;PointOfSale;
"

        # Criar lançador no menu de aplicativos e na área de trabalho
        echo "$DESKTOP_ENTRY" > "${APP_DIR}/zbiz-plus.desktop"
        echo "$DESKTOP_ENTRY" > "${DESKTOP_DIR}/ZBIZ+ Terminal POS.desktop"
        chmod +x "${APP_DIR}/zbiz-plus.desktop"
        chmod +x "${DESKTOP_DIR}/ZBIZ+ Terminal POS.desktop"

        # Tentar confiar no atalho em ambientes GNOME/Ubuntu
        if command -v gio &>/dev/null; then
            gio set "${DESKTOP_DIR}/ZBIZ+ Terminal POS.desktop" metadata::trusted true 2>/dev/null || true
        fi

        # Autostart
        if [[ "$AUTOSTART_CHOICE" =~ ^[Ss]$ ]]; then
            echo "$DESKTOP_ENTRY" > "${AUTOSTART_DIR}/zbiz-plus.desktop"
            chmod +x "${AUTOSTART_DIR}/zbiz-plus.desktop"
            echo "✅ Inicialização automática configurada em ${AUTOSTART_DIR}!"
        fi

        echo ""
        echo "=============================================================================="
        echo "✅ Terminal ZBIZ+ instalado com sucesso!"
        echo "   Ícone disponível na Área de Trabalho: 'ZBIZ+ Terminal POS'"
        echo "   URL: ${TARGET_URL}"
        echo "=============================================================================="
        echo ""
        read -rp "Deseja abrir o Terminal ZBIZ+ agora? [S/n]: " OPEN_NOW
        OPEN_NOW="${OPEN_NOW:-S}"
        if [[ "$OPEN_NOW" =~ ^[Ss]$ ]]; then
            ${EXEC_CMD} &
        fi
        ;;

    2)
        echo ""
        echo "--- Instalação de Servidor Local ZBIZ+ ---"
        echo "Verificando dependências locais..."
        
        if command -v docker &>/dev/null && command -v docker-compose &>/dev/null || docker compose version &>/dev/null; then
            echo "🐳 Docker detectado! Deseja subir via Docker Compose? [S/n]: "
            read -rp "" DOCKER_CONFIRM
            DOCKER_CONFIRM="${DOCKER_CONFIRM:-S}"
            if [[ "$DOCKER_CONFIRM" =~ ^[Ss]$ ]]; then
                docker compose up -d
                echo "✅ Containers iniciados! Aceda a: http://localhost:8000"
                exit 0
            fi
        fi

        echo "Configurando ambiente PHP local..."
        cd "${SCRIPT_DIR}"
        [ ! -f .env ] && cp .env.example .env
        php artisan key:generate --force
        php artisan migrate --force
        php artisan db:seed --class=PlanSeeder --force || true

        # Criar serviço systemd do utilizador para manter servidor rodando
        SERVICE_DIR="${HOME}/.config/systemd/user"
        mkdir -p "${SERVICE_DIR}"
        cat << EOF > "${SERVICE_DIR}/zbizplus.service"
[Unit]
Description=ZBIZ+ Local Server Daemon
After=network.target

[Service]
Type=simple
WorkingDirectory=${SCRIPT_DIR}
ExecStart=$(which php) artisan serve --host=127.0.0.1 --port=8000
Restart=always
RestartSec=5

[Install]
WantedBy=default.target
EOF

        if command -v systemctl &>/dev/null; then
            systemctl --user daemon-reload
            systemctl --user enable zbizplus.service
            systemctl --user restart zbizplus.service
            echo "✅ Serviço local ativado via systemd (porta 8000)!"
        else
            nohup php artisan serve --host=127.0.0.1 --port=8000 > /dev/null 2>&1 &
            echo "✅ Servidor local iniciado em segundo plano na porta 8000!"
        fi

        echo "Para configurar o terminal de acesso, execute este script novamente e escolha a opção [1]."
        ;;

    3)
        xdg-open "http://146.235.224.99/zbiz_plus" 2>/dev/null || echo "Abra no navegador: http://146.235.224.99/zbiz_plus"
        ;;

    *)
        echo "Encerrado."
        exit 0
        ;;
esac
