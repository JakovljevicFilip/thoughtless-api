# Thoughtless API

Thoughtless API is the backend service for the Thoughtless frontend application.  
It is built using **Laravel** and runs in a Dockerized environment using **Laravel Sail**.

---

## 🖥 Local Setup

Follow these steps to get your development environment up and running without build errors on a fresh install.

### 1. Clone the repository

```bash
git clone git@github.com:JakovljevicFilip/thoughtless-api.git
cd thoughtless-api
```

### 2. Copy the environment file

```bash
cp .env.example .env
```

### 3. Install Composer dependencies (before building containers)

If you have PHP + Composer locally:
```bash
composer install
```

If you don't have PHP locally:
```bash
docker run --rm -v $(pwd):/app composer install
```

---

### 4. Create shared Docker network

```bash
docker network inspect thoughtless >/dev/null 2>&1 || docker network create thoughtless
```

---

### 5. Start the containers

```bash
docker compose -f docker-compose.local.yml up -d --build
```

---

### 6. Laravel setup inside the container

```bash
docker compose -f docker-compose.local.yml exec thoughtless-api git config --global --add safe.directory /var/www/html
docker compose -f docker-compose.local.yml exec thoughtless-api php artisan key:generate
docker compose -f docker-compose.local.yml exec thoughtless-api php artisan migrate
```

---

### ✅ 7. Set up the testing database (for running tests)

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

### 1. Pull latest code & checkout branch

```bash
cd /path/to/thoughtless-api
git fetch --all
git checkout pfe-23
git pull origin pfe-23
```

### 2. Install Composer dependencies

```bash
docker run --rm -v $(pwd):/app composer install --no-dev --optimize-autoloader
```

---

### 3. Build and start containers

```bash
docker compose -f docker-compose.prod.yml up -d --build
```

---

### 4. Laravel production setup

```bash
docker compose -f docker-compose.prod.yml exec thoughtless-api php artisan key:generate
docker compose -f docker-compose.prod.yml exec thoughtless-api php artisan migrate --force
docker compose -f docker-compose.prod.yml exec thoughtless-api php artisan config:cache
docker compose -f docker-compose.prod.yml exec thoughtless-api php artisan route:cache
docker compose -f docker-compose.prod.yml exec thoughtless-api php artisan view:cache
```

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
