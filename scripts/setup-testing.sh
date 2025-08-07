#!/bin/bash

set -e

echo "🔧 Setting up test environment with default Postgres credentials"

PGUSER=postgres
PGPASSWORD=password
DBNAME=testing

# Copy .env.testing if it doesn't exist
if [ ! -f .env.testing ]; then
  echo "📄 Copying .env to .env.testing"
  cp .env .env.testing
fi

# Inject DB credentials into .env.testing
grep -q '^DB_USERNAME=' .env.testing && \
  sed -i "s/^DB_USERNAME=.*/DB_USERNAME=$PGUSER/" .env.testing || \
  echo "DB_USERNAME=$PGUSER" >> .env.testing

grep -q '^DB_PASSWORD=' .env.testing && \
  sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD=$PGPASSWORD/" .env.testing || \
  echo "DB_PASSWORD=$PGPASSWORD" >> .env.testing

grep -q '^DB_DATABASE=' .env.testing && \
  sed -i "s/^DB_DATABASE=.*/DB_DATABASE=$DBNAME/" .env.testing || \
  echo "DB_DATABASE=$DBNAME" >> .env.testing

# Create the 'testing' database inside pgsql container
echo "🛢️ Creating 'testing' database (ignoring error if it exists)..."
docker compose exec -e PGPASSWORD="$PGPASSWORD" pgsql psql -U "$PGUSER" -c "CREATE DATABASE $DBNAME;" || {
  echo "⚠️  Database may already exist. Continuing..."
}

# Run test migrations
echo "📦 Running test migrations..."
./vendor/bin/sail artisan migrate --env=testing

echo "✅ Test environment setup complete!"
