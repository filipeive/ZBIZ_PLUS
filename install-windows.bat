@echo off
chcp 65001 >nul
title ZBIZ+ Enterprise Suite - Instalador Windows
color 0A

:: Garantir que o script executa no diretório onde está localizado
cd /d "%~dp0"

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
echo [1] Configurar Terminal POS (Conectar ao ZBIZ+ Nuvem)
echo     - Cria atalho de App Nativo na Área de Trabalho (sem barra de navegação).
echo     - Ideal para caixas/atendimento conectados à nuvem central.
echo.
echo [2] Instalar Servidor Local Completo (Modo Offline / No Computador)
echo     - Configura o ambiente local, chaves de criptografia e banco de dados.
echo     - Cria o atalho "ZBIZ+ Servidor Local" na Área de Trabalho.
echo.
echo [3] Iniciar o Servidor Local Imediatamente
echo.
echo [0] Sair
echo.
set /p OPTION="Digite a opção [1, 2, 3 ou 0]: "

if "%OPTION%"=="1" goto INSTALL_TERMINAL_CLOUD
if "%OPTION%"=="2" goto INSTALL_LOCAL_SERVER
if "%OPTION%"=="3" goto START_SERVER_NOW
if "%OPTION%"=="0" goto EXIT_SCRIPT

echo Opção inválida.
pause
goto EXIT_SCRIPT

:: ==============================================================================
:: OPÇÃO 1: TERMINAL POS (NUVEM OU URL REMOTA)
:: ==============================================================================
:INSTALL_TERMINAL_CLOUD
cls
echo ==============================================================================
echo        CONFIGURAÇÃO DE ATALHO DE TERMINAL POS (APP NATIVO)
echo ==============================================================================
echo.
echo Informe o endereço URL do sistema ZBIZ+:
echo [Pressione ENTER para usar a Nuvem Oficial: http://146.235.224.99/zbiz_plus]
set /p TARGET_URL="URL [Padrão: http://146.235.224.99/zbiz_plus]: "
if "%TARGET_URL%"=="" set "TARGET_URL=http://146.235.224.99/zbiz_plus"

:: Detectar navegador Edge ou Chrome
set "BROWSER_EXE="
if exist "%ProgramFiles(x86)%\Microsoft\Edge\Application\msedge.exe" (
    set "BROWSER_EXE=%ProgramFiles(x86)%\Microsoft\Edge\Application\msedge.exe"
) else if exist "%ProgramFiles%\Microsoft\Edge\Application\msedge.exe" (
    set "BROWSER_EXE=%ProgramFiles%\Microsoft\Edge\Application\msedge.exe"
) else if exist "%ProgramFiles%\Google\Chrome\Application\chrome.exe" (
    set "BROWSER_EXE=%ProgramFiles%\Google\Chrome\Application\chrome.exe"
) else if exist "%ProgramFiles(x86)%\Google\Chrome\Application\chrome.exe" (
    set "BROWSER_EXE=%ProgramFiles(x86)%\Google\Chrome\Application\chrome.exe"
)

:: Criar launcher em batch específico do terminal
set "TERMINAL_LAUNCHER=%USERPROFILE%\ZBIZ_Terminal.bat"
(
    echo @echo off
    if defined BROWSER_EXE (
        echo start "" "%BROWSER_EXE%" --app=%TARGET_URL%
    ) else (
        echo start "" "%TARGET_URL%"
    )
) > "%TERMINAL_LAUNCHER%"

:: Criar atalho na Área de Trabalho
powershell -ExecutionPolicy Bypass -Command "$ws = New-Object -ComObject WScript.Shell; $s = $ws.CreateShortcut([System.Environment]::GetFolderPath('Desktop') + '\ZBIZ+ Terminal POS.lnk'); $s.TargetPath = '%TERMINAL_LAUNCHER%'; $s.WindowStyle = 7; $s.Description = 'ZBIZ+ Enterprise POS Suite - Fdsmultiservices'; $s.Save()"

echo.
echo ==============================================================================
echo [✓] SUCESSO! O atalho "ZBIZ+ Terminal POS" foi criado na sua Área de Trabalho.
echo     URL Configurada: %TARGET_URL%
echo ==============================================================================
echo.
set /p OPEN_NOW="Deseja abrir o terminal agora? [S/N, Padrão: S]: "
if "%OPEN_NOW%"=="" set OPEN_NOW=S
if /i "%OPEN_NOW%"=="S" (
    call "%TERMINAL_LAUNCHER%"
)
goto EXIT_SCRIPT

:: ==============================================================================
:: OPÇÃO 2: INSTALAR SERVIDOR LOCAL COMPLETO
:: ==============================================================================
:INSTALL_LOCAL_SERVER
cls
echo ==============================================================================
echo        INSTALAÇÃO E CONFIGURAÇÃO DE SERVIDOR LOCAL
echo ==============================================================================
echo.

:: Detectar PHP no PATH ou XAMPP / Laragon
set "PHP_BIN=php"
where php >nul 2>nul
if %errorlevel% neq 0 (
    if exist "C:\xampp\php\php.exe" (
        set "PHP_BIN=C:\xampp\php\php.exe"
    ) else if exist "C:\laragon\bin\php\php-8.3*\php.exe" (
        for /d %%D in ("C:\laragon\bin\php\php-*") do if exist "%%D\php.exe" set "PHP_BIN=%%D\php.exe"
    ) else (
        echo [!] PHP não encontrado no PATH nem no XAMPP/Laragon.
        echo Para executar o servidor offline, por favor instale o PHP 8.2+ ou XAMPP.
        echo Se deseja conectar à Nuvem, utilize a Opção [1].
        pause
        goto EXIT_SCRIPT
    )
)

echo [1/4] Verificando arquivo de ambiente .env...
if not exist .env (
    copy .env.example .env >nul
    echo [✓] Arquivo .env criado a partir de .env.example.
) else (
    echo [✓] Arquivo .env já existe.
)

echo [2/4] Gerando chave de segurança da aplicação...
"%PHP_BIN%" artisan key:generate --force

echo [3/4] Atualizando estrutura do banco de dados (migrações)...
"%PHP_BIN%" artisan migrate --force
if %errorlevel% neq 0 (
    echo [!] AVISO: Não foi possível conectar ao banco de dados agora.
    echo     Certifique-se de que o MySQL/MariaDB (ex: XAMPP) está iniciado.
    echo     O sistema continuará com o driver de sessões resiliente.
)

echo [4/4] Criando atalho "ZBIZ+ Servidor Local" na Área de Trabalho...
set "SERVER_SCRIPT=%~dp0iniciar-servidor.bat"
powershell -ExecutionPolicy Bypass -Command "$ws = New-Object -ComObject WScript.Shell; $s = $ws.CreateShortcut([System.Environment]::GetFolderPath('Desktop') + '\ZBIZ+ Servidor Local.lnk'); $s.TargetPath = '%SERVER_SCRIPT%'; $s.WorkingDirectory = '%~dp0'; $s.Description = 'ZBIZ+ Servidor Local - Fdsmultiservices'; $s.Save()"

echo.
echo ==============================================================================
echo [✓] INSTALAÇÃO CONCLUÍDA COM SUCESSO!
echo     Criado atalho na Área de Trabalho: "ZBIZ+ Servidor Local"
echo     Para iniciar o sistema a qualquer momento, dê 2 cliques no atalho.
echo ==============================================================================
echo.
set /p START_NOW="Deseja iniciar o servidor local agora? [S/N, Padrão: S]: "
if "%START_NOW%"=="" set START_NOW=S
if /i "%START_NOW%"=="S" (
    call "%SERVER_SCRIPT%"
)
goto EXIT_SCRIPT

:: ==============================================================================
:: OPÇÃO 3: INICIAR SERVIDOR AGORA
:: ==============================================================================
:START_SERVER_NOW
cls
call "%~dp0iniciar-servidor.bat"
goto EXIT_SCRIPT

:: ==============================================================================
:: SAÍDA LIMPA
:: ==============================================================================
:EXIT_SCRIPT
echo.
echo Obrigado por utilizar as soluções Fdsmultiservices.
timeout /t 2 >nul
exit /b 0
