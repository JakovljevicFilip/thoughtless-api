# Thoughtless API

Thoughtless API is the backend service for the Thoughtless frontend application.  
It is built using **Laravel** and runs in a Dockerized environment using **Laravel Sail**.

---

## 📦 Common Steps (Local & Server)

Follow these steps first for **any** environment.

### 1. Clone the repository

```bash
git clone git@github.com:JakovljevicFilip/thoughtless-api.git
cd thoughtless-api
```

### 2. Copy the environment file

```bash
cp .env.example .env
```

---

## 🖥 Local Setup

These steps prepare your local development environment without build errors on a fresh install.

### 1. Install Composer dependencies

If you **don’t have PHP locally**:
```bash
docker run --rm -v $(pwd):/app composer install
```

If you **do have PHP locally**:
```bash
composer install
```

---

### 2. Create shared Docker network

This allows Thoughtless API to communicate with the Thoughtless frontend during development:

```bash
docker network inspect thoughtless >/dev/null 2>&1 || docker network create thoughtless
```

---

### 3. Start the containers

```bash
docker compose -f docker-compose.local.yml up -d --build
```

---

### 4. Laravel setup inside the container

```bash
docker compose -f docker-compose.local.yml exec thoughtless-api git config --global --add safe.directory /var/www/html
docker compose -f docker-compose.local.yml exec thoughtless-api php artisan key:generate
docker compose -f docker-compose.local.yml exec thoughtless-api php artisan migrate
```

---

### ✅ 5. Set up the testing database (for running tests)

```bash
chmod +x scripts/setup-testing.sh
./scripts/setup-testing.sh
```

---

### 🧰 Useful Commands

Enter Laravel container shell as **sail** user:
```bash
docker exec -it --user sail thoughtless-api bash
```

---

## 🌐 Server Setup

Follow these steps to deploy Thoughtless API on the server.

### 1. Install Composer dependencies

```bash
docker run --rm -v $(pwd):/app composer install --no-dev --optimize-autoloader
```

---

### 2. Build and start containers

```bash
docker compose -f docker-compose.prod.yml up -d --build
```

---
### 3. Fix File Permissions

When deploying as a **non-root user** (e.g., `deploy`), you must ensure the PHP process (running as `www-data`) can write to Laravel’s `storage/` and `bootstrap/cache/` directories.

Run the following commands after cloning or pulling updates:

```bash
docker compose -f docker-compose.prod.yml exec thoughtless-api chown -R www-data:www-data storage bootstrap/cache
docker compose -f docker-compose.prod.yml exec thoughtless-api chmod -R ug+rwx storage bootstrap/cache
```

### 4. Laravel production setup

```bash
docker compose -f docker-compose.prod.yml exec thoughtless-api php artisan key:generate
docker compose -f docker-compose.prod.yml exec thoughtless-api php artisan migrate --force
docker compose -f docker-compose.prod.yml exec thoughtless-api php artisan config:cache
docker compose -f docker-compose.prod.yml exec thoughtless-api php artisan route:cache
docker compose -f docker-compose.prod.yml exec thoughtless-api php artisan view:cache
```

---

> ⚠️ This step is **not needed when running as root**, but required when deploying as another user.

---

### 🔍 Useful Production Commands

Check container logs:
```bash
docker compose -f docker-compose.prod.yml logs -f thoughtless-api
```

Restart container:
```bash
docker compose -f docker-compose.prod.yml restart thoughtless-api
```

Enter Laravel container shell as **sail** user:
```bash
docker exec -it --user sail thoughtless-api bash
```
