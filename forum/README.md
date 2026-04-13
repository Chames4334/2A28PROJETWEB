# Module Forum (communauté)

Application PHP MVC (sans framework) pour les tables `users`, `post` et `reply` de la base **insurance_db**. Aucune modification de schéma n’est nécessaire.

## Connexion à la base de données

1. Importez le fichier `insurance_db.sql` (à la racine du projet) dans MySQL (phpMyAdmin ou ligne de commande) si ce n’est pas déjà fait.
2. Vérifiez les paramètres dans `forum/config/database.php` :
   - **Hôte** : `127.0.0.1` (ou `localhost`)
   - **Base** : `insurance_db`
   - **Utilisateur** : `root`
   - **Mot de passe** : vide (valeur par défaut XAMPP)
3. Si votre configuration diffère, modifiez uniquement le tableau retourné par `database.php` (DSN, utilisateur, mot de passe).

## Lancer le forum dans le navigateur

Les pages PHP sont dans **`forum/views/`** (un fichier par action). Les fichiers statiques (CSS, JS) restent dans **`forum/public/`** et sont chargés en relatif (`../public/css/forum.css`).

Exemple d’URL XAMPP :

`http://localhost/Projet%20Web%202A28/forum/views/index.php`  

(adaptez le chemin selon l’emplacement du projet sous `htdocs`.)

Avec le serveur PHP intégré :

```bash
cd forum/views
php -S localhost:8080
```

Puis ouvrez `http://localhost:8080/index.php`.

## Compte utilisateur

- **Inscription** : page `register.php` — crée une ligne dans `users` avec `password_hash` et `status = active`.
- **Comptes existants** : si vous avez déjà des utilisateurs dans `users`, connectez-vous avec leur email et mot de passe (le mot de passe doit correspondre au hash stocké).
- **Test rapide (développement)** : dans `forum/config/app.php`, vous pouvez définir `FORUM_DEV_AUTO_USER_ID` sur un `id` utilisateur **existant** dans la base pour simuler une session sans login. Remettez `false` en production.

## Structure des dossiers

```
forum/
  config/       database.php, app.php
  controllers/  AuthController, PostController, ReplyController
  core/         bootstrap.php, helpers.php, app.php
  models/       User, Post, Reply
  views/        scripts d’entrée (*.php), layout/, post/, auth/
  public/       css/, js/ uniquement (assets)
```

## Fichiers d’entrée (`views/` à la racine du dossier views)

| Fichier            | Rôle                          |
|--------------------|-------------------------------|
| `index.php`        | Liste des sujets              |
| `post.php`         | Détail d’un sujet + réponses  |
| `create.php`       | Formulaire nouveau sujet      |
| `post_store.php`   | Enregistrement du sujet (POST)|
| `mine.php`         | Mes sujets                    |
| `post_edit.php`    | Édition (auteur)              |
| `post_update.php`  | Mise à jour (POST)            |
| `post_delete.php`  | Retrait du sujet — `statut` → `masque` |
| `reply_store.php`  | Nouvelle réponse (POST)       |
| `reply_delete.php` | Retrait d’une réponse — `masque` |
| `login.php` / `register.php` / `logout.php` | Authentification |

Les gabarits HTML sont dans `views/layout/`, `views/post/`, `views/auth/` et sont inclus par le contrôleur via `view()`.

## Règles métier rappelées

- Affichage : `post.statut = 'actif'`, `reply.statut = 'actif'`.
- Réponses affichées : uniquement `parent_reply_id IS NULL` (pas de fil de discussion pour l’instant).
- « Suppression » auteur : mise à jour du `statut` à **`masque`** (pas de suppression physique).
