#!/usr/bin/env bash

# ==============================================================================
# ZBIZ+ Enterprise Cloud & POS Suite
# Desinstalador de Terminal Kiosk / Desktop e Serviços para Linux
# Desenvolvido por Fdsmultiservices
# Suporte WhatsApp: (+258) 86 213 4230 | Email: fdsmultiservices@gmail.com
# ==============================================================================

set -e

echo "=============================================================================="
echo "       ZBIZ+ ENTERPRISE - DESINSTALADOR (LINUX)"
echo "       Desenvolvido por: Fdsmultiservices"
echo "=============================================================================="
echo ""
echo "A remover atalhos e configurações locais do ZBIZ+..."

# 1. Remover atalhos da Área de Trabalho
rm -f "${HOME}/Desktop/ZBIZ+ Terminal POS.desktop"
rm -f "${HOME}/Área de Trabalho/ZBIZ+ Terminal POS.desktop"

# 2. Remover lançador do Menu de Aplicações
rm -f "${HOME}/.local/share/applications/zbiz-plus.desktop"

# 3. Remover inicialização automática
rm -f "${HOME}/.config/autostart/zbiz-plus.desktop"

# 4. Parar e remover serviço de servidor local systemd (caso tenha instalado a Opção 2)
if command -v systemctl &>/dev/null; then
    systemctl --user stop zbizplus.service 2>/dev/null || true
    systemctl --user disable zbizplus.service 2>/dev/null || true
    rm -f "${HOME}/.config/systemd/user/zbizplus.service"
    systemctl --user daemon-reload 2>/dev/null || true
fi

# 5. Atualizar base de dados de aplicações
if command -v update-desktop-database &>/dev/null; then
    update-desktop-database "${HOME}/.local/share/applications" 2>/dev/null || true
fi

echo ""
echo "=============================================================================="
echo "✅ Desinstalação concluída com sucesso!"
echo "   ✓ Atalho da Área de Trabalho removido."
echo "   ✓ Lançador do Menu de Aplicações removido."
echo "   ✓ Inicialização automática desativada."
echo "   ✓ Serviços de segundo plano (se existentes) desativados."
echo "=============================================================================="
