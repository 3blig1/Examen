# Instructions de Déploiement

## Vérification rapide

### 1. Vérifier que toutes les tables ont été créées
```bash
php artisan migrate:status
```

### 2. Si les migrations n'ont pas été exécutées
```bash
php artisan migrate
php artisan db:seed
```

### 3. Lancer le serveur de développement
```bash
php artisan serve
```

## Dépannage Courant

### Erreur: Table 'users' doesn't exist
→ Exécutez les migrations: `php artisan migrate`

### Erreur: Class 'App\Models\Exam' not found
→ Assurez-vous que les fichiers de modèles existent dans `app/Models/`

### Email de vérification non reçu
→ Vérifiez le fichier `storage/logs/laravel.log` pour l'URL de vérification

### Les vues ne s'affichent pas correctement
→ Vérifiez que Bootstrap 5 est chargé via CDN dans `resources/views/layouts/app.blade.php`

## Configuration du Serveur de Production

Pour un déploiement en production:

1. **Variables d'environnement** (.env)
```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-domaine.com
DB_CONNECTION=mysql
DB_HOST=votre-host
DB_DATABASE=votre_bdd
DB_USERNAME=utilisateur
DB_PASSWORD=mot_de_passe
MAIL_DRIVER=smtp
MAIL_HOST=mail.votre-domaine.com
MAIL_FROM_ADDRESS=noreply@votre-domaine.com
```

2. **Optimisation**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

3. **Permissions des fichiers**
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
```

## Authentification Email

Pour activer l'envoi d'emails de vérification, configurez MAIL_* dans .env:

```env
MAIL_DRIVER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre-email@gmail.com
MAIL_PASSWORD=votre-mot-de-passe
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@osd-exams.com
MAIL_FROM_NAME="ÖSD Exams"
```

## Utilisateurs de Test

### Admin
```
email: admin@example.com
password: password123
```

### Examiner
```
email: examiner@example.com
password: password123
```

### Students
```
10 étudiants créés avec:
password: password123
email: test1@example.com, test2@example.com, etc.
```

## API REST (Optionnel)

Pour ajouter une API REST, créez des routes dans `routes/api.php`:

```php
Route::post('/exams', [ExamController::class, 'store']);
Route::get('/exams', [ExamController::class, 'index']);
Route::post('/registrations', [ExamController::class, 'register']);
```

## Sauvegarde et Restauration

### Sauvegarder la base de données
```bash
mysqldump -u root -p osd_exams > backup.sql
```

### Restaurer la base de données
```bash
mysql -u root -p osd_exams < backup.sql
```

## Performance

Pour améliorer la performance:

1. Activer le cache des requêtes
2. Utiliser la mise en cache des vues
3. Optimiser les requêtes N+1
4. Utiliser CDN pour les assets statiques

## Monitoring

Vérifiez régulièrement:
- `storage/logs/laravel.log` pour les erreurs
- Les performances des requêtes database
- La taille de la base de données
