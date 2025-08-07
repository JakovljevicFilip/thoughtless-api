#!/bin/bash

set -e

echo "🔧 Setting up test environment with Postgres credentials from .env"

# Extract DB credentials from .env
PGUSER=$(grep DB_USERNAME .env | cut -d '=' -f2)
PGPASSWORD=$(grep DB_PASSWORD .env | cut -d '=' -f2)
DBNAME=testing

# Copy .env.testing if it doesn't exist
if [ ! -f .env.testing ]; then
  echo "📄 Copying .env to .env.testing"
  cp .env .env.testing
fi

# Inject DB credentials into .env.testing
sed -i "s/^DB_USERNAME=.*/DB_USERNAME=$PGUSER/" .env.testing
sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD=$PGPASSWORD/" .env.testing
sed -i "s/^DB_DATABASE=.*/DB_DATABASE=$DBNAME/" .env.testing

# Create the 'testing' database inside pgsql container
echo "🛢️ Creating '$DBNAME' database (ignoring error if it exists)..."
docker compose exec -e PGPASSWORD="$PGPASSWORD" pgsql psql -U "$PGUSER" -c "CREATE DATABASE $DBNAME;" || {
  echo "⚠️  Database may already exist or user lacks permission. Continuing..."
}

# Run test migrations using Sail
echo "📦 Running test migrations..."
docker compose exec thoughtless-api php artisan migrate --env=testing

echo "✅ Test environment setup complete!"
