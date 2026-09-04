@echo off
set PHP_PATH=C:\MAMP\bin\php\php8.3.1\php.exe
if not exist "%PHP_PATH%" (
    echo PHP not found at %PHP_PATH%
    pause
    exit /b
)
echo Starting Ensan System with MySQL (Make sure MySQL is running in XAMPP)...
"%PHP_PATH%" artisan serve --host=0.0.0.0 --port=8000
pause
