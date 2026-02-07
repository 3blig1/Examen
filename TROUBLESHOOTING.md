# Dépannage et Erreurs Courantes

## Erreurs de Migration

### ❌ Erreur: "SQLSTATE[HY000]: General error: 1030 Got error..."
**Cause**: Table n'existe pas  
**Solution**:
```bash
php artisan migrate:fresh
php artisan db:seed
```

### ❌ Erreur: "Base de données 'inscription_examen' n'existe pas"
**Cause**: Base de données non créée  
**Solution**: 
1. Créer la BD dans phpMyAdmin ou MySQL
2. Ou utiliser SQLite (modifier .env: `DB_CONNECTION=sqlite`)

### ❌ Erreur: "Unknown column 'password' in 'field list'"
**Cause**: Migrations non exécutées  
**Solution**:
```bash
php artisan migrate
```

---

## Erreurs d'Authentification

### ❌ Erreur: "Invalid credentials"
**Cause**: Email ou mot de passe incorrect  
**Solution**:
1. Vérifier que l'utilisateur existe: `php artisan tinker`
2. Vérifier que l'email est vérifié

### ❌ Erreur: "Please verify your email first"
**Cause**: Email non vérifié  
**Solution**:
1. Vérifier le log: `tail -f storage/logs/laravel.log`
2. Cliquer sur le lien de vérification fourni
3. Ou modifier manuellement dans DB: `email_verified_at = NOW()`

### ❌ Erreur: "Your account is disabled"
**Cause**: Compte désactivé  
**Solution**: Modifier `is_active = 1` dans la table users

---

## Erreurs de Vues

### ❌ Erreur: "View [...] not found"
**Cause**: Fichier vue manquant  
**Solution**: Créer le fichier dans `resources/views/` ou vérifier le chemin exact

### ❌ CSS/JS ne se charge pas
**Cause**: Bootstrap CDN inaccessible ou cache navigateur  
**Solution**:
1. Vider le cache navigateur (Ctrl+Shift+Del)
2. Vérifier la connexion Internet
3. Vérifier que `resources/views/layouts/app.blade.php` charge Bootstrap

### ❌ Page blanche ou erreur 500
**Cause**: Erreur PHP  
**Solution**:
1. Vérifier le log: `storage/logs/laravel.log`
2. Activer le debug dans .env: `APP_DEBUG=true`
3. Exécuter: `php artisan cache:clear`

---

## Erreurs de Routes

### ❌ Erreur: "Route [...] not defined"
**Cause**: Route non créée ou mal nommée  
**Solution**:
1. Vérifier dans `routes/web.php`
2. Exécuter: `php artisan route:list`
3. Vérifier le nom exact avec `->name('nom')`

### ❌ Erreur: "403 Unauthorized"
**Cause**: Pas les permissions  
**Solution**:
1. Vérifier le rôle de l'utilisateur (admin, student, examiner)
2. Vérifier le middleware dans les routes

---

## Erreurs de Modèles

### ❌ Erreur: "Call to undefined relationship"
**Cause**: Relation non définie dans le modèle  
**Solution**: Ajouter la relation dans le modèle:
```php
public function user() {
    return $this->belongsTo(User::class);
}
```

### ❌ Erreur: "Model not found" (404)
**Cause**: Objet n'existe pas dans la BD  
**Solution**:
1. Vérifier l'ID
2. Vérifier que les données existent: `php artisan tinker`
3. Vérifier le seeder

---

## Erreurs de Paiement

### ❌ Le paiement ne s'enregistre pas
**Cause**: Validation échouée  
**Solution**:
1. Vérifier que le montant est positif
2. Vérifier le type de donnée (numérique)
3. Vérifier les logs

### ❌ Erreur: "The amount paid field is required"
**Cause**: Champ vide dans le formulaire  
**Solution**: Remplir le champ montant avant de soumettre

---

## Erreurs d'Email

### ❌ Email de vérification non reçu
**Cause**: Configuration MAIL non faite  
**Solution**:
1. Chercher l'URL dans `storage/logs/laravel.log`
2. Copier l'URL dans le navigateur
3. Ou configurer MAIL dans .env et relancer

### ❌ Erreur SMTP
**Cause**: Configuration MAIL incorrecte  
**Solution**: Vérifier dans .env:
```env
MAIL_DRIVER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre-email@gmail.com
MAIL_PASSWORD=votre-mot-de-passe
MAIL_ENCRYPTION=tls
```

---

## Erreurs de Performance

### ❌ Requête très lente
**Cause**: Requête N+1 ou données nombreuses  
**Solution**:
1. Utiliser eager loading: `with('relation')`
2. Ajouter des indexes BD
3. Utiliser la pagination: `paginate(15)`

### ❌ Mémoire insuffisante
**Cause**: Trop de données chargées  
**Solution**:
1. Utiliser la pagination
2. Augmenter memory_limit dans php.ini
3. Optimiser les requêtes

---

## Solutions Rapides

### 🔧 Réinitialiser la BD complètement
```bash
php artisan migrate:fresh
php artisan db:seed
```

### 🔧 Vider tous les caches
```bash
php artisan cache:clear
php artisan route:cache --clear
php artisan config:clear
php artisan view:clear
```

### 🔧 Régénérer la clé d'application
```bash
php artisan key:generate
```

### 🔧 Tester les migrations
```bash
php artisan migrate:status
```

### 🔧 Accéder à la base de données
```bash
php artisan tinker
```

### 🔧 Créer un utilisateur test
```bash
php artisan tinker
User::create(['name' => 'Test', 'email' => 'test@example.com', 'password' => bcrypt('password')])
```

---

## Checklist de Déploiement

- [ ] Vérifier `php artisan migrate:status` - toutes les migrations doivent être "Ran"
- [ ] Vérifier `php artisan config:cache` - pas d'erreurs
- [ ] Vérifier les logs: `tail -f storage/logs/laravel.log`
- [ ] Tester l'inscription d'un nouvel utilisateur
- [ ] Tester la connexion
- [ ] Tester l'inscription à un examen
- [ ] Vérifier que les emails s'envoient (ou voir dans les logs)
- [ ] Tester le rôle admin
- [ ] Tester le rôle examiner
- [ ] Vérifier que les permissions fonctionnent

---

## Contact et Support

Pour des problèmes persistants:
- Vérifier le fichier `storage/logs/laravel.log` (contient les erreurs détaillées)
- Exécuter `php artisan tinker` pour tester les requêtes
- Vérifier la configuration dans `.env`
- Consulter la documentation Laravel: https://laravel.com/docs

---

**Dernière mise à jour**: 2026-02-03
