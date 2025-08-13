# Thoughtless API

Thoughtless API is the backend service for the Thoughtless frontend application.  
It is built using Laravel and runs in a Dockerized environment using Laravel Sail.

---

## 🚀 Local Setup

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

> **⚠️ Reminder:** Open `.env` and set the proper values for your local environment before starting the app.

### 3. Start the containers and install dependencies

```bash
docker network inspect thoughtless >/dev/null 2>&1 || docker network create thoughtless
docker compose -f docker-compose.local.yml up -d --build
docker compose -f docker-compose.local.yml exec thoughtless-api git config --global --add safe.directory /var/www/html && docker compose -f docker-compose.local.yml exec thoughtless-api composer install && docker compose -f docker-compose.local.yml exec thoughtless-api php artisan key:generate && docker compose -f docker-compose.local.yml exec thoughtless-api php artisan migrate
```

### 4. Set up the testing database (for running tests)

Laravel uses a separate database called `testing` when running tests.  
A helper script is included to automate the setup:

```bash
chmod +x scripts/setup-testing.sh
./scripts/setup-testing.sh
```

---

## 🌐 Server Setup

These steps are for deploying Thoughtless API on a remote server.

### 1. Clone the repository

```bash
git clone git@github.com:JakovljevicFilip/thoughtless-api.git
cd thoughtless-api
```

### 2. Copy the environment file

```bash
cp .env.example .env
```

> **⚠️ Reminder:** Open `.env` and set the proper values for your production environment before starting the app.

### 3. Start the containers and install dependencies

```bash
docker network inspect thoughtless >/dev/null 2>&1 || docker network create thoughtless
docker compose -f docker-compose.prod.yml up -d --build
docker compose -f docker-compose.prod.yml exec thoughtless-api git config --global --add safe.directory /var/www/html && docker compose -f docker-compose.prod.yml exec thoughtless-api composer install --no-dev --optimize-autoloader && docker compose -f docker-compose.prod.yml exec thoughtless-api php artisan key:generate && docker compose -f docker-compose.prod.yml exec thoughtless-api php artisan migrate --force
```

---

## 🧰 Useful Commands

Enter the main Laravel container shell as user Sail:

```bash
docker exec -it --user sail thoughtless-api bash
```
