@echo off
set PHP_PATH=%~dp0.tools\php83\php.exe
if not exist "%PHP_PATH%" (
    echo PHP not found at %PHP_PATH%
    pause
    exit /b
)
echo Starting Ensan System with MySQL (Make sure MySQL is running in XAMPP)...
"%PHP_PATH%" artisan serve --port=8000
pause
