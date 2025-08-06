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
# Start Docker containers (pass host UID/GID to prevent permission issues)
WWWUSER=$(id -u) WWWGROUP=$(id -g) docker compose up -d

# Install PHP dependencies inside the container
docker compose exec laravel.test composer install

# Generate the Laravel app key
docker compose exec laravel.test php artisan key:generate

# Run database migrations
docker compose exec laravel.test php artisan migrate
```

#### 🧼 Note: If you see a Git warning about “dubious ownership,” run this inside the container:

```bash
docker compose exec laravel.test git config --global --add safe.directory /var/www/html
```
