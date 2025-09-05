#!/bin/bash

echo "🧪 Testing Docker Environment..."

# Check if Docker is running
if ! docker info > /dev/null 2>&1; then
    echo "❌ Docker is not running. Please start Docker Desktop first."
    exit 1
fi

# Check if docker-compose is available
if ! command -v docker-compose &> /dev/null; then
    echo "❌ docker-compose is not installed or not in PATH."
    exit 1
fi

# Test basic Docker commands
echo "✅ Docker is running"
echo "✅ docker-compose is available"

# Check if services are running
if docker-compose ps | grep -q "Up"; then
    echo "✅ Docker services are running"
    
    # Test web server
    echo "🌐 Testing web server..."
    if curl -s http://localhost:8080 > /dev/null; then
        echo "✅ Web server is responding on port 8080"
    else
        echo "❌ Web server is not responding on port 8080"
    fi
    
    # Test database
    echo "🗄️  Testing database connection..."
    if docker-compose exec -T mysql mysql -u med_user -pmed_password -e "SELECT 1;" 2>/dev/null; then
        echo "✅ Database connection successful"
    else
        echo "❌ Database connection failed"
    fi
    
    # Test Redis
    echo "🔴 Testing Redis connection..."
    if docker-compose exec -T redis redis-cli ping 2>/dev/null | grep -q "PONG"; then
        echo "✅ Redis connection successful"
    else
        echo "❌ Redis connection failed"
    fi
    
else
    echo "⚠️  Docker services are not running. Start them with: ./docker/start.sh"
fi

echo ""
echo "🎯 Docker environment test completed!"











