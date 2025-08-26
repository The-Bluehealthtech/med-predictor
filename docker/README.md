# 🐳 Docker Environment for Med-Predictor

This directory contains all the Docker configuration files needed to run the Med-Predictor application in a containerized environment.

## 🚀 Quick Start

### Prerequisites
- Docker Desktop installed and running
- Docker Compose (usually included with Docker Desktop)

### Start the Environment
```bash
./docker/start.sh
```

### Stop the Environment
```bash
./docker/stop.sh
```

## 🌐 Services & Ports

| Service | Port | Description |
|---------|------|-------------|
| **Nginx** | 8080 | Web server (main application) |
| **MySQL** | 3306 | Database server |
| **Redis** | 6379 | Cache and session storage |
| **Adminer** | 8081 | Database management interface |
| **MailHog** | 8025 | Email testing interface |
| **MailHog SMTP** | 1025 | SMTP server for testing |

## 🏗️ Architecture

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Nginx (8080)  │    │  PHP-FPM (9000) │    │   MySQL (3306)  │
│   (Web Server)  │◄──►│  (Application)  │◄──►│   (Database)    │
└─────────────────┘    └─────────────────┘    └─────────────────┘
                                │
                                ▼
                       ┌─────────────────┐
                       │   Redis (6379)  │
                       │ (Cache/Session) │
                       └─────────────────┘
```

## 📁 File Structure

```
docker/
├── Dockerfile              # Multi-stage Docker image
├── docker-compose.yml      # Service orchestration
├── nginx.conf             # Nginx main configuration
├── nginx-app.conf         # Laravel-specific Nginx config
├── php.ini                # PHP configuration
├── supervisord.conf       # Process management
├── entrypoint.sh          # Container initialization
├── env.docker             # Environment template
├── start.sh               # Start script
├── stop.sh                # Stop script
├── README.md              # This file
└── mysql/
    └── init/              # Database initialization scripts
```

## 🔧 Configuration

### Environment Variables
Copy `docker/env.docker` to `.env` in the project root:
```bash
cp docker/env.docker .env
```

### Database Credentials
- **Database**: `med_predictor`
- **Username**: `med_user`
- **Password**: `med_password`
- **Root Password**: `root_password`

### Customization
Edit `docker-compose.yml` to modify:
- Port mappings
- Volume mounts
- Environment variables
- Service dependencies

## 🛠️ Development Commands

### View Logs
```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f app
docker-compose logs -f nginx
docker-compose logs -f mysql
```

### Access Container Shell
```bash
# Application container
docker-compose exec app bash

# Database container
docker-compose exec mysql mysql -u root -p

# Redis container
docker-compose exec redis redis-cli
```

### Database Operations
```bash
# Run migrations
docker-compose exec app php artisan migrate

# Seed database
docker-compose exec app php artisan db:seed

# Tinker
docker-compose exec app php artisan tinker
```

### Cache Operations
```bash
# Clear all caches
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan route:clear
docker-compose exec app php artisan view:clear
```

## 🚨 Troubleshooting

### Common Issues

#### Port Already in Use
If you get "port already in use" errors:
```bash
# Check what's using the port
lsof -i :8080

# Stop conflicting services or change ports in docker-compose.yml
```

#### Permission Issues
```bash
# Fix storage permissions
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

#### Database Connection Issues
```bash
# Check if MySQL is running
docker-compose ps mysql

# View MySQL logs
docker-compose logs mysql

# Restart MySQL
docker-compose restart mysql
```

### Reset Environment
```bash
# Stop and remove everything
docker-compose down -v

# Rebuild from scratch
docker-compose build --no-cache
docker-compose up -d
```

## 🔒 Security Notes

- Default passwords are for development only
- Never use these credentials in production
- Consider using Docker secrets for production
- Regularly update base images for security patches

## 📚 Next Steps

After setting up Docker:
1. **Configure your IDE** to use the Docker environment
2. **Set up database migrations** and seeders
3. **Configure email testing** with MailHog
4. **Set up monitoring** and logging
5. **Prepare for production deployment**

## 🤝 Contributing

When modifying Docker configuration:
1. Test changes locally first
2. Update this documentation
3. Consider backward compatibility
4. Test with different environments
