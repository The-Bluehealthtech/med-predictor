#!/bin/bash

echo "🐳 Starting Med-Predictor Docker Environment..."

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker Desktop first."
    exit 1
fi

# Create .env file from template if it doesn't exist
if [ ! -f .env ]; then
    echo "📝 Creating .env file from Docker template..."
    cp docker/env.docker .env
    echo "✅ .env file created"
fi

# Build and start services
echo "🔨 Building Docker images..."
docker-compose build --no-cache

echo "🚀 Starting services..."
docker-compose up -d

# Wait for services to be ready
echo "⏳ Waiting for services to be ready..."
sleep 10

# Check service status
echo "📊 Service Status:"
docker-compose ps

echo ""
echo "🎉 Docker environment started successfully!"
echo ""
echo "🌐 Access your application:"
echo "   - Main App: http://localhost:8080"
echo "   - Adminer (DB): http://localhost:8081"
echo "   - MailHog: http://localhost:8025"
echo ""
echo "📋 Useful commands:"
echo "   - View logs: docker-compose logs -f"
echo "   - Stop services: docker-compose down"
echo "   - Restart: docker-compose restart"
echo "   - Shell access: docker-compose exec app bash"

