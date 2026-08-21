@echo off
REM Start Laravel dev servers
REM Usage: dev [artisan-port] [vite-port]
REM   Default: artisan on 8000, Vite on 3000

set ARTISAN_PORT=%1
set VITE_PORT=%2
if "%ARTISAN_PORT%"=="" set ARTISAN_PORT=8000
if "%VITE_PORT%"=="" set VITE_PORT=3000

echo Starting dev servers...
echo   Laravel: http://localhost:%ARTISAN_PORT%
echo   Vite:    http://localhost:%VITE_PORT%
echo   Press Ctrl+C to stop
echo.

start /min "Laravel Server" cmd /c "cd /d %~dp0 && php artisan serve --port=%ARTISAN_PORT%"
call npm run dev -- --port %VITE_PORT%
