@echo off
chcp 65001 >nul
title ZBIZ+ Enterprise Suite - Instalador & Onboarding Kiosk
color 0A

:: ==============================================================================
:: ZBIZ+ Enterprise Cloud & POS Suite v1.0.19
:: Instalador e Configurador de Terminal Kiosk / Desktop para Windows
:: Desenvolvido por Fdsmultiservices
:: Suporte WhatsApp: (+258) 86 213 4230 | Email: fdsmultiservices@gmail.com
:: ==============================================================================

cls
echo ==============================================================================
echo        ZBIZ+ ENTERPRISE CLOUD ^& POS SUITE - MOÇAMBIQUE
echo        Instalador de Terminal de Venda / Farmácia / Restauração
echo        Desenvolvido por: Fdsmultiservices
echo        Suporte Técnico: (+258) 86 213 4230 ^| fdsmultiservices@gmail.com
echo ==============================================================================
echo.
echo Escolha o modo de instalação desejado:
echo.
echo [1] Configurar Terminal POS Kiosk (Modo App Nativo para Balcão/Caixa)
echo     - Ideal para postos de venda em Farmácias, Lojas e Restaurantes.
echo     - Cria atalho na Área de Trabalho que abre sem barra de navegador.
echo     - Suporta impressão térmica silenciosa de recibos.
echo.
echo [2] Instalar Servidor Local Completo (Modo Offline / On-Premise)
echo     - Instala e inicializa o servidor ZBIZ+ nesta máquina via Docker/PHP.
echo.
echo [3] Abrir ZBIZ+ Cloud imediatamente no modo App
echo.
echo [0] Sair
echo.
set /p OPTION="Digite a opção desejada [1, 2, 3 ou 0]: "

if "%OPTION%"=="1" goto INSTALL_KIOSK
if "%OPTION%"=="2" goto INSTALL_LOCAL_SERVER
if "%OPTION%"=="3" goto OPEN_APP_DIRECT
if "%OPTION%"=="0" goto EXIT_SCRIPT

echo Opção inválida.
pause
goto EXIT_SCRIPT

:INSTALL_KIOSK
cls
echo ==============================================================================
echo        CONFIGURAÇÃO DE TERMINAL KIOSK / APP NATIVO
echo ==============================================================================
echo.
echo Informe o endereço URL do sistema ZBIZ+:
echo [Pressione ENTER para usar a Nuvem: http://146.235.224.99/zbiz_plus]
set /p TARGET_URL="URL do ZBIZ+ [Padrão: http://146.235.224.99/zbiz_plus]: "
if "%TARGET_URL%"=="" set TARGET_URL=http://146.235.224.99/zbiz_plus

echo.
echo Deseja qual formato de exibição?
echo [1] Modo App Nativo (Janela limpa, sem barra de navegação, redimensionável)
echo [2] Modo Kiosk Total (Tela cheia travada, ideal para operadores de caixa)
set /p VIEW_MODE="Escolha o formato [1 ou 2, Padrão: 1]: "
if "%VIEW_MODE%"=="" set VIEW_MODE=1

echo.
echo Deseja ativar a inicialização automática do ZBIZ+ ao ligar o computador?
echo [S] Sim, iniciar com o Windows
echo [N] Não, apenas criar atalho na Área de Trabalho
set /p AUTO_START="Escolha [S ou N, Padrão: S]: "
if "%AUTO_START%"=="" set AUTO_START=S

:: Detectar navegador disponível (Edge ou Chrome)
set BROWSER_CMD=
set BROWSER_NAME=

if exist "%ProgramFiles(x86)%\Microsoft\Edge\Application\msedge.exe" (
    set "BROWSER_CMD=%ProgramFiles(x86)%\Microsoft\Edge\Application\msedge.exe"
    set "BROWSER_NAME=Microsoft Edge"
) else if exist "%ProgramFiles%\Microsoft\Edge\Application\msedge.exe" (
    set "BROWSER_CMD=%ProgramFiles%\Microsoft\Edge\Application\msedge.exe"
    set "BROWSER_NAME=Microsoft Edge"
) else if exist "%ProgramFiles%\Google\Chrome\Application\chrome.exe" (
    set "BROWSER_CMD=%ProgramFiles%\Google\Chrome\Application\chrome.exe"
    set "BROWSER_NAME=Google Chrome"
) else if exist "%ProgramFiles(x86)%\Google\Chrome\Application\chrome.exe" (
    set "BROWSER_CMD=%ProgramFiles(x86)%\Google\Chrome\Application\chrome.exe"
    set "BROWSER_NAME=Google Chrome"
) else (
    set "BROWSER_CMD=msedge.exe"
    set "BROWSER_NAME=Navegador Padrão"
)

echo.
echo Navegador detectado: %BROWSER_NAME%
echo Configurando atalho no Desktop...

:: Argumentos de execução
if "%VIEW_MODE%"=="2" (
    set "BROWSER_ARGS=--kiosk %TARGET_URL% --kiosk-printing --no-first-run --disable-pinch"
) else (
    set "BROWSER_ARGS=--app=%TARGET_URL% --no-first-run"
)

:: Criação do script de inicialização local
set "LAUNCHER_BAT=%USERPROFILE%\ZBIZ_Launcher.bat"
(
    echo @echo off
    echo start "" "%BROWSER_CMD%" %BROWSER_ARGS%
) > "%LAUNCHER_BAT%"

:: Criar atalho na Área de Trabalho via PowerShell
set "ICON_PATH=%~dp0public\favicon.ico"
if not exist "%ICON_PATH%" (
    set "ICON_PATH=%BROWSER_CMD%"
)

powershell -ExecutionPolicy Bypass -Command "$ws = New-Object -ComObject WScript.Shell; $s = $ws.CreateShortcut([System.Environment]::GetFolderPath('Desktop') + '\ZBIZ+ Terminal POS.lnk'); $s.TargetPath = '%LAUNCHER_BAT%'; $s.WindowStyle = 7; $s.IconLocation = '%ICON_PATH%'; $s.Description = 'ZBIZ+ Enterprise POS Suite - Fdsmultiservices'; $s.Save()"

if /i "%AUTO_START%"=="S" (
    powershell -ExecutionPolicy Bypass -Command "$ws = New-Object -ComObject WScript.Shell; $s = $ws.CreateShortcut([System.Environment]::GetFolderPath('Startup') + '\ZBIZ+ Terminal POS.lnk'); $s.TargetPath = '%LAUNCHER_BAT%'; $s.WindowStyle = 7; $s.IconLocation = '%ICON_PATH%'; $s.Description = 'ZBIZ+ Enterprise POS Suite - Inicialização Automática'; $s.Save()"
    echo [✓] Inicialização automática configurada no Windows Startup.
)

echo.
echo ==============================================================================
echo [✓] SUCESSO! O Terminal ZBIZ+ foi instalado com sucesso na sua Área de Trabalho.
echo     Atalho: "ZBIZ+ Terminal POS"
echo     URL Alvo: %TARGET_URL%
echo ==============================================================================
echo.
echo Deseja abrir o sistema agora? (S/N)
set /p OPEN_NOW="[S/N, Padrão: S]: "
if "%OPEN_NOW%"=="" set OPEN_NOW=S
if /i "%OPEN_NOW%"=="S" (
    start "" "%BROWSER_CMD%" %BROWSER_ARGS%
)
goto EXIT_SCRIPT

:INSTALL_LOCAL_SERVER
cls
echo ==============================================================================
echo        INSTALAÇÃO DE SERVIDOR LOCAL OFFLINE
echo ==============================================================================
echo.
echo Verificando ambiente local...

where docker >nul 2>nul
if %errorlevel% equ 0 (
    echo [✓] Docker detectado!
    echo Deseja iniciar o ZBIZ+ via Docker Compose? (S/N)
    set /p USE_DOCKER="[Padrão: S]: "
    if "%USE_DOCKER%"=="" set USE_DOCKER=S
    if /i "%USE_DOCKER%"=="S" (
        echo Subindo containers Docker...
        docker compose up -d
        echo Containers iniciados em http://localhost:8000
        set "TARGET_URL=http://localhost:8000"
        set "VIEW_MODE=1"
        goto INSTALL_KIOSK
    )
)

where php >nul 2>nul
if %errorlevel% equ 0 (
    echo [✓] PHP detectado no PATH do sistema.
    echo Configurando ambiente local...
    if not exist .env copy .env.example .env
    php artisan key:generate --force
    php artisan migrate --force

    echo.
    echo ==============================================================================
    echo        CONFIGURAÇÃO DE SINCRONIZAÇÃO HÍBRIDA (LOCAL ^<--^> NUVEM)
    echo ==============================================================================
    echo Deseja ativar a Sincronização Híbrida Automática com a Nuvem Central ZBIZ+?
    echo [S] Sim, ativar sincronização em background a cada 5 minutos (Recomendado)
    echo [N] Não, operar exclusivamente offline
    set /p ENABLE_SYNC="Escolha [S/N, Padrão: S]: "
    if "%ENABLE_SYNC%"=="" set ENABLE_SYNC=S

    if /i "%ENABLE_SYNC%"=="S" (
        echo.
        echo Informe a URL da Nuvem Central:
        echo [Padrão: http://146.235.224.99/zbiz_plus/api/sync/ingest]
        set /p SYNC_URL="URL: "
        if "%SYNC_URL%"=="" set SYNC_URL=http://146.235.224.99/zbiz_plus/api/sync/ingest

        echo.
        echo Informe o Token Secreto de Sincronização:
        echo [Padrão: zbiz_sync_default_token]
        set /p SYNC_TOKEN="Token: "
        if "%SYNC_TOKEN%"=="" set SYNC_TOKEN=zbiz_sync_default_token

        findstr /v /c:"CLOUD_SYNC_URL=" /c:"CLOUD_SYNC_TOKEN=" .env > .env.tmp
        move /y .env.tmp .env >nul
        echo.>> .env
        echo CLOUD_SYNC_URL=%SYNC_URL%>> .env
        echo CLOUD_SYNC_TOKEN=%SYNC_TOKEN%>> .env

        :: Criar script VBS para execução 100% silenciosa em segundo plano (sem piscar tela preta)
        set "SYNC_VBS=%~dp0zbiz_sync_silent.vbs"
        (
            echo Set WshShell = CreateObject^("WScript.Shell"^)
            echo WshShell.CurrentDirectory = "%~dp0"
            echo WshShell.Run "php artisan zbiz:sync-push", 0, True
        ) > "%SYNC_VBS%"

        schtasks /create /tn "ZBIZ_Hybrid_Sync" /tr "wscript.exe \"%SYNC_VBS%\"" /sc minute /mo 5 /f >nul 2>nul
        echo [✓] Sincronização Híbrida agendada com sucesso no Windows a cada 5 minutos!
    )

    echo.
    echo Iniciando servidor em segundo plano na porta 8000...
    start /b php artisan serve --host=127.0.0.1 --port=8000
    timeout /t 3 >nul
    set "TARGET_URL=http://127.0.0.1:8000"
    goto INSTALL_KIOSK
) else (
    echo [!] PHP ou Docker não encontrados no Windows.
    echo Para utilizar como servidor local completo, instale o Docker Desktop ou o PHP 8.3.
    echo Alternativamente, use a opção [1] para conectar ao servidor em Nuvem ZBIZ+.
    pause
    goto EXIT_SCRIPT
)

:OPEN_APP_DIRECT
set "TARGET_URL=http://146.235.224.99/zbiz_plus"
start msedge --app=%TARGET_URL% 2>nul || start chrome --app=%TARGET_URL% 2>nul || start %TARGET_URL%
goto EXIT_SCRIPT

:EXIT_SCRIPT
echo.
echo Obrigado por utilizar as soluções Fdsmultiservices.
echo Pressione qualquer tecla para encerrar.
pause >nul
exit /b 0

