# Wikicampers_projet

C'est un projet basé sur Symfony 7, pour gerer un service de location de véhicules. Ce système inclut (Créer, Modifier, Supprimer et reserver) pour la gestion et la réservation de véhicules.

## Prérequis

- PHP >= 7.4
- Symfony >= 4.4
- Composer
- liip/imagine-bundle
    ```
    composer require liip/imagine-bundle
    ```

## Installation

1. Cloner le dépôt :

    ```bash
    git clone https://github.com/omerAlzain/wikicampers_projet.git
    cd wikicampers
    ```

2. Installer les dépendances backend :

    ```bash
    composer install
    ```


3. Configurer la base de données dans le fichier `.env` :
    ### Si vous utilisez MySql
    ```dotenv
    DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name
    ```

    ### Si vous utilisez PostgreSQL
    ```dotenv
    DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=16&charset=utf8"
    ```


4. Créer la base de données et exécuter les migrations :

    ```bash
    php bin/console doctrine:database:create
    php bin/console make:migration 
    php bin/console doctrine:migrations:migrate
    ```

5. Démarrer le serveur Symfony :

    ```bash
    symfony server:start
    ```

## Pour acceder:

Vous pouvez maintenant accéder au site via l'adresse suivante : http://127.0.0.1:8000/
