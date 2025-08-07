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
docker compose exec thoughtless-api git config --global --add safe.directory /var/www/html && \
docker compose exec thoughtless-api composer install && \
docker compose exec thoughtless-api php artisan key:generate && \
docker compose exec thoughtless-api php artisan migrate
```

---

### ✅ 4. Set up the testing database (for running tests)

Laravel uses a separate database called `testing` when running tests.
A helper script is included to automate the setup:

```bash
chmod +x scripts/setup-testing.sh
./scripts/setup-testing.sh
```

---

### 🧰 Useful Commands

Enter the main Laravel container shell as user Sail:

```bash
docker exec -it --user sail thoughtless-api bash
```
