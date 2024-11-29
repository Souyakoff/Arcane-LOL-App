# Arcane Breaker - Jeu de Cartes en Ligne

Arcane Breaker est un jeu de cartes en ligne où les joueurs s'affrontent en utilisant des cartes représentant des personnages et des capacités spéciales. Le but est de réduire les points de vie de l'arcane de l'adversaire à zéro tout en protégeant la sienne. Le jeu propose une expérience stratégique avec des cartes à collectionner et un gameplay engageant.

---

## Fonctionnalités

- **Création de Deck** : Les joueurs peuvent composer leur propre deck avec des cartes représentant des personnages, des attaques et des capacités spéciales.
- **Affrontement** : Le jeu se joue entre deux rôles, l'attaquant et le défenseur, qui choisissent des cartes à jouer pendant leur tour.
- **Boutique** : Les joueurs peuvent acheter de nouvelles cartes pour enrichir leur collection.
- **Interface Profil** : Les utilisateurs peuvent gérer leur profil, changer leur photo de profil, et voir leur historique de jeu.
- **Règles du Jeu** : Des règles claires sont expliquées pour apprendre à jouer et maîtriser les stratégies.
  
---

## Installation

### Prérequis

- PHP 7.4 ou supérieur
- Serveur web (Apache/Nginx) avec support PHP
- Base de données MySQL ou MariaDB
- Serveur local (ex: XAMPP, WAMP) ou un hébergement en ligne

### Étapes d'installation

1. **Cloner le dépôt**

   Clone ce dépôt dans le répertoire de ton serveur local ou de ton hébergement.

   ```bash
   git clone https://github.com/ton-utilisateur/Arcane-LOL-App.git
   cd arcane-breaker
   ```

2. **Configurer la base de données**

   Crée une base de données MySQL/MariaDB et utilise le fichier `arcane_breaker.sql` pour créer les tables nécessaires.

   Exemple de commande MySQL :

   ```sql
   CREATE DATABASE arcane_breaker;
   USE arcane_breaker;
   SOURCE db.sql;
   ```

3. **Configurer la connexion à la base de données**

   Dans le fichier `db_connect.php`, modifie les informations de connexion (utilisateur, mot de passe, hôte) en fonction de ta configuration.

   ```php
   <?php
   $host = 'localhost'; // Hôte de la base de données
   $dbname = 'arcane_breaker'; // Nom de la base de données
   $username = 'root'; // Utilisateur MySQL
   $password = ''; // Mot de passe MySQL
   ```

4. **Démarrer le serveur**

   Si tu utilises un serveur local comme XAMPP, démarre Apache et MySQL. Si tu utilises un hébergement distant, déploie les fichiers sur ton serveur.

---

## Utilisation

### Se connecter

- Si tu n'as pas de compte, tu peux t'inscrire en cliquant sur **"S'inscrire"** depuis la page d'accueil.
- Si tu as déjà un compte, tu peux te connecter en utilisant tes identifiants.

### Jouer

1. **Créer un Deck** : Allez dans la section "Deck" pour composer un deck de cartes.
2. **Lancer une Partie** : Une fois ton deck prêt, tu peux commencer une partie en cliquant sur "Jouer".
3. **Acheter des Cartes** : Accède à la boutique pour acheter de nouvelles cartes et améliorer ton deck.

---

## Technologies utilisées

- **Frontend** :
  - HTML5
  - CSS3 (pour le style)
  - JavaScript (pour les interactions, gestion du jeu)
  
- **Backend** :
  - PHP (pour la gestion des utilisateurs, des sessions, et des requêtes)
  - PDO pour la connexion à la base de données

- **Base de données** :
  - MySQL ou MariaDB

---

## Contribuer

1. Fork ce projet.
2. Crée une branche pour ta fonctionnalité (`git checkout -b ma-fonctionnalite`).
3. Commit tes modifications (`git commit -am 'Ajoute une nouvelle fonctionnalité'`).
4. Push vers ta branche (`git push origin ma-fonctionnalite`).
5. Crée une pull request.

---

## Crédits

- **Développeur principal** : [Ton nom ou ton pseudonyme]
- **Design des cartes et illustrations** : [Nom des contributeurs ou sources des images utilisées]

---


### Notes supplémentaires :

- **Sécurisation des sessions utilisateur** : Le projet utilise `PHP sessions` pour gérer l'état de connexion de l'utilisateur.
- **Accessibilité** : Veuillez assurer que votre serveur supporte les fichiers PHP et les connexions à une base de données.

---
