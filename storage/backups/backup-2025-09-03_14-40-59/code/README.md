# 🏈 Med-Predictor - Complete Football Management Platform

A comprehensive football management platform built with Laravel, Vue.js, and Node.js microservices for player management, health tracking, performance analysis, and FIFA integration.

## 🚀 Platform Overview

Med-Predictor is a complete football management solution that combines:

-   **🏥 Medical Predictions & Health Records**
-   **⚽ Player Management & Registration**
-   **🏆 Competition & League Management**
-   **📊 Performance Analytics & AI Insights**
-   **🔗 FIFA Connect Integration**
-   **📱 FIT Microservice** (Biometric & GPS Data)
-   **🔄 Transfer System & Document Management**
-   **👥 User Management & Role-Based Access**
-   **🌐 Multi-language Support** (English/French)

## 🏗️ Architecture

```
Med-Predictor Platform
├── 🎨 Laravel Backend (PHP 8.1+)
│   ├── RESTful APIs
│   ├── Database Management
│   ├── Authentication & Authorization
│   └── Business Logic
├── ⚛️ Vue.js Frontend
│   ├── Modern UI Components
│   ├── Real-time Updates
│   ├── Progressive Web App
│   └── Responsive Design
├── 🔧 FIT Microservice (Node.js)
│   ├── Biometric Data Processing
│   ├── GPS Tracking
│   ├── OAuth2 Integrations
│   └── Real-time Analytics
└── 🗄️ Multi-Database Support
    ├── PostgreSQL (Main Data)
    ├── MongoDB (Analytics)
    └── Redis (Caching)
```

## ✨ Key Features

### 🏥 Medical & Health Management

-   **AI-Powered Medical Predictions**
-   **Health Records Management**
-   **Medical History Tracking**
-   **Injury Prevention Analytics**
-   **Biometric Data Integration**

### ⚽ Player Management

-   **Player Registration System**
-   **License Management**
-   **Performance Tracking**
-   **Health Records**
-   **Document Management**

### 🏆 Competition Management

-   **League & Championship Management**
-   **Fixture Generation**
-   **Match Sheet System**
-   **Standings & Rankings**
-   **Team Management**

### 🔗 FIFA Integration

-   **FIFA Connect API Integration**
-   **Data Synchronization**
-   **Player ID Management**
-   **Real-time Updates**

### 📊 Analytics & AI

-   **Performance Analytics**
-   **AI Insights & Recommendations**
-   **Predictive Analytics**
-   **Real-time Dashboards**

### 🔄 Transfer System

-   **Transfer Management**
-   **Document Processing**
-   **Payment Tracking**
-   **Contract Management**

### 👥 User Management

-   **Role-Based Access Control**
-   **Multi-language Support**
-   **Account Requests**
-   **Audit Trail**

### 📱 FIT Microservice

-   **Biometric Data Processing**
-   **GPS Tracking**
-   **OAuth2 Integrations** (Catapult, Apple HealthKit, Garmin)
-   **Real-time Analytics**

## 🛠️ Technology Stack

### Backend

-   **Laravel 10** - PHP Framework
-   **PHP 8.1+** - Server Language
-   **PostgreSQL** - Primary Database
-   **Redis** - Caching & Sessions
-   **MongoDB** - Analytics Data

### Frontend

-   **Vue.js 3** - Progressive Framework
-   **Vite** - Build Tool
-   **Tailwind CSS** - Styling
-   **Alpine.js** - Interactive Components
-   **Pinia** - State Management

### Microservices

-   **Node.js 18+** - FIT Service
-   **Express.js** - API Framework
-   **PM2** - Process Management
-   **OAuth2** - Authentication

### DevOps

-   **Docker** - Containerization
-   **Nginx** - Web Server
-   **GitHub Actions** - CI/CD
-   **PM2** - Process Management

## 📋 Prerequisites

### For Development

-   **PHP 8.1+**
-   **Node.js 18+**
-   **Composer**
-   **PostgreSQL 15+**
-   **Redis 7+**
-   **MongoDB 6.0+** (for FIT service)

### For Production

-   **Ubuntu 22.04 LTS**
-   **Nginx**
-   **SSL Certificate**
-   **Domain Name** (optional)

## 🚀 Quick Start

### 1. Clone Repository

```bash
git clone https://github.com/izharmahjoub1/med-predictor.git
cd med-predictor
```

### 2. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Install FIT service dependencies
cd src && npm install
```

### 3. Environment Setup

```bash
# Copy environment files
cp .env.example .env
cp src/env.example src/.env

# Generate Laravel key
php artisan key:generate
```

### 4. Database Setup

```bash
# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Create storage link
php artisan storage:link
```

### 5. Start Development Servers

```bash
# Start Laravel development server
php artisan serve

# Start FIT microservice (in another terminal)
cd src && npm run dev

# Build frontend assets
npm run dev
```

## 🌐 Access Points

-   **Main Application**: http://localhost:8000
-   **FIT Microservice**: http://localhost:3000
-   **API Documentation**: http://localhost:8000/api/docs
-   **Health Check**: http://localhost:3000/health

## 📚 Documentation

### 📖 User Guides

-   [Player Registration Guide](docs/player-registration.md)
-   [Medical Predictions Guide](docs/medical-predictions.md)
-   [Competition Management Guide](docs/competition-management.md)
-   [FIFA Integration Guide](docs/fifa-integration.md)

### 🔧 Technical Documentation

-   [API Documentation](docs/api-documentation.md)
-   [Database Schema](docs/database-schema.md)
-   [Deployment Guide](DEPLOYMENT_COMPLETE_GUIDE.md)
-   [FIT Service Documentation](docs/fit-service.md)

### 🚀 Deployment

-   [Production Deployment](DEPLOYMENT_COMPLETE_GUIDE.md)
-   [Docker Deployment](docs/docker-deployment.md)
-   [Server Setup](setup-server.sh)

## 🔧 Configuration

### Environment Variables

#### Laravel (.env)

```bash
APP_NAME="Med-Predictor"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=med_predictor
DB_USERNAME=your_user
DB_PASSWORD=your_password

REDIS_HOST=localhost
REDIS_PASSWORD=null
REDIS_PORT=6379

FIFA_API_KEY=your-fifa-api-key
FIFA_API_SECRET=your-fifa-api-secret
```

#### FIT Service (src/.env)

```bash
NODE_ENV=production
PORT=3000
HOST=0.0.0.0

MONGODB_URI=mongodb://localhost:27017/fit_database
POSTGRES_HOST=localhost
POSTGRES_DB=fit_database
REDIS_URL=redis://localhost:6379

JWT_SECRET=your-jwt-secret
ENCRYPTION_KEY=your-encryption-key
ENCRYPTION_IV=your-encryption-iv

CATAPULT_CLIENT_ID=your-catapult-client-id
CATAPULT_CLIENT_SECRET=your-catapult-client-secret
```

## 🚀 Production Deployment

### Option 1: Complete Server Setup

```bash
# On your production server
wget https://raw.githubusercontent.com/izharmahjoub1/med-predictor/main/setup-server.sh
chmod +x setup-server.sh
./setup-server.sh
```

### Option 2: Docker Deployment

```bash
# Build and run with Docker
docker-compose up -d
```

### Option 3: GitHub Actions (Automatic)

Configure GitHub Secrets and push to main branch for automatic deployment.

## 📊 Monitoring & Management

### Service Management

```bash
# Laravel Application
php artisan serve
php artisan queue:work
php artisan schedule:run

# FIT Microservice
pm2 start ecosystem.config.js
pm2 status
pm2 logs fit-service
```

### Database Management

```bash
# PostgreSQL
sudo -u postgres psql med_predictor

# MongoDB
mongosh fit_database

# Redis
redis-cli
```

### Monitoring Commands

```bash
# System status
/opt/monitor-fit.sh

# Create backup
/opt/backup-fit.sh

# Health checks
curl http://localhost:8000/health
curl http://localhost:3000/health
```

## 🔒 Security Features

-   **JWT Authentication**
-   **Role-Based Access Control (RBAC)**
-   **Two-Factor Authentication (2FA)**
-   **API Rate Limiting**
-   **CORS Protection**
-   **SQL Injection Prevention**
-   **XSS Protection**
-   **CSRF Protection**
-   **Encrypted Data Storage**

## 🌍 Internationalization

-   **Multi-language Support** (English/French)
-   **Localized Content**
-   **RTL Support Ready**
-   **Timezone Management**

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🆘 Support

-   **Documentation**: [Complete Guide](DEPLOYMENT_COMPLETE_GUIDE.md)
-   **Issues**: [GitHub Issues](https://github.com/izharmahjoub1/med-predictor/issues)
-   **Email**: support@med-predictor.com

## 🏆 Project Status

-   ✅ **Core Platform**: Complete
-   ✅ **FIT Microservice**: Complete
-   ✅ **FIFA Integration**: Complete
-   ✅ **Medical Predictions**: Complete
-   ✅ **Player Management**: Complete
-   ✅ **Competition Management**: Complete
-   ✅ **Transfer System**: Complete
-   ✅ **Multi-language**: Complete
-   ✅ **Security**: Complete
-   ✅ **Documentation**: Complete
-   ✅ **Deployment**: Complete

---

**🎯 Med-Predictor - The Complete Football Management Solution**
