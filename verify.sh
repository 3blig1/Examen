#!/bin/bash

# Script de vérification de la plateforme ÖSD

echo "=========================================="
echo "Vérification de la plateforme ÖSD"
echo "=========================================="
echo ""

# Vérifier PHP
echo "✓ Vérification de PHP..."
if command -v php &> /dev/null; then
    echo "  PHP version: $(php -v | head -n 1)"
else
    echo "  ✗ PHP non trouvé"
    exit 1
fi

# Vérifier les fichiers clés
echo ""
echo "✓ Vérification des fichiers clés..."
files=(
    "app/Models/User.php"
    "app/Models/Exam.php"
    "app/Models/ExamRegistration.php"
    "app/Http/Controllers/AuthController.php"
    "app/Http/Controllers/DashboardController.php"
    "routes/web.php"
    "resources/views/layouts/app.blade.php"
    "database/seeders/DatabaseSeeder.php"
)

for file in "${files[@]}"; do
    if [ -f "$file" ]; then
        echo "  ✓ $file"
    else
        echo "  ✗ $file manquant"
    fi
done

# Vérifier Composer
echo ""
echo "✓ Vérification de Composer..."
if [ -f "composer.json" ]; then
    echo "  ✓ composer.json trouvé"
else
    echo "  ✗ composer.json manquant"
fi

# Vérifier la clé d'application
echo ""
echo "✓ Vérification de la configuration..."
if grep -q "APP_KEY=" .env 2>/dev/null; then
    echo "  ✓ APP_KEY configurée"
else
    echo "  ⚠ APP_KEY non trouvée (exécutez: php artisan key:generate)"
fi

echo ""
echo "=========================================="
echo "Étapes suivantes:"
echo "=========================================="
echo ""
echo "1. composer install"
echo "2. php artisan key:generate"
echo "3. php artisan migrate"
echo "4. php artisan db:seed"
echo "5. php artisan serve"
echo ""
echo "Puis accédez à: http://localhost:8000"
echo ""
