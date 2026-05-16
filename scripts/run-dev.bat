@echo off
REM =================================================================
REM Web-2 Project - Auto Start Script
REM Checks project setup and starts PHP dev server
REM =================================================================
setlocal enabledelayedexpansion

cd /d "%~dp0.."
set REPO_ROOT=%cd%
set FRONTEND_DIR=%REPO_ROOT%\frontend
set PORT=8000
set HOST=localhost

echo.
echo ====== Web-2 Project Auto Start ======
echo.

REM Find PHP executable
set PHP_EXE=
for %%A in (php.exe) do (
    if not "%%~$PATH:A"=="" (
        set PHP_EXE=%%~$PATH:A
        goto :php_found
    )
)

REM Check common XAMPP/PHP install locations
if exist "C:\xampp\php\php.exe" (
    set PHP_EXE=C:\xampp\php\php.exe
    goto :php_found
)
if exist "C:\Program Files\php\php.exe" (
    set PHP_EXE=C:\Program Files\php\php.exe
    goto :php_found
)
if exist "C:\Program Files (x86)\php\php.exe" (
    set PHP_EXE=C:\Program Files (x86)\php\php.exe
    goto :php_found
)

echo ERROR: PHP not found in PATH or common locations
echo Please install PHP or add it to PATH
pause
exit /b 1

:php_found
echo [1/3] Found PHP: %PHP_EXE%

REM Run health check
echo [2/3] Running health check...
"%PHP_EXE%" -f "%REPO_ROOT%\scripts\health-check.php"
if %ERRORLEVEL% neq 0 (
    echo.
    echo Health check failed. Please fix the issues above.
    pause
    exit /b 1
)

REM Start server
echo.
echo [3/3] Starting PHP server on http://%HOST%:%PORT%
echo Server will open in your browser shortly...
echo Press Ctrl+C to stop the server.
echo.

start http://%HOST%:%PORT%/pages/index.php
"%PHP_EXE%" -S %HOST%:%PORT% -t "%FRONTEND_DIR%"
