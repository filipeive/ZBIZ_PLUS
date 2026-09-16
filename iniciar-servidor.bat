@echo off
chcp 65001 >nul
title ZBIZ+ Enterprise - Inicializador do Servidor Local
color 0A

:: Entrar no diretório do projeto
cd /d "%~dp0"

echo ==============================================================================
echo        ZBIZ+ ENTERPRISE CLOUD ^& POS SUITE - SERVIDOR LOCAL
echo        Desenvolvido por: Fdsmultiservices
echo        Suporte Técnico: (+258) 86 213 4230 ^| fdsmultiservices@gmail.com
echo ==============================================================================
echo.

:: Detectar PHP no PATH ou em locais comuns (XAMPP / Laragon)
set "PHP_BIN=php"
where php >nul 2>nul
if %errorlevel% neq 0 (
    if exist "C:\xampp\php\php.exe" (
        set "PHP_BIN=C:\xampp\php\php.exe"
    ) else if exist "C:\laragon\bin\php\php-8.3*\php.exe" (
        for /d %%D in ("C:\laragon\bin\php\php-*") do if exist "%%D\php.exe" set "PHP_BIN=%%D\php.exe"
    ) else (
        echo [ERRO] O PHP não foi encontrado nesta máquina!
        echo Certifique-se de que o XAMPP, Laragon ou PHP 8.2+ está instalado.
        echo.
        pause
        exit /b 1
    )
)

echo [1/2] Iniciando servidor ZBIZ+ na porta 8000...
:: Inicia o servidor artisan em uma janela dedicada sem bloquear este console
start "ZBIZ+ Servidor Local (Porta 8000) - NAO FECHAR" "%PHP_BIN%" artisan serve --host=127.0.0.1 --port=8000

echo [2/2] Aguardando inicialização do serviço...
timeout /t 2 /nobreak >nul

set "TARGET_URL=http://127.0.0.1:8000"

:: Detectar navegador para abrir em Modo App Nativo (janela limpa de PDV)
if exist "%ProgramFiles(x86)%\Microsoft\Edge\Application\msedge.exe" (
    start "" "%ProgramFiles(x86)%\Microsoft\Edge\Application\msedge.exe" --app=%TARGET_URL%
) else if exist "%ProgramFiles%\Microsoft\Edge\Application\msedge.exe" (
    start "" "%ProgramFiles%\Microsoft\Edge\Application\msedge.exe" --app=%TARGET_URL%
) else if exist "%ProgramFiles%\Google\Chrome\Application\chrome.exe" (
    start "" "%ProgramFiles%\Google\Chrome\Application\chrome.exe" --app=%TARGET_URL%
) else if exist "%ProgramFiles(x86)%\Google\Chrome\Application\chrome.exe" (
    start "" "%ProgramFiles(x86)%\Google\Chrome\Application\chrome.exe" --app=%TARGET_URL%
) else (
    start "" "%TARGET_URL%"
)

echo.
echo ==============================================================================
echo [✓] Servidor ZBIZ+ iniciado com sucesso em %TARGET_URL%!
echo     A janela do servidor deve permanecer aberta enquanto utiliza o sistema.
echo ==============================================================================
echo.
timeout /t 3 >nul
exit /b 0

