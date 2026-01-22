# PROJET_MAXIME_ALEXIS

## FONCTIONNALITES PRINCIPALES

### Gestion des articles
- Creation, modification et suppression d'articles
- Editeur WYSIWYG avec formatage riche (gras, italique, titres, listes, liens)
- Upload d'images associees aux articles
- Extraction automatique de resumes pour l'affichage en liste
- Recherche par mots-cles dans le titre et le contenu
- Filtrage par annee et mois de publication

### Systeme d'authentification
- Inscription utilisateur avec validation email
- Connexion securisee avec hash de mot de passe
- Gestion de roles (utilisateur standard, administrateur)
- Sessions securisees

### Interactions utilisateurs
- Systeme de commentaires sur les articles
- Systeme de likes avec toggle (ajouter/retirer)
- Profils utilisateurs avec statistiques
- Modification du profil (bio, informations personnelles)

### Carrousel multimedia
- Affichage de slides en page d'accueil
- Support des images (JPG, PNG, GIF, WEBP)
- Support des documents PDF avec conversion automatique
- Conversion PDF en images via Imagick (1 page = 1 slide)
- Gestion de l'ordre d'affichage et activation/desactivation
- Defilement automatique toutes les 5 secondes

### Interface d'administration
- Dashboard de gestion des articles
- Gestion des utilisateurs et des roles
- Gestion du carrousel multimedia
- Visualisation des logs systeme en temps reel
- Upload multiple de fichiers

## ARCHITECTURE TECHNIQUE

### Structure des fichiers

```
/htdocs/
├── includes/
│   ├── config.php              # Configuration base de donnees et sessions
│   └── functions.php           # Fonctions metier et utilitaires
├── admin/
│   ├── dashboard.php           # Gestion des articles et utilisateurs
│   ├── carousel.php            # Gestion du carrousel
│   └── logs.php                # Visualisation des logs
├── logs/
│   └── app.log                 # Journal des evenements systeme
├── uploads/                    # Fichiers uploades (images, PDFs convertis)
├── index.php                   # Page d'accueil avec liste articles et carrousel
├── article.php                 # Affichage article complet
├── profile.php                 # Profil utilisateur
├── login.php                   # Authentification
├── register.php                # Inscription
├── slide.php                   # Affichage slide carrousel en plein ecran
└── logout.php                  # Deconnexion
```

### Base de donnees

Structure MySQL/MariaDB avec 5 tables principales :

**user**
- id, username, email, password (hache), bio, role, created_at

**articles**
- id, title, content (HTML), image, author_id, created_at, updated_at

**comments**
- id, article_id, user_id, content, created_at

**likes**
- id, article_id, user_id, created_at

**carousel**
- id, title, description, image, position, active, created_at

## DEPENDANCES SYSTEME

### Extensions PHP requises
- pdo
- pdo_mysql
- gd
- fileinfo
- mbstring
- imagick (pour conversion PDF)

## INSTALLATION

### Prerequis
- Serveur web Apache/Nginx
- PHP 7.4 ou superieur
- MySQL/MariaDB 5.7 ou superieur
- Extension PHP Imagick installee


## FONCTIONNEMENT DETAILLE

### Conversion PDF vers images

Processus automatique lors de l'upload d'un PDF dans le carrousel :

1. Upload du fichier PDF dans dossier temporaire
2. Lecture du PDF avec Imagick
3. Conversion de chaque page en image JPEG
4. Creation d'un slide par page avec nomenclature "Titre - Page N"
5. Suppression du fichier PDF temporaire
6. Stockage des images converties dans /uploads/

### Editeur de texte enrichi

Implementation native avec execCommand() JavaScript :
- Formatage en temps reel
- Detection de l'etat des boutons (actif/inactif)
- Sauvegarde du HTML dans le champ cache avant soumission
- Support des titres H1, H2, H3
- Support des listes ordonnees et non ordonnees
- Insertion de liens

### Recherche et filtrage

Systeme de recherche combine :
- Recherche textuelle dans titre et contenu (LIKE %terme%)
- Filtrage par annee (YEAR(created_at))
- Filtrage par mois (MONTH(created_at))
- Combinaison possible des criteres (AND logique)

### Gestion des sessions

- Demarrage session au chargement de config.php
- Variables session : user_id, username, role
- Verification role admin via fonction checkAdmin()
- Redirection automatique si non autorise

## LOGS SYSTEME

Format des logs dans /htdocs/logs/app.log :
```
[YYYY-MM-DD HH:MM:SS] Message descriptif de l'action
```

Evenements logges :
- Authentification (connexion, deconnexion)
- Creation/modification/suppression articles
- Upload de fichiers
- Conversion PDF
- Erreurs de base de donnees
- Tentatives d'acces non autorise

## SECURITE

### Mesures implementees

- Validation cote serveur de tous les formulaires
- Echappement HTML systematique des donnees utilisateur
- Hachage bcrypt des mots de passe (password_hash)
- Requetes preparees PDO 
- Limitation taille fichiers (10 MB)
- Gestion des roles et permissions

## PERFORMANCES

### Limites

- Pas de systeme de cache
- Pas de pagination sur la liste des articles
- Chargement synchrone des images
- Conversion PDF bloquante (pas de file d'attente)

## LIEN

[projet-blog.free.nf](https://projet-blog.free.nf/)
