# Minerva : projet de gestion de bibliothèque

But : approfondir des notions Symfony 7

pas de Docker pour l'instant (merci Windows ...)

## Installation
```shell
composer install
```

### Ajouter le .env.* avec les variables

``` dotenv
APP_SECRET=be147348de343f8abdea745b73ead9af
DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=16&charset=utf8"
MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0
MAILER_DSN=null://null
```

### Créer la bdd
```shell
symfony console doctrine:database:create
symfony console make:migration
symfony console d:m:m
```

# Workflow du projet
(Généré par DeepSeek, à corriger au fur et à mesure)


## 📚 Projet : Système de Gestion de Bibliothèque
Objectif : Une plateforme complète pour gérer les livres, les utilisateurs, les emprunts, avec des fonctionnalités avancées comme les recommandations ou les réservations.

## 🎯 Fonctionnalités Étendues
Gestion des Livres & Médias (Livres, DVDs, CDs avec héritage Doctrine).

Système d’Emprunt/Réservation (Workflow, délais, pénalités).

Recherche Avancée (Filtres, Elasticsearch ou Doctrine Search).

API REST (Pour une future app mobile via API Platform).

Dashboard Admin (Statistiques, rapports).

Notifications (Emails, SMS pour les retards).

Système de Recommandation (Basé sur l’historique des utilisateurs).

## 📅 Découpage en Sprints
Sprint 0 : Setup & Modélisation
Tâches :

Installation de Symfony 7 + AssetMapper.

Configuration de Docker (PostgreSQL, MailHog pour les emails).

Modélisation UML des entités (Livres, Utilisateurs, Emprunts, etc.).

Configuration de base de sécurité (Login, Roles Hierarchy).

Sprint 1 : Gestion des Médias & Héritage Doctrine
Modules :

Single Table Inheritance (Livre, DVD, CD).

CRUD Admin (EasyAdmin ou custom).

Upload de Fichiers (Couvertures de livres via VichUploader ou Filesystem).

Tâches :

Entité Media (abstraite) + entités enfants.

Formulaires dynamiques (champs différents selon le type).

Service de stockage des fichiers.

Sprint 2 : Système d’Emprunt & Workflow
Modules :

Workflow Component (Statuts : disponible, réservé, emprunté, retard).

Validation Custom (Un utilisateur ne peut pas emprunter 10 livres en même temps).

Calcul automatique des dates de retour.

Tâches :

Entité Borrowing (utilisateur, média, dates).

Voter pour vérifier si un utilisateur peut prolonger un emprunt.

Commande console pour notifier les retards.

Sprint 3 : Recherche & API
Modules :

Barre de recherche (Doctrine Full-Text Search ou Elasticsearch).

API Platform (Exposer les livres/emprunts en JSON-LD).

Filtres personnalisés (Par auteur, disponibilité, etc.).

Tâches :

Service SearchManager avec pagination.

Configurer les groupes de sérialisation pour l’API.

Tests fonctionnels pour l’API.

Sprint 4 : Dashboard & Statistiques
Modules :

Génération de rapports (Livres populaires, retards fréquents).

Graphiques (Chart.js ou Symfony UX sans Webpack).

Export CSV/PDF (Utilisation de Spout ou Dompdf).

Tâches :

Service StatsGenerator avec DQL avancé.

Twig Components pour les widgets réutilisables.

Sprint 5 : Notifications & Recommandations
Modules :

Système de Notifications (Emails asynchrones avec Messenger).

Recommandations ("Les utilisateurs qui ont emprunté ce livre ont aussi aimé...").

Logs Contextuels (Monolog + Slack/Sentry).

Tâches :

Service RecommendationEngine basé sur l’historique.

Événements personnalisés pour déclencher les notifications.

Sprint 6 : Optimisation & Déploiement
Modules :

Cache HTTP (Pour les pages livres populaires).

Tests E2E (Panther ou Cypress).

Déploiement (Docker + GitHub Actions).

Tâches :

Configurer Varnish ou Symfony Cache.

Scénarios de test pour les workflows complexes.