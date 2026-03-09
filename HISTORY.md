# Historique du Projet : Musician Storefront App

Ce document retrace les étapes clés et les actions réalisées par Junie pour le développement de l'application Musician Storefront.

## 1. Initialisation du Projet
- **Structure Docker** : Mise en place de l'environnement avec Nginx, PHP-FPM, MySQL, et Angular.
- **Backend PHP Slim** : Configuration du framework Slim avec Eloquent ORM.
- **Frontend Angular** : Intégration du template Fuse (v19) comme base pour l'interface utilisateur.
- **Authentification & RBAC** : Implémentation du système de connexion par JWT et gestion des rôles (Super Admin, Artiste, Client).

## 2. Infrastructure et Stockage
- **Intégration MinIO** : Configuration de MinIO comme solution de stockage S3-compatible pour le développement local.
- **Base de Données** : Création des migrations pour les tables `users`, `songs`, `orders`, `pricing_rules`, et `payment_health`.

## 3. Développement du Backend (API)
- **Authentification** : Création de l' `AuthController` pour la gestion des tokens JWT.
- **Administration** : Développement de l' `AdminController` pour la gestion globale (artistes, prix, santé des paiements).
- **Artistes** : Création de l' `ArtistController` pour les opérations spécifiques aux artistes (upload de morceaux, profil, ventes).
- **Middleware** : Mise en place du `RoleMiddleware` pour sécuriser les routes selon les permissions.

## 4. Développement du Frontend (Super Admin)
- **Tableau de bord** : Création d'un dashboard affichant les statistiques globales (artistes récents, derniers morceaux).
- **Gestion des Artistes** : Interface pour attribuer des classements (stars) aux artistes.
- **Gestion des Prix** : Interface de configuration des tarifs par niveau d'artiste et par devise.
- **Santé des Paiements** : Monitoring en temps réel des services (PayPal, CinetPay, PayDunia).
- **Modération** : Interface de gestion des morceaux (Publication, Brouillon, Archive).

## 5. Développement du Frontend (Artiste)
- **Dashboard Artiste** : Statistiques personnelles (total des ventes, morceaux publiés).
- **Gestion des Morceaux** : Interface d'upload et liste des titres personnels.
- **Profil & Paramètres** : Mise à jour des informations de l'artiste (pays d'origine requis).
- **Rapports de Ventes** : Visualisation des performances commerciales.

## 6. Améliorations de l'Expérience Utilisateur (UX)
- **Redirection Dynamique** : Mise à jour de la logique de connexion pour rediriger automatiquement vers `/admin` ou `/artist` selon le rôle.
- **Navigation** : Réorganisation de la barre latérale Fuse pour regrouper logiquement les outils par rôle.
- **Données de Test** : Création de scripts de "seeding" pour peupler l'application avec des utilisateurs, des morceaux et des statistiques de démonstration.

## 7. Utilisateurs de Test Créés
- **Super Admin** : `hughes.brian@company.com` / `Secure-Password-123$%^`
- **Super Admin (Alt)** : `superadmin2@example.com` / `admin_pass_123`
- **Artiste** : `artist_user@example.com` / `artist_pass_123`
- **Client** : `newuser@example.com` / `password123`

## 8. Nouvelles Fonctionnalités Complétées (Dernière Mise à Jour)
- **Storefront & Checkout** : Implémentation du panier d'achat, du processus de paiement et de la navigation publique.
- **Espace Client** : Création de la bibliothèque musicale permettant aux acheteurs de télécharger leurs morceaux acquis via des URLs signées S3.
- **Notifications** : Service d'envoi de reçus par email après achat réussi.
- **Rapports PDF** : Service de génération et d'exportation de rapports de ventes au format PDF pour les artistes.
- **Intégration S3** : Gestion réelle des fichiers via AWS SDK (S3 présigné).
