#!/usr/bin/env php
<?php
/**
 * Commandes Utiles pour la Plateforme ÖSD
 * 
 * Ce fichier contient toutes les commandes Laravel utiles
 * pour gérer et développer l'application.
 */

echo <<<'COMMANDS'

╔════════════════════════════════════════════════════════════╗
║      COMMANDES UTILES - PLATEFORME ÖSD                    ║
╚════════════════════════════════════════════════════════════╝

═══════════════════════════════════════════════════════════════
🚀 DÉMARRAGE DE L'APPLICATION
═══════════════════════════════════════════════════════════════

Installer les dépendances:
  $ composer install

Générer la clé d'application:
  $ php artisan key:generate

Exécuter les migrations:
  $ php artisan migrate

Charger les données de test:
  $ php artisan db:seed

Lancer le serveur:
  $ php artisan serve

═══════════════════════════════════════════════════════════════
📊 GESTION DE LA BASE DE DONNÉES
═══════════════════════════════════════════════════════════════

Voir l'état des migrations:
  $ php artisan migrate:status

Exécuter une migration spécifique:
  $ php artisan migrate --path=database/migrations/2026_02_03_100000_create_exams_table.php

Revenir à la dernière migration:
  $ php artisan migrate:rollback

Réinitialiser toutes les migrations:
  $ php artisan migrate:reset

Réinitialiser et recharger (ATTENTION!):
  $ php artisan migrate:fresh
  $ php artisan migrate:fresh --seed

Créer une nouvelle migration:
  $ php artisan make:migration create_table_name_table

═══════════════════════════════════════════════════════════════
👥 GESTION DES UTILISATEURS
═══════════════════════════════════════════════════════════════

Accéder à la base de données en CLI:
  $ php artisan tinker

Créer un nouvel utilisateur:
  > User::create(['name' => 'John', 'email' => 'john@example.com', 'password' => bcrypt('password123'), 'role' => 'student', 'email_verified_at' => now()])

Voir tous les utilisateurs:
  > User::all()

Voir un utilisateur spécifique:
  > User::find(1)
  > User::where('email', 'admin@example.com')->first()

Modifier un utilisateur:
  > $user = User::find(1)
  > $user->update(['name' => 'New Name'])

Supprimer un utilisateur:
  > User::find(1)->delete()

Créer plusieurs utilisateurs:
  > User::factory(10)->create(['role' => 'student', 'email_verified_at' => now()])

═══════════════════════════════════════════════════════════════
📚 GESTION DES EXAMENS
═══════════════════════════════════════════════════════════════

Voir tous les examens:
  > Exam::all()

Créer un examen:
  > Exam::create(['name' => 'B1', 'date' => now()->addDays(30), 'max_participants' => 30, 'price' => 100, 'location' => 'Vienna', 'status' => 'upcoming'])

Voir les inscriptions d'un examen:
  > $exam = Exam::find(1)
  > $exam->registrations

Voir les résultats d'un examen:
  > $exam->results

═══════════════════════════════════════════════════════════════
📝 GESTION DES INSCRIPTIONS
═══════════════════════════════════════════════════════════════

Voir toutes les inscriptions:
  > ExamRegistration::all()

Voir les inscriptions d'un étudiant:
  > $user = User::find(1)
  > $user->examRegistrations

Voir les inscriptions en attente:
  > ExamRegistration::where('status', 'pending')->get()

Confirmer une inscription:
  > $registration = ExamRegistration::find(1)
  > $registration->confirm()

═══════════════════════════════════════════════════════════════
📊 GESTION DES RÉSULTATS
═══════════════════════════════════════════════════════════════

Voir tous les résultats:
  > ExamResult::all()

Voir les résultats d'un étudiant:
  > $user = User::find(1)
  > $user->examResults

Créer un résultat:
  > ExamResult::create(['exam_registration_id' => 1, 'exam_id' => 1, 'user_id' => 1, 'reading_score' => 85, 'listening_score' => 80, 'writing_score' => 75, 'speaking_score' => 90, 'level_achieved' => 'B1', 'status' => 'passed'])

═══════════════════════════════════════════════════════════════
🔧 MAINTENANCE ET NETTOYAGE
═══════════════════════════════════════════════════════════════

Vider tous les caches:
  $ php artisan cache:clear

Vider le cache des configurations:
  $ php artisan config:clear

Vider le cache des routes:
  $ php artisan route:cache --clear

Vider le cache des vues:
  $ php artisan view:clear

Vider tous les caches (complet):
  $ php artisan optimize:clear

Mettre en cache les configurations (production):
  $ php artisan config:cache

Mettre en cache les routes (production):
  $ php artisan route:cache

═══════════════════════════════════════════════════════════════
🔍 DÉBOGAGE ET INFORMATION
═══════════════════════════════════════════════════════════════

Voir toutes les routes:
  $ php artisan route:list

Voir les migrations:
  $ php artisan migrate:status

Voir la configuration:
  > config('app.name')
  > config('database.default')

Voir les variables d'environnement:
  > env('APP_NAME')
  > env('APP_DEBUG')

Vérifier les fichiers logs:
  $ tail -f storage/logs/laravel.log

Générer les logs d'optimisation:
  $ php artisan optimize

═══════════════════════════════════════════════════════════════
🎨 DÉVELOPPEMENT
═══════════════════════════════════════════════════════════════

Créer un nouveau contrôleur:
  $ php artisan make:controller NameController

Créer un nouveau modèle:
  $ php artisan make:model Name

Créer une nouvelle migration:
  $ php artisan make:migration create_table_name_table

Créer un nouveau seeder:
  $ php artisan make:seeder NameSeeder

Créer une nouvelle mailable (email):
  $ php artisan make:mailable NameMailable

Créer un nouveau middleware:
  $ php artisan make:middleware NameMiddleware

═══════════════════════════════════════════════════════════════
🧪 TESTS
═══════════════════════════════════════════════════════════════

Créer un test:
  $ php artisan make:test NameTest

Exécuter les tests:
  $ php artisan test

Exécuter un test spécifique:
  $ php artisan test tests/Feature/ExamTest.php

═══════════════════════════════════════════════════════════════
📈 PRODUCTION
═══════════════════════════════════════════════════════════════

Mettre en mode production:
  Dans .env:
    APP_ENV=production
    APP_DEBUG=false

Générer les fichiers de cache:
  $ php artisan config:cache
  $ php artisan route:cache

Vérifier la configuration:
  $ php artisan migrate:status
  $ php artisan route:list

═══════════════════════════════════════════════════════════════
💾 SAUVEGARDE
═══════════════════════════════════════════════════════════════

Exporter la base de données:
  $ mysqldump -u root -p inscription_examen > backup.sql

Importer la base de données:
  $ mysql -u root -p inscription_examen < backup.sql

Sauvegarder l'application:
  $ tar -czf osd-backup.tar.gz .

═══════════════════════════════════════════════════════════════
🆘 DÉPANNAGE
═══════════════════════════════════════════════════════════════

Si la page s'affiche en blanc:
  1. Ajouter APP_DEBUG=true dans .env
  2. Vérifier storage/logs/laravel.log
  3. Exécuter: php artisan cache:clear

Si les migrations ne fonctionnent pas:
  > php artisan migrate:status
  > php artisan migrate --seed

Si les vues ne s'affichent pas:
  > php artisan view:clear
  > Vérifier le chemin dans la route

Si les emails ne sont pas envoyés:
  > Configurer MAIL_* dans .env
  > Vérifier storage/logs/laravel.log

Si la performance est lente:
  > Vérifier database queries dans les logs
  > Ajouter des indexes BD
  > Utiliser eager loading: with('relation')

═══════════════════════════════════════════════════════════════
📞 RESSOURCES
═══════════════════════════════════════════════════════════════

Documentation Laravel: https://laravel.com/docs
Tinker: https://laravel.com/docs/tinker
Eloquent: https://laravel.com/docs/eloquent
Migrations: https://laravel.com/docs/migrations
Validation: https://laravel.com/docs/validation

═══════════════════════════════════════════════════════════════

💡 Tips:
  - Utilisez php artisan tinker pour tester les requêtes
  - Vérifiez storage/logs/laravel.log en cas d'erreur
  - Utilisez APP_DEBUG=true durant le développement
  - Mettez en cache en production: php artisan optimize

═══════════════════════════════════════════════════════════════

COMMANDS;
?>
