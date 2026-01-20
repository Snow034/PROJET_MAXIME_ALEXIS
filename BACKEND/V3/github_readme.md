# Blog PHP - Système de gestion d'articles

Système de blog développé en PHP natif avec MySQL, déployé sur InfinityFree.

## Fonctionnalités

### Gestion des utilisateurs
- Inscription avec validation (username unique, email unique, mot de passe minimum 6 caractères)
- Connexion et déconnexion avec gestion de sessions
- Système de rôles : User et Admin
- Biographie utilisateur modifiable
- Page profil avec statistiques d'activité

### Gestion des articles
- CRUD complet des articles (titre, contenu, image)
- Upload d'images avec validation MIME, extensions autorisées et limite de 5MB
- Modification d'articles avec remplacement d'image optionnel
- Suppression d'articles avec nettoyage automatique des images associées
- Affichage public des articles par ordre chronologique décroissant

### Système de commentaires
- Ajout de commentaires sur les articles par utilisateurs authentifiés
- Affichage chronologique des commentaires avec auteur et date
- Suppression de commentaires par l'auteur ou un administrateur
- Compteur de commentaires par article

### Système de likes
- Like et unlike sur les articles
- Contrainte unique en base de données : un like par utilisateur par article
- Compteur de likes visible publiquement
- Liste des articles aimés accessible depuis le profil utilisateur

### Panel administrateur
- Dashboard réservé aux administrateurs avec vérification de rôle
- Interface de gestion des articles (création, modification, suppression)
- Gestion des utilisateurs avec modification des rôles
- Système de logs en temps réel avec rafraîchissement automatique toutes les 3 secondes
- Interface masquable pour les logs
- Bouton de vidage des logs
- Script d'installation automatique des tables manquantes

### Sécurité
- Protection XSS via htmlspecialchars sur toutes les sorties
- Requêtes SQL préparées avec PDO
- Validation stricte des uploads : vérification extension et type MIME réel
- Hashage bcrypt des mots de passe via password_hash
- Vérification systématique des permissions selon les rôles
- Sessions PHP natives avec vérification d'état
- .htaccess avec protection contre injections SQL et traversée de répertoires

### Profil utilisateur
- Statistiques : nombre d'articles écrits, commentaires postés, likes donnés
- Liste complète des articles écrits par l'utilisateur
- Liste des articles aimés avec date de like
- Historique des 10 derniers commentaires
- Modification de biographie

### Système de logs
- Enregistrement horodaté de toutes les actions critiques
- Fichier de logs avec rotation possible
- Interface de visualisation temps réel dans le panel admin
- Logs incluent les titres d'articles au lieu des IDs pour meilleure lisibilité
- Actions loguées : connexions, déconnexions, CRUD articles, commentaires, likes, modifications de rôles

## Structure de la base de données

### Table user
- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- username (VARCHAR(50), NOT NULL)
- email (VARCHAR(100), NOT NULL, UNIQUE)
- password (VARCHAR(255), NOT NULL)
- bio (TEXT, NULL)
- role (ENUM 'user'|'admin', DEFAULT 'user')
- created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)

### Table articles
- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- title (VARCHAR(255), NOT NULL)
- content (TEXT, NOT NULL)
- image (VARCHAR(255), NULL)
- author_id (INT, FOREIGN KEY vers user.id, CASCADE)
- created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
- updated_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP ON UPDATE)

### Table comments
- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- article_id (INT, FOREIGN KEY vers articles.id, CASCADE)
- user_id (INT, FOREIGN KEY vers user.id, CASCADE)
- comment (TEXT, NOT NULL)
- created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)

### Table likes
- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- article_id (INT, FOREIGN KEY vers articles.id, CASCADE)
- user_id (INT, FOREIGN KEY vers user.id, CASCADE)
- created_at (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)
- UNIQUE (article_id, user_id)

## Architecture des fichiers

```
/htdocs/
├── includes/
│   ├── config.php          - Configuration PDO, sessions, fuseau horaire, création dossiers
│   └── functions.php       - Fonctions utilitaires (checkAdmin, uploadImage, validation)
├── admin/
│   ├── dashboard.php       - Panel administration principal
│   ├── logs.php           - Visualisation logs avec auto-refresh AJAX
│   ├── install_tables.php - Installation automatique tables BDD
│   └── check_structure.php - Vérification et correction structure BDD
├── uploads/               - Stockage images uploadées
├── logs/                  - Fichiers de logs applicatifs
│   └── app.log
├── index.php             - Page d'accueil publique avec liste articles
├── login.php             - Authentification utilisateur
├── register.php          - Inscription avec biographie optionnelle
├── logout.php            - Déconnexion et destruction session
├── article.php           - Vue détaillée article, commentaires, système de likes
├── profile.php           - Profil utilisateur avec statistiques complètes
├── .htaccess            - Configuration Apache et règles de sécurité
└── schema.sql           - Script SQL de création complète BDD
```

## Technologies

- PHP 7.4+ (natif, sans framework)
- MySQL 5.7+ avec PDO
- Apache (serveur web)
- Sessions PHP natives
- Fonctions natives PHP : password_hash, password_verify, move_uploaded_file

## Installation

### Étape 1 : Base de données
Exécuter schema.sql dans phpMyAdmin pour créer toutes les tables et relations.

### Étape 2 : Configuration
Modifier includes/config.php avec les identifiants de connexion MySQL.

### Étape 3 : Permissions
Créer les dossiers uploads/ et logs/ avec permissions 755.

### Étape 4 : Déploiement
Uploader tous les fichiers vers /htdocs/ via FTP.

### Étape 5 : Compte administrateur
Compte par défaut créé via schema.sql :
- Email : admin@example.com
- Mot de passe : admin123

## Configuration

### Fuseau horaire
PHP : Europe/Paris (date_default_timezone_set)
MySQL : +01:00 (SET time_zone)

### Limites upload
- Taille maximale : 5MB par fichier
- Extensions autorisées : jpg, jpeg, png, gif, webp
- Validation MIME et extension

### Auto-refresh logs
Intervalle : 3000ms (3 secondes)
Méthode : AJAX fetch API

## Corrections de bugs

### Erreurs 500 résolues
- Ajout session_start() en première ligne de config.php
- Initialisation variable globale $LOGS dans config.php
- Création fonction log_msg() dans config.php
- Création automatique des dossiers uploads/ et logs/ si absents
- Correction des chemins relatifs avec __DIR__
- Gestion erreur PDO avec try-catch systématique

### Problèmes SQL corrigés
- Création script check_structure.php pour détecter colonne 'content' vs 'description'
- Création script install_tables.php pour tables comments et likes manquantes
- Ajout colonne bio à table user
- Configuration timezone MySQL via PDO::exec

### Optimisations
- Logs affichent titres d'articles au lieu des IDs numériques
- Récupération du titre avant suppression d'article pour logs
- Suppression automatique fichier image lors de suppression/modification article
- Vérification double upload (extension + finfo MIME)
- Conservation des données de formulaire en cas d'erreur validation

## Sécurité implémentée

### Mesures en place
- display_errors Off en production
- Requêtes SQL préparées PDO (aucune concaténation)
- htmlspecialchars sur toutes sorties HTML
- password_hash avec PASSWORD_DEFAULT (bcrypt)
- Vérification permissions via checkAdmin() et checkLogin()
- Validation stricte uploads : extension, MIME, taille
- Sessions avec vérification session_status()
- .htaccess : blocage injections SQL, traversée répertoires, listage dossiers

### .htaccess
- Désactivation display_errors et register_globals
- Blocage requêtes contenant union, select, insert, etc.
- Blocage traversée répertoires (.., %2e%2e)
- Protection fichiers sensibles (.htaccess, config.php)
- Headers sécurité : X-Content-Type-Options, X-Frame-Options, X-XSS-Protection
- Compression GZIP
- Cache fichiers statiques

## Système de logs

### Emplacement
- Interface : /admin/logs.php (admin uniquement)
- Fichier : /logs/app.log

### Actions enregistrées
- Authentification : connexions réussies/échouées, déconnexions
- Articles : création, modification (avec titre), suppression (avec titre)
- Commentaires : ajout, suppression
- Likes : like, unlike (avec titre article)
- Utilisateurs : création compte, modification rôle, mise à jour profil
- Système : création tables BDD, erreurs SQL

### Format
```
[YYYY-MM-DD HH:MM:SS] Message descriptif
```

## Notes techniques

### Séparation des préoccupations
Le code PHP est fourni sans CSS. La structure HTML est sémantique et prête pour intégration design.

### Gestion des erreurs
Toutes les opérations PDO sont encapsulées dans try-catch avec logging des exceptions.

### Validation des données
- Côté serveur : validation systématique POST/GET/FILES avec isset() et trim()
- Côté client : attributs HTML5 (required, minlength, type="email")
- Double validation upload : extension fichier + type MIME via finfo

### Performance
- Requêtes optimisées avec index sur colonnes fréquemment utilisées
- LIMIT sur requêtes commentaires (10 derniers)
- Utilisation FETCH_ASSOC pour économie mémoire
- CASCADE sur foreign keys pour intégrité référentielle

## Compte par défaut

Utilisateur administrateur créé via schema.sql :
- Username : admin
- Email : admin@example.com  
- Password : admin123 (hash bcrypt stocké)
- Role : admin

Ce compte doit être modifié ou supprimé après première connexion en production.