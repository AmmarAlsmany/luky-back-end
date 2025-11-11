# 🚀 LUKY BACKEND - PRODUCTION DEPLOYMENT GUIDE

## 📦 What's Included

Your project now has everything needed for production deployment:

### Configuration Files
- `.env.production` - Production environment template
- `nginx.conf` - Nginx web server configuration
- `supervisor.conf` - Queue worker configuration
- `DEPLOYMENT_CHECKLIST.md` - Comprehensive deployment guide

### Scripts
- `deploy-production.sh` - Automated deployment script
- `backup.sh` - Automated backup script

---

## ⚡ QUICK START - DEPLOYMENT IN 10 STEPS

### 1. Prepare Your Server
```bash
# Install required packages
sudo apt update
sudo apt install -y nginx postgresql redis-server supervisor php8.3-fpm composer nodejs npm

# Install PHP extensions
sudo apt install -y php8.3-cli php8.3-pgsql php8.3-mbstring php8.3-xml php8.3-bcmath php8.3-curl php8.3-zip php8.3-gd php8.3-redis
```

### 2. Setup Database
```bash
sudo -u postgres psql
CREATE DATABASE luky_production;
CREATE USER luky_user WITH ENCRYPTED PASSWORD 'your-strong-password';
GRANT ALL PRIVILEGES ON DATABASE luky_production TO luky_user;
\q
```

### 3. Clone & Setup Project
```bash
cd /var/www
git clone https://github.com/your-username/luky-backend.git
cd luky-backend
cp .env.production .env
```

### 4. Configure Environment
Edit `.env` file with your production settings:
```bash
nano .env
```

**Critical settings to update:**
- `APP_URL` - Your domain (https://your-domain.com)
- `DB_PASSWORD` - Your database password
- `MYFATOORAH_API_KEY` - Your LIVE MyFatoorah API key
- `MYFATOORAH_API_URL` - https://api.myfatoorah.com (LIVE URL)
- `FCM_SERVER_KEY` - Your Firebase server key
- `FCM_CREDENTIALS_PATH` - Path to Firebase JSON file

### 5. Install Dependencies & Build
```bash
composer install --no-dev --optimize-autoloader
npm ci --production
npm run build
```

### 6. Setup Application
```bash
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 7. Set Permissions
```bash
sudo chown -R www-data:www-data /var/www/luky-backend
sudo chmod -R 755 /var/www/luky-backend
sudo chmod -R 775 /var/www/luky-backend/storage
sudo chmod -R 775 /var/www/luky-backend/bootstrap/cache
sudo chmod 600 /var/www/luky-backend/.env
```

### 8. Configure Nginx
```bash
# Copy and edit Nginx config
sudo cp nginx.conf /etc/nginx/sites-available/luky-backend
sudo nano /etc/nginx/sites-available/luky-backend  # Update domain name

# Enable site
sudo ln -s /etc/nginx/sites-available/luky-backend /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 9. Setup SSL Certificate
```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com -d www.your-domain.com
```

### 10. Setup Queue Workers
```bash
# Copy and configure Supervisor
sudo cp supervisor.conf /etc/supervisor/conf.d/luky-backend.conf
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start luky-backend:*
```

---

## 🔄 REGULAR UPDATES

### Using the Deployment Script
```bash
cd /var/www/luky-backend

# Put app in maintenance mode
php artisan down --message="Updating application, please wait..."

# Run automated deployment
bash deploy-production.sh

# Bring app back online
php artisan up
```

### Manual Update Steps
```bash
# 1. Pull latest code
git pull origin main

# 2. Update dependencies
composer install --no-dev --optimize-autoloader
npm ci --production && npm run build

# 3. Run migrations
php artisan migrate --force

# 4. Clear and cache
php artisan config:clear && php artisan config:cache
php artisan route:clear && php artisan route:cache
php artisan view:clear && php artisan view:cache

# 5. Restart services
sudo systemctl restart php8.3-fpm
php artisan queue:restart
```

---

## 💾 BACKUPS

### Setup Automated Backups
```bash
# Make backup script executable
chmod +x backup.sh

# Edit database password in backup.sh
nano backup.sh

# Test backup
bash backup.sh

# Add to crontab (runs daily at 2 AM)
sudo crontab -e
```

Add this line:
```
0 2 * * * /var/www/luky-backend/backup.sh >> /var/log/luky-backup.log 2>&1
```

### Manual Backup
```bash
# Database backup
pg_dump -U luky_user luky_production | gzip > backup_$(date +%Y%m%d).sql.gz

# Files backup
tar -czf storage_backup.tar.gz storage/app
```

### Restore from Backup
```bash
# Restore database
gunzip < backup_20250101.sql.gz | psql -U luky_user luky_production

# Restore files
tar -xzf storage_backup.tar.gz -C /var/www/luky-backend/
```

---

## 🔍 MONITORING & LOGS

### Check Application Status
```bash
# Check if site is running
curl -I https://your-domain.com

# Check PHP-FPM
sudo systemctl status php8.3-fpm

# Check Nginx
sudo systemctl status nginx

# Check queue workers
sudo supervisorctl status
```

### View Logs
```bash
# Application logs
tail -f storage/logs/laravel.log

# Nginx access logs
tail -f /var/log/nginx/luky-backend-access.log

# Nginx error logs
tail -f /var/log/nginx/luky-backend-error.log

# Queue worker logs
tail -f storage/logs/queue-worker.log
```

---

## 🛠️ TROUBLESHOOTING

### Application Returns 500 Error
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Check permissions
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data /var/www/luky-backend

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Queue Jobs Not Processing
```bash
# Check supervisor status
sudo supervisorctl status

# Restart queue workers
sudo supervisorctl restart luky-backend:*

# Check queue worker logs
tail -f storage/logs/queue-worker.log
```

### Database Connection Failed
```bash
# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();
>>> exit

# Check PostgreSQL status
sudo systemctl status postgresql

# Verify credentials in .env
cat .env | grep DB_
```

### Nginx 502 Bad Gateway
```bash
# Check PHP-FPM status
sudo systemctl status php8.3-fpm

# Check PHP-FPM error log
sudo tail -f /var/log/php8.3-fpm.log

# Restart PHP-FPM
sudo systemctl restart php8.3-fpm
```

---

## 🔐 SECURITY REMINDERS

✅ **Critical Security Checklist:**
- [ ] `APP_DEBUG=false` in production
- [ ] Strong passwords for database and services
- [ ] SSL certificate installed and auto-renewing
- [ ] Firewall configured (UFW)
- [ ] `.env` file has 600 permissions
- [ ] Regular security updates applied
- [ ] Fail2ban configured
- [ ] SSH key-based auth only

---

## 📞 SUPPORT

For detailed information, see:
- `DEPLOYMENT_CHECKLIST.md` - Full deployment guide
- `nginx.conf` - Web server configuration
- `supervisor.conf` - Queue worker configuration
- `backup.sh` - Backup automation

---

## 🎯 PERFORMANCE OPTIMIZATION

### Enable OPcache (PHP)
```bash
sudo nano /etc/php/8.3/fpm/php.ini
```

Add/uncomment:
```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
```

### Use Redis for Cache & Sessions
Already configured in `.env.production`:
```env
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### Enable Gzip Compression
Already configured in `nginx.conf`

---

## 📊 MONITORING TOOLS (Optional)

Consider setting up:
- **Laravel Telescope** (development only)
- **Laravel Horizon** (queue monitoring)
- **New Relic** or **DataDog** (application monitoring)
- **Sentry** (error tracking)
- **Uptime Robot** (uptime monitoring)

---

**Remember:** Always test deployments in a staging environment first! 🚀
