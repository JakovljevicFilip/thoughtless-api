#!/bin/bash

# fix-permissions.sh
# Run this to fix file ownership if files were created inside the Docker container.

echo "🔧 Fixing file ownership..."

HOST_UID=$(id -u)
HOST_GID=$(id -g)

# Apply ownership recursively to the current directory
sudo chown -R "$HOST_UID:$HOST_GID" .

echo "✅ Ownership fixed. All files now belong to: $USER"
