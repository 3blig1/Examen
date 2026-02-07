@echo off
REM Script de vérification de la plateforme ÖSD (Windows)

echo.
echo ==========================================
echo Verification de la plateforme OSD
echo ==========================================
echo.

REM Vérifier les fichiers clés
echo Verification des fichiers cles...
setlocal enabledelayedexpansion

set "files[0]=app\Models\User.php"
set "files[1]=app\Models\Exam.php"
set "files[2]=app\Models\ExamRegistration.php"
set "files[3]=app\Http\Controllers\AuthController.php"
set "files[4]=app\Http\Controllers\DashboardController.php"
set "files[5]=routes\web.php"
set "files[6]=resources\views\layouts\app.blade.php"
set "files[7]=database\seeders\DatabaseSeeder.php"

for /L %%i in (0,1,7) do (
    if exist "!files[%%i]!" (
        echo   OK !files[%%i]!
    ) else (
        echo   ERREUR !files[%%i]! manquant
    )
)

echo.
echo Verification de la configuration...
if exist "composer.json" (
    echo   OK composer.json trouve
) else (
    echo   ERREUR composer.json manquant
)

echo.
echo ==========================================
echo Etapes suivantes:
echo ==========================================
echo.
echo 1. composer install
echo 2. php artisan key:generate
echo 3. php artisan migrate
echo 4. php artisan db:seed
echo 5. php artisan serve
echo.
echo Puis accedez a: http://localhost:8000
echo.
pause
