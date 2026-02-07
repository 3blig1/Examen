# Guide d'utilisation des traductions (i18n)

L'application supporte maintenant **3 langues** : **Français (fr)**, **Allemand (de)**, **Anglais (en)**.

## 📁 Structure des fichiers de traduction

```
resources/lang/
├── fr/
│   └── messages.php          # Traductions français
├── de/
│   └── messages.php          # Traductions allemand
└── en/
    └── messages.php          # Traductions anglais
```

## 🔄 Comment utiliser les traductions dans les vues

### Syntaxe basique :
```blade
{{ __('messages.home') }}
{{ __('messages.login') }}
{{ __('messages.exams') }}
```

### Syntaxe pour paramètres dynamiques :
```blade
{{ __('messages.key', ['param' => 'value']) }}
```

## 🌍 Sélecteur de langue

Le sélecteur de langue se trouve dans la **navbar en haut à droite**.

- Français (FR)
- Deutsch (DE)
- English (EN)

Cliquer sur une langue change immédiatement la locale et persiste la sélection en session.

## 📄 Fichiers à traduire dans les vues

Pour traduire complètement l'application, remplacer les textes hardcodés par `__('messages.key')` dans :

### Pages principales
- [resources/views/welcome.blade.php](resources/views/welcome.blade.php) - Page d'accueil
- [resources/views/auth/login.blade.php](resources/views/auth/login.blade.php) - Connexion
- [resources/views/auth/register.blade.php](resources/views/auth/register.blade.php) - Inscription
- [resources/views/dashboard/index.blade.php](resources/views/dashboard/index.blade.php) - Dashboard étudiant
- [resources/views/dashboard/admin.blade.php](resources/views/dashboard/admin.blade.php) - Dashboard admin
- [resources/views/dashboard/examiner.blade.php](resources/views/dashboard/examiner.blade.php) - Dashboard examinateur

### Pages examens
- [resources/views/exams/index.blade.php](resources/views/exams/index.blade.php) - Liste examens
- [resources/views/exams/show.blade.php](resources/views/exams/show.blade.php) - Détails examen

### Pages admin
- [resources/views/admin/students.blade.php](resources/views/admin/students.blade.php) - Liste étudiants
- [resources/views/admin/exams.blade.php](resources/views/admin/exams.blade.php) - Gestion examens
- [resources/views/admin/users.blade.php](resources/views/admin/users.blade.php) - Gestion utilisateurs
- [resources/views/admin/enter-results.blade.php](resources/views/admin/enter-results.blade.php) - Saisie résultats

## 🛠️ Ajouter une nouvelle traduction

### 1. Ajouter la clé dans les 3 fichiers `resources/lang/{xx}/messages.php` :

```php
// resources/lang/fr/messages.php
'my_new_key' => 'Ma nouvelle traduction en français',

// resources/lang/de/messages.php
'my_new_key' => 'Meine neue Übersetzung auf Deutsch',

// resources/lang/en/messages.php
'my_new_key' => 'My new translation in English',
```

### 2. Utiliser dans la vue :
```blade
{{ __('messages.my_new_key') }}
```

## 🧪 Test des traductions

1. Lancer le serveur :
```bash
php artisan serve
```

2. Visiter : `http://localhost:8000`

3. Cliquer sur le sélecteur de langue en haut à droite pour tester les 3 langues

4. Les pages doivent s'afficher dans la langue sélectionnée

## 📌 Clés disponibles

Toutes les clés de traduction sont dans `resources/lang/fr/messages.php`. Les catégories incluent :

- Navigation & Layout
- Titles & Headers
- Actions
- Form Labels
- Roles
- Status
- Messages
- Exam scores
- Dashboard
- Welcome/Home
- Auth
- Exams List
- Exam Details
- Admin Pages
- Result Entry

## ⚙️ Middleware SetLocale

Le middleware `\App\Http\Middleware\SetLocale` :
- Lit la locale depuis la session
- Accepte le paramètre `?lang=xx` dans l'URL
- Persiste le choix de langue en session
- Définit la locale Laravel globalement

Les locales valides sont : `fr`, `de`, `en`.

## 🔗 Exemples d'utilisation

### Dans une vue :
```blade
<h1>{{ __('messages.welcome_title') }}</h1>
<p>{{ __('messages.welcome_subtitle') }}</p>
<a href="?lang=fr">{{ __('messages.language') }}</a>
```

### Dans un contrôleur :
```php
$message = __('messages.success_created');
return back()->with('success', $message);
```

### Avec paramètres :
```blade
<p>{{ __('messages.spots_left') }} (5 places)</p>
```

## 📖 Notes importantes

- La langue par défaut est définie dans `config/app.php`
- La locale actuelle est accessible via `app()->getLocale()`
- Les traductions sont cachées en production ; ne pas oublier de run `php artisan config:cache` après déploiement
