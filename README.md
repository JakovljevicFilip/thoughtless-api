# Thoughtless API

Thoughtless API is the backend service for the Thoughtless frontend application. It is built using Laravel and runs in a Dockerized environment using Laravel Sail.

---

## 🚀 Getting Started

These instructions will get your local development environment up and running.

### 1. Clone the repository

```bash
git clone git@github.com:JakovljevicFilip/thoughtless-api.git
cd thoughtless-api
```

### 2. Copy the environment file

```bash
cp .env.example .env
```

### 3. Start the containers and install dependencies

```bash
docker network inspect thoughtless >/dev/null 2>&1 || docker network create thoughtless
docker compose up -d
docker compose exec laravel.test git config --global --add safe.directory /var/www/html
docker compose exec laravel.test composer install
docker compose exec laravel.test php artisan key:generate
docker compose exec laravel.test php artisan migrate
```

### 🛠️ Fixing Permissions (Optional)

If you create files inside the Docker container (e.g., using `sail artisan make:test`), you might not be able to edit them from your host machine.
To fix file ownership, run:

```bash
./fix-permissions.sh
```
