# Plateforme de Gestion des Examens ÖSD

Une application web complète pour la gestion des examens ÖSD (Österreichisches Sprachdiplom) en langue allemande.

## Fonctionnalités

### Pour les Étudiants
- ✅ **Inscription aux examens** - Inscription simple et rapide aux examens disponibles
- ✅ **Vérification d'email** - Confirmation de compte par email
- ✅ **Tableau de bord personnel** - Suivi des inscriptions, paiements et résultats
- ✅ **Gestion des paiements** - Confirmation des paiements en ligne
- ✅ **Consultation des résultats** - Accès aux résultats des examens

### Pour les Administrateurs
- ✅ **Gestion des examens** - Créer, modifier, supprimer des examens
- ✅ **Gestion des inscriptions** - Confirmer ou rejeter les inscriptions
- ✅ **Gestion des résultats** - Saisie et publication des résultats
- ✅ **Gestion des utilisateurs** - Vue d'ensemble de tous les étudiants
- ✅ **Tableau de bord administrateur** - Statistiques complètes

### Pour les Examinateurs
- ✅ **Tableau de bord des examens** - Vue des examens à venir
- ✅ **Saisie des résultats** - Entrée des scores et niveaux
- ✅ **Publication des résultats** - Rendre les résultats publics

## Design Professionnel

- 🎨 **Interface moderne** - Design responsif avec Bootstrap 5
- 📱 **Mobile-friendly** - Adapté à tous les appareils
- 🎯 **UX intuitive** - Navigation claire et facile à utiliser
- 🎨 **Couleurs professionnelles** - Palette bleu/doré élégante

## Stack Technologique

- **Framework**: Laravel 11
- **Base de données**: MySQL/SQLite
- **Frontend**: Bootstrap 5, Font Awesome
- **Authentication**: Laravel Built-in
- **Language**: PHP 8.2+

## Installation

### Prérequis
- PHP 8.2 ou supérieur
- Composer
- MySQL/SQLite
- Node.js (pour les assets frontend)

### Étapes d'installation

1. **Cloner le repository**
```bash
git clone <repository-url>
cd inscriptionexamen
```

2. **Installer les dépendances PHP**
```bash
composer install
```

3. **Copier le fichier .env**
```bash
cp .env.example .env
```

4. **Générer la clé d'application**
```bash
php artisan key:generate
```

5. **Configurer la base de données**

Dans le fichier `.env`, configurez votre base de données:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=osd_exams
DB_USERNAME=root
DB_PASSWORD=
```

6. **Exécuter les migrations**
```bash
php artisan migrate
```

7. **Seeder les données de test**
```bash
php artisan db:seed
```

8. **Installer les dépendances frontend**
```bash
npm install
npm run build
```

9. **Démarrer le serveur**
```bash
php artisan serve
```

L'application sera accessible à `http://127.0.0.1:8000`

## Comptes de Test

### Admin
- **Email**: admin@example.com
- **Mot de passe**: password123
- **Rôle**: Administrateur

### Examiner
- **Email**: examiner@example.com
- **Mot de passe**: password123
- **Rôle**: Examinateur

### Étudiant
- Plusieurs comptes étudiants sont créés lors du seeding
- **Email**: test1@example.com, test2@example.com, etc.
- **Mot de passe**: password123
- **Rôle**: Étudiant

## Structure des Dossiers

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── ExamController.php
│   │   └── AdminController.php
│   └── Middleware/
│       ├── IsAdmin.php
│       └── IsExaminer.php
└── Models/
    ├── User.php
    ├── Exam.php
    ├── ExamRegistration.php
    ├── ExamResult.php
    └── EmailVerification.php

routes/
└── web.php

resources/views/
├── layouts/
│   └── app.blade.php
├── auth/
│   ├── login.blade.php
│   └── register.blade.php
├── dashboard/
│   ├── index.blade.php
│   ├── admin.blade.php
│   └── examiner.blade.php
├── exams/
│   ├── index.blade.php
│   └── show.blade.php
└── admin/
    ├── students.blade.php
    ├── student-details.blade.php
    ├── exams.blade.php
    ├── create-exam.blade.php
    ├── edit-exam.blade.php
    ├── exam-registrations.blade.php
    └── enter-results.blade.php

database/
├── migrations/
│   ├── create_exams_table.php
│   ├── create_exam_registrations_table.php
│   ├── create_exam_results_table.php
│   ├── create_email_verifications_table.php
│   └── add_fields_to_users_table.php
└── seeders/
    └── DatabaseSeeder.php
```

## Routes Principales

### Authentification
- `GET /login` - Formulaire de connexion
- `POST /login` - Traiter la connexion
- `GET /register` - Formulaire d'inscription
- `POST /register` - Créer un nouvel utilisateur
- `GET /verify-email/{token}` - Vérifier l'email

### Examens
- `GET /exams` - Lister tous les examens
- `GET /exams/{exam}` - Détails d'un examen
- `POST /exams/{exam}/register` - S'inscrire à un examen

### Tableau de Bord
- `GET /dashboard` - Tableau de bord étudiant

### Administration
- `GET /admin/dashboard` - Tableau de bord admin
- `GET /admin/students` - Lister les étudiants
- `GET /admin/exams` - Gérer les examens
- `POST /admin/exams` - Créer un examen
- `GET /admin/exams/{exam}/registrations` - Voir les inscriptions
- `GET /admin/exams/{exam}/results` - Saisir les résultats

## Statuts

### Inscription aux Examens
- `pending` - En attente de confirmation
- `confirmed` - Confirmée
- `cancelled` - Annulée
- `completed` - Terminée

### Paiement
- `unpaid` - Non payé
- `paid` - Payé

### Résultats d'Examen
- `pending` - En attente
- `passed` - Réussi
- `failed` - Échoué

## Niveaux d'Examen ÖSD

- **A1** - Débutant absolu
- **A2** - Élémentaire
- **B1** - Intermédiaire
- **B2** - Intermédiaire supérieur
- **C1** - Avancé
- **C2** - Maîtrise

## Compétences Évaluées

Chaque examen évalue 4 compétences:
1. **Lecture** (Lesen)
2. **Écoute** (Hörverstehen)
3. **Écriture** (Schreiben)
4. **Expression Orale** (Sprechen)

## Sécurité

- Authentification par mot de passe hashé
- Vérification d'email requise
- Middleware d'autorisation pour admin et examinateur
- Protection CSRF sur tous les formulaires
- Validation des données côté serveur

## Support

Pour toute question ou problème, veuillez contacter:
- Email: info@osd-exams.com
- Téléphone: +43 1 234 5678

## Licence

Tous droits réservés © 2026 ÖSD Exams Management.
