# Thoughtless API

Thoughtless API is the backend service for the Thoughtless frontend application. It is built using Laravel and runs in a Dockerized environment using Laravel Sail.

---

## 🚀 Getting Started

These instructions will get your local development environment up and running.

### 1. Clone the repository:

```bash
git clone git@github.com:JakovljevicFilip/thoughtless-api.git
cd thoughtless-api
```

### 2. Copy the environment file

```bash
cp .env.example .env
```

### 3. Start the containers:

```bash
docker compose up -d
docker compose exec laravel.test composer install
docker compose exec laravel.test php artisan key:generate
docker compose exec laravel.test php artisan migrate
```

---
