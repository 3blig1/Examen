# Guide Complet d'Utilisation - Plateforme ÖSD

## 🚀 Démarrage Rapide

### Pour les Développeurs

1. **Installation initiale**
```bash
cd c:\laragon\www\inscriptionexamen
composer install
php artisan key:generate
```

2. **Configuration de la base de données** (dans .env)
```env
DB_DATABASE=inscription_examen
DB_USERNAME=root
DB_PASSWORD=
```

3. **Migrations et Seeding**
```bash
php artisan migrate
php artisan db:seed
```

4. **Lancer l'application**
```bash
php artisan serve
```

Accédez à: `http://localhost:8000`

---

## 👥 Rôles et Permissions

### 1. **Administrateur**
- Accès complet à l'interface d'administration
- Gestion des examens (créer, modifier, supprimer)
- Gestion des inscriptions (confirmer, rejeter)
- Saisie et publication des résultats
- Vue d'ensemble de tous les étudiants

**Accès**: `/admin/dashboard`

### 2. **Examinateur**
- Consultation des examens programmés
- Saisie des résultats des étudiants
- Publication des résultats
- Suivi des examens en cours

**Accès**: `/examiner/dashboard`

### 3. **Étudiant**
- Inscription aux examens disponibles
- Confirmation de paiement
- Consultation du tableau de bord personnel
- Consultation de ses résultats

**Accès**: `/dashboard`

---

## 📋 Workflows Principaux

### Workflow 1: Inscription d'un Étudiant

```
1. Nouvel utilisateur → Page d'inscription
2. Remplir le formulaire avec informations personnelles
3. Créer le compte (mot de passe hashé, email enregistré)
4. Email de vérification envoyé (lien dans les logs)
5. Cliquer sur le lien de vérification
6. Accès au tableau de bord
7. Sélectionner examen
8. Confirmer l'inscription
9. Effectuer le paiement
10. Inscription confirmée
```

### Workflow 2: Gestion des Examens (Admin)

```
1. Admin → /admin/exams
2. Créer un nouvel examen
3. Définir: Niveau (A1-C2), Date, Lieu, Capacité, Tarif
4. Examen publié et visible pour les étudiants
5. Suivre les inscriptions
6. Confirmer/Rejeter les inscriptions
7. Saisir les résultats pour chaque étudiant
8. Publier les résultats
```

### Workflow 3: Publication des Résultats

```
1. Examinateur → Examen
2. Saisir les scores (Lecture, Écoute, Écriture, Orale)
3. Définir le niveau atteint (A1-C2)
4. Marquer comme Réussi/Échoué
5. Ajouter des remarques optionnelles
6. Publier les résultats
7. Les étudiants reçoivent notification des résultats
```

---

## 📊 Structures de Données

### Table: users
```sql
- id (PK)
- name, first_name, last_name
- email (unique)
- phone, country, date_of_birth
- password (hashed)
- role (admin, examiner, student)
- email_verified_at
- is_active
- created_at, updated_at
```

### Table: exams
```sql
- id (PK)
- name (A1-C2)
- description
- date, location
- max_participants, registered_count
- price
- status (upcoming, ongoing, completed)
- created_at, updated_at
```

### Table: exam_registrations
```sql
- id (PK)
- user_id (FK)
- exam_id (FK)
- status (pending, confirmed, cancelled, completed)
- payment_status (unpaid, paid)
- amount_paid, payment_date
- confirmed_at
- created_at, updated_at
```

### Table: exam_results
```sql
- id (PK)
- exam_registration_id (FK)
- exam_id (FK)
- user_id (FK)
- reading_score, listening_score, writing_score, speaking_score
- total_score
- level_achieved (A1-C2)
- status (pending, passed, failed)
- published_at
- remarks
- created_at, updated_at
```

### Table: email_verifications
```sql
- id (PK)
- user_id (FK)
- token (unique)
- expires_at
- verified_at
- created_at, updated_at
```

---

## 🎯 Points de Personnalisation

### 1. **Couleurs et Branding**
Modifiez dans `resources/views/layouts/app.blade.php`:
```css
--primary-color: #1a5f7a;      /* Bleu foncé */
--secondary-color: #d4af37;    /* Doré */
--accent-color: #2d7a96;       /* Bleu accentué */
```

### 2. **Textes et Contenu**
- Page d'accueil: `resources/views/welcome.blade.php`
- Footer: `resources/views/layouts/app.blade.php`
- Noms d'examen: Modifiez dans les seeders

### 3. **Emails**
Créez une Mailable personnalisée pour les emails:
```bash
php artisan make:mailable EmailVerification
```

### 4. **Niveaux d'Examen**
Modifiez les options dans:
- `app/Http/Controllers/AdminController.php`
- Les vues admin

---

## 🔒 Sécurité

### Implémentée:
- ✅ Hachage des mots de passe (bcrypt)
- ✅ Protection CSRF sur tous les formulaires
- ✅ Vérification d'email obligatoire
- ✅ Middleware d'autorisation (admin/examiner)
- ✅ Validation côté serveur
- ✅ Dates de vérification d'email limitées (24h)

### À Ajouter:
- Taux limite (rate limiting)
- Historique des modifications
- Chiffrement des données sensibles
- Audit logging
- 2FA pour les administrateurs

---

## 📱 Responsive Design

L'application utilise Bootstrap 5 et est complètement responsive:
- ✅ Desktop (1920px+)
- ✅ Tablette (768px - 1024px)
- ✅ Mobile (320px - 767px)

---

## 🧪 Tests Manuels

### Scénario 1: Inscription et Connexion
1. Visiter `/register`
2. Remplir le formulaire
3. Vérifier email (voir les logs)
4. Connexion réussie

### Scénario 2: Inscription à un Examen
1. Se connecter (étudiant)
2. Aller sur `/exams`
3. Sélectionner un examen
4. S'inscrire
5. Confirmer paiement
6. Voir dans le tableau de bord

### Scénario 3: Gestion Admin
1. Connexion admin
2. Aller sur `/admin/dashboard`
3. Créer un nouvel examen
4. Voir les inscriptions
5. Saisir les résultats
6. Publier les résultats

---

## 📞 Support et Maintenance

### Logs
- Logs d'application: `storage/logs/laravel.log`
- Voir pour les erreurs et les informations d'email

### Base de Données
- Sauvegarder régulièrement
- Vérifier la taille: `storage/logs/`

### Performance
- Utiliser `php artisan tinker` pour les requêtes test
- Vérifier les requêtes N+1 dans les logs

---

## 🎓 Exemples d'Utilisation

### Créer un nouvel examen (API interne)
```php
$exam = Exam::create([
    'name' => 'B1',
    'description' => 'Examen B1',
    'date' => now()->addDays(30),
    'max_participants' => 25,
    'price' => 100,
    'location' => 'Vienna Center',
    'status' => 'upcoming'
]);
```

### Enregistrer un résultat
```php
$result = ExamResult::create([
    'exam_registration_id' => $registration->id,
    'exam_id' => $exam->id,
    'user_id' => $user->id,
    'reading_score' => 85,
    'listening_score' => 80,
    'writing_score' => 75,
    'speaking_score' => 88,
    'level_achieved' => 'B1',
    'status' => 'passed'
]);
```

---

## 🎨 Personnalisation du Design

### Logo et Images
Ajoutez vos images dans `public/images/`

### Police personnalisée
Modifiez dans `resources/views/layouts/app.blade.php`:
```html
<link href="https://fonts.googleapis.com/css2?family=VotrePolice" rel="stylesheet">
```

### Thème Sombre (optionnel)
Ajoutez les classes CSS pour le mode sombre dans les styles personnalisés

---

## 📈 Prochaines Étapes

Pour améliorer l'application:

1. **Analytics** - Ajouter des statistiques détaillées
2. **API REST** - Pour mobile app
3. **PDF Certificates** - Générer des certificats
4. **Email Notifications** - Envoyer des notifications
5. **File Uploads** - Permettre l'upload de documents
6. **SMS Reminders** - Rappels par SMS
7. **Payment Gateway** - Intégration Stripe/PayPal
8. **Multi-language** - Support multilingue

---

**Version**: 1.0.0  
**Date**: 2026-02-03  
**Dernière mise à jour**: 2026-02-03
