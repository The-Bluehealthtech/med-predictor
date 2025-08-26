#!/bin/bash

echo "🛑 Stopping Med-Predictor Docker Environment..."

# Stop and remove containers
echo "⏹️  Stopping containers..."
docker-compose down

# Remove volumes (optional - uncomment if you want to clear data)
# echo "🗑️  Removing volumes..."
# docker-compose down -v

echo "✅ Docker environment stopped successfully!"
echo ""
echo "💡 To start again, run: ./docker/start.sh"
