# 🔒 Enchères Secrètes

Plateforme d'enchères secrètes développée avec Symfony 7. Les utilisateurs peuvent consulter des objets et placer des offres secrètes. L'administrateur publie les objets et clôture les enchères pour désigner le gagnant.

## 🛠️ Technologies

- **Symfony 7** — Framework PHP
- **Doctrine ORM** — Gestion de la base de données
- **MySQL 8** — Base de données
- **Twig** — Moteur de templates
- **Bootstrap / Bootswatch Slate** — Design
- **Docker** — Conteneurisation

## 🚀 Installation

### Prérequis
- Docker
- Docker Compose

### Lancement

```bash
git clone https://github.com/CyrilDavid27370/encheres-secretes.git
cd encheres-secretes
docker compose up -d
docker exec -it encheres_php bash
composer install
php bin/console doctrine:migrations:migrate
```

### Accès

| Service | URL |
|---|---|
| Application | http://localhost:8080 |
| PhpMyAdmin | http://localhost:8081 |

## 👤 Comptes de test

| Rôle | Email | Mot de passe |
|---|---|---|
| Admin | admin@gmail.com | admin |
| Utilisateur | user1@gmail.com | password |
| Utilisateur | user2@gmail.com | password |

## 📋 Fonctionnalités

### Lot 1 — Consultation
- Liste des objets avec filtre par catégorie
- Recherche par titre ou description
- Détail d'un objet avec statut et catégories

### Lot 2 — Authentification
- Inscription avec validation
- Connexion / Déconnexion
- Rôles ROLE_USER et ROLE_ADMIN

### Lot 3 — Enchères
- Placer une enchère (utilisateur connecté)
- Une seule enchère par objet par utilisateur
- Offre supérieure au prix de départ obligatoire

### Lot 4 — Administration
- Publier / dépublier un objet
- Clôturer une enchère (désigne le gagnant)
- Liste des enchérisseurs visible par l'admin

### Lot 5 — CRUD Admin
- Ajouter, modifier, supprimer un objet

### Bonus
- 🔍 Recherche par titre/description
- 🔒 Protection CSRF sur les actions sensibles
- 📊 Tableau de bord admin
- 👤 Page profil utilisateur
- 🔨 Nombre d'enchères affiché sur la liste

## 👨‍💻 Auteur

**Cyril David**
Formation DWWM — Hunik Academy 2026