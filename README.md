# DeskApp - Démarrage rapide

## Lancer le projet (Docker)

1. Ouvre un terminal à la racine du projet
2. Lance :

    ```bash
    docker-compose up --build
    ```

3. Accède à :

    - **Backend Symfony (API)** : http://localhost:8000
    - **Frontend Nuxt.js** : http://localhost:3000

4. Pour initialiser la base de données :
    ```bash
    docker exec -it symfony-backend
    ```
    ```bash
    php bin/console doctrine:migrations:migrate
    ```
    ```bash
    php bin/console app:init-users
    ```
    ```bash
    php bin/console app:init-reservations
    ```

C'est tout !

---

## Identifiants de connexion (admin par défaut)

-   **Email** : admin@example.com
-   **Mot de passe** : password

Utilisez ces identifiants pour vous connecter en tant qu'administrateur.
