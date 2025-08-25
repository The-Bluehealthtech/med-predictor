# 🚀 Complete FIT Service Deployment Guide

This guide will walk you through deploying your FIT (Football Intelligence & Tracking) service to production.

## 📋 Prerequisites

-   A VPS/Cloud server (DigitalOcean, AWS, etc.)
-   A domain name (optional but recommended)
-   SSH access to your server

## 🎯 Step-by-Step Deployment

### Step 1: Create Your Server

#### Option A: DigitalOcean (Recommended)

1. Go to [DigitalOcean](https://www.digitalocean.com/)
2. Create a new Droplet:
    - **Image**: Ubuntu 22.04 LTS
    - **Plan**: Basic ($6/month)
    - **Datacenter**: Choose closest to your users
    - **Authentication**: SSH Key (recommended) or Password
3. Note your server IP address

#### Option B: AWS EC2

1. Go to [AWS Console](https://aws.amazon.com/)
2. Launch an EC2 instance:
    - **AMI**: Ubuntu 22.04 LTS
    - **Instance Type**: t3.micro (free tier) or t3.small
    - **Security Group**: Allow SSH (22), HTTP (80), HTTPS (443), Custom (3000)

### Step 2: Connect to Your Server

```bash
ssh root@YOUR_SERVER_IP
```

### Step 3: Run Server Setup

```bash
# Download the setup script
wget https://raw.githubusercontent.com/izharmahjoub1/med-predictor/main/setup-server.sh

# Make it executable
chmod +x setup-server.sh

# Run the setup
./setup-server.sh
```

### Step 4: Clone Your Repository

```bash
# Switch to the fit user
su - fit

# Clone your repository
git clone https://github.com/izharmahjoub1/med-predictor.git
cd med-predictor
```

### Step 5: Configure Environment

```bash
# Copy environment template
cp src/env.example /opt/fit-service/.env

# Edit the environment file
nano /opt/fit-service/.env
```

**Configure these important variables:**

```bash
# Application Configuration
NODE_ENV=production
PORT=3000
HOST=0.0.0.0

# Security (CHANGE THESE!)
JWT_SECRET=your-super-secret-jwt-key-change-this-in-production
ENCRYPTION_KEY=your-32-character-encryption-key-change-this
ENCRYPTION_IV=your-16-character-iv-change-this

# Database Configuration
MONGODB_URI=mongodb://localhost:27017/fit_database
POSTGRES_HOST=localhost
POSTGRES_PORT=5432
POSTGRES_DB=fit_database
POSTGRES_USER=fit_user
POSTGRES_PASSWORD=fit_password

# Redis Configuration
REDIS_URL=redis://localhost:6379

# OAuth2 Configuration (Configure these with your actual credentials)
CATAPULT_CLIENT_ID=your-catapult-client-id
CATAPULT_CLIENT_SECRET=your-catapult-client-secret
APPLE_CLIENT_ID=your-apple-client-id
APPLE_CLIENT_SECRET=your-apple-client-secret
GARMIN_CLIENT_ID=your-garmin-client-id
GARMIN_CLIENT_SECRET=your-garmin-client-secret
```

### Step 6: Deploy the Application

```bash
# Run the deployment script
./deploy-production.sh
```

### Step 7: Set Up Domain and SSL (Optional)

If you have a domain name:

```bash
# Replace with your domain
sudo sed -i 's/your-domain.com/YOUR_ACTUAL_DOMAIN.com/g' /etc/nginx/sites-available/fit-service

# Get SSL certificate
sudo certbot --nginx -d YOUR_ACTUAL_DOMAIN.com

# Test nginx configuration
sudo nginx -t

# Reload nginx
sudo systemctl reload nginx
```

### Step 8: Verify Deployment

```bash
# Check service status
pm2 status

# Check health endpoint
curl http://localhost:3000/health

# View logs
pm2 logs fit-service

# Monitor system
/opt/monitor-fit.sh
```

## 🔧 Management Commands

### Service Management

```bash
# Start service
pm2 start fit-service

# Stop service
pm2 stop fit-service

# Restart service
pm2 restart fit-service

# View logs
pm2 logs fit-service

# Monitor
pm2 monit
```

### System Monitoring

```bash
# Full system status
/opt/monitor-fit.sh

# Create backup
/opt/backup-fit.sh

# View system resources
htop
```

### Database Management

```bash
# PostgreSQL
sudo -u postgres psql fit_database

# MongoDB
mongosh fit_database

# Backup databases
pg_dump -h localhost -U fit_user fit_database > backup.sql
mongodump --db fit_database --out backup/
```

## 🚨 Troubleshooting

### Service Won't Start

```bash
# Check logs
pm2 logs fit-service

# Check environment
cat /opt/fit-service/.env

# Check database connections
curl http://localhost:3000/health
```

### Database Issues

```bash
# Check PostgreSQL
sudo systemctl status postgresql

# Check MongoDB
sudo systemctl status mongod

# Check Redis
sudo systemctl status redis-server
```

### Network Issues

```bash
# Check firewall
sudo ufw status

# Check nginx
sudo nginx -t
sudo systemctl status nginx

# Check ports
sudo netstat -tlnp
```

## 🔄 Automatic Deployment (GitHub Actions)

### Set Up GitHub Secrets

1. Go to your GitHub repository
2. Navigate to Settings → Secrets and variables → Actions
3. Add these secrets:
    - `HOST`: Your server IP
    - `USERNAME`: Your server username (usually 'fit')
    - `SSH_KEY`: Your private SSH key

### Enable Automatic Deployment

Once secrets are configured, every push to the main branch will automatically deploy to your server.

## 📊 Monitoring and Alerts

### Set Up Monitoring

```bash
# Install monitoring tools
sudo apt install -y htop iotop nethogs

# Create monitoring dashboard
pm2 install pm2-server-monit
```

### Set Up Alerts

```bash
# Configure email alerts in .env
ALERT_EMAIL_ENABLED=true
ALERT_EMAIL_HOST=smtp.gmail.com
ALERT_EMAIL_USER=your-email@gmail.com
ALERT_EMAIL_PASSWORD=your-app-password
```

## 🔒 Security Checklist

-   [ ] Changed default passwords
-   [ ] Configured firewall
-   [ ] Set up SSL certificate
-   [ ] Updated environment variables
-   [ ] Configured OAuth2 credentials
-   [ ] Set up monitoring
-   [ ] Created backups
-   [ ] Tested health endpoints

## 🎉 Success!

Your FIT service is now deployed and running!

**Access your service:**

-   Local: `http://YOUR_SERVER_IP:3000`
-   With domain: `https://YOUR_DOMAIN.com`
-   Health check: `http://YOUR_SERVER_IP:3000/health`

**Next steps:**

1. Test all OAuth2 integrations
2. Configure monitoring alerts
3. Set up regular backups
4. Monitor performance
5. Update documentation

## 📞 Support

If you encounter issues:

1. Check the logs: `pm2 logs fit-service`
2. Verify configuration: `cat /opt/fit-service/.env`
3. Test health endpoint: `curl http://localhost:3000/health`
4. Check system status: `/opt/monitor-fit.sh`

---

**🎯 Your FIT service is now production-ready!**
