# SnowTricks - Projet Symfony

SnowTricks est un projet Symfony visant à créer une communauté en ligne dédiée aux passionnés de snowboard. Cette plateforme permet aux utilisateurs de partager des vidéos et des photos de leurs figures.

## Table des matières

- [Prérequis](#prérequis)
- [Installation](#installation)
- [Chargement des Fixtures](#chargement-des-fixtures)
- [Utilisation](#utilisation)
- [Connexion](#connexion)

## Prérequis

Avant de commencer, assurez-vous d'avoir les éléments suivants installés :

- PHP >= 7.2.5
- Composer (https://getcomposer.org/)
- Symfony CLI (https://symfony.com/download)
- Une base de données compatible (par exemple, MySQL)

## Installation

1. Clonez ce dépôt GitHub dans le répertoire de votre choix :

   ```bash
   git clone https://github.com/BenDejardin/SnowTricks.git
   ```

2. Accédez au répertoire du projet :

   ```bash
   cd SnowTricks
   ```

3. Installez les dépendances Symfony avec Composer :

   ```bash
   composer install
   ```

## Chargement des Fixtures

Pour peupler votre base de données avec des données de test, vous pouvez charger les fixtures avec Symfony CLI :

```bash
symfony console doctrine:fixtures:load
```

Suivez les instructions pour confirmer le chargement des fixtures.

## Utilisation

Maintenant que votre application est installée et que les données de test sont chargées, vous pouvez la démarrer en utilisant le serveur de développement Symfony CLI :

```bash
symfony server:start
```

Accédez à votre application dans votre navigateur en utilisant l'URL fournie par Symfony CLI (par défaut : http://127.0.0.1:8000).

## Connexion

Pour vous connecter, un utilisateur par défaut a été créé avec les informations suivantes :
- Email : bendejardin8@gmail.com
- Mot de passe : bendejardin8

Vous pouvez utiliser ces informations pour vous connecter à l'application et commencer à partager vos vidéos et photos de snowboard.
