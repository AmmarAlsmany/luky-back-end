# 🚀 LUKY BACKEND - PRODUCTION DEPLOYMENT CHECKLIST

## 📋 PRE-DEPLOYMENT CHECKLIST

### 1. Server Requirements
- [ ] Ubuntu 22.04+ or similar Linux distribution
- [ ] PHP 8.3+ with required extensions
- [ ] PostgreSQL 14+
- [ ] Redis 6+
- [ ] Nginx or Apache
- [ ] Node.js 18+ and NPM
- [ ] Composer 2.x
- [ ] Supervisor (for queue workers)
- [ ] SSL certificate (Let's Encrypt recommended)

### 2. PHP Extensions Required
```bash
php8.3-cli
php8.3-fpm
php8.3-pgsql
php8.3-mbstring
php8.3-xml
php8.3-bcmath
php8.3-curl
php8.3-zip
php8.3-gd
php8.3-intl
php8.3-redis
```

### 3. Install Required Packages
```bash
sudo apt update
sudo apt install -y nginx postgresql redis-server supervisor certbot python3-certbot-nginx
sudo apt install -y php8.3 php8.3-fpm php8.3-cli php8.3-pgsql php8.3-mbstring php8.3-xml php8.3-bcmath php8.3-curl php8.3-zip php8.3-gd php8.3-intl php8.3-redis
```

---

## 🔧 SERVER SETUP

### 1. Create Database
```bash
sudo -u postgres psql
CREATE DATABASE luky_production;
CREATE USER luky_user WITH ENCRYPTED PASSWORD 'STRONG_PASSWORD_HERE';
GRANT ALL PRIVILEGES ON DATABASE luky_production TO luky_user;
\q
```

### 2. Clone Repository
```bash
cd /var/www
git clone https://github.com/your-repo/luky-backend.git
cd luky-backend
```

### 3. Install Dependencies
```bash
composer install --no-dev --optimize-autoloader
npm ci --production
npm run build
```

### 4. Environment Configuration
```bash
cp .env.production .env
nano .env  # Edit with your production values
php artisan key:generate
```

### 5. Set Permissions
```bash
sudo chown -R www-data:www-data /var/www/luky-backend
sudo chmod -R 755 /var/www/luky-backend
sudo chmod -R 775 /var/www/luky-backend/storage
sudo chmod -R 775 /var/www/luky-backend/bootstrap/cache
sudo chmod 600 /var/www/luky-backend/.env
```

### 6. Run Migrations
```bash
php artisan migrate --force
php artisan db:seed --class=CitySeeder  # If needed
```

### 7. Optimize Application
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

---

## 🌐 NGINX CONFIGURATION

### 1. Copy Nginx Config
```bash
sudo cp nginx.conf /etc/nginx/sites-available/luky-backend
sudo ln -s /etc/nginx/sites-available/luky-backend /etc/nginx/sites-enabled/
```

### 2. Update Domain Names
```bash
sudo nano /etc/nginx/sites-available/luky-backend
# Replace 'your-domain.com' with actual domain
```

### 3. Setup SSL Certificate
```bash
sudo certbot --nginx -d your-domain.com -d www.your-domain.com
```

### 4. Test and Reload Nginx
```bash
sudo nginx -t
sudo systemctl reload nginx
```

---

## ⚙️ SUPERVISOR CONFIGURATION

### 1. Copy Supervisor Config
```bash
sudo cp supervisor.conf /etc/supervisor/conf.d/luky-backend.conf
```

### 2. Update Supervisor
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start luky-backend:*
```

### 3. Check Queue Workers
```bash
sudo supervisorctl status
```

---

## 🔒 SECURITY CHECKLIST

### Application Security
- [ ] `APP_DEBUG=false` in `.env`
- [ ] `APP_ENV=production` in `.env`
- [ ] Strong `APP_KEY` generated
- [ ] Strong database password
- [ ] `.env` file permissions set to 600
- [ ] Remove unnecessary files (tests, .git if needed)

### Server Security
- [ ] Firewall configured (UFW)
```bash
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

- [ ] SSH key-based authentication enabled
- [ ] Root login disabled
- [ ] Fail2ban installed and configured
```bash
sudo apt install fail2ban
sudo systemctl enable fail2ban
```

### SSL/HTTPS
- [ ] SSL certificate installed
- [ ] HTTPS redirect enabled
- [ ] SSL protocols configured (TLS 1.2+)
- [ ] Auto-renewal enabled for Let's Encrypt
```bash
sudo certbot renew --dry-run
```

---

## 📊 MONITORING & LOGS

### Log Locations
- Application logs: `/var/www/luky-backend/storage/logs/laravel.log`
- Nginx access: `/var/log/nginx/luky-backend-access.log`
- Nginx error: `/var/log/nginx/luky-backend-error.log`
- Queue workers: `/var/www/luky-backend/storage/logs/queue-worker.log`

### Monitor Application
```bash
# Watch Laravel logs
tail -f /var/www/luky-backend/storage/logs/laravel.log

# Watch Nginx error logs
tail -f /var/log/nginx/luky-backend-error.log

# Check queue workers
sudo supervisorctl status
```

---

## 🔄 CRON JOBS

Add Laravel scheduler to crontab:
```bash
sudo crontab -e -u www-data
```

Add this line:
```
* * * * * cd /var/www/luky-backend && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🚀 DEPLOYMENT WORKFLOW

### Initial Deployment
1. Run the deployment script:
```bash
cd /var/www/luky-backend
bash deploy-production.sh
```

### Regular Updates
```bash
# Put app in maintenance mode
php artisan down

# Pull latest changes
git pull origin main

# Run deployment
bash deploy-production.sh

# Bring app back online
php artisan up
```

---

## ✅ POST-DEPLOYMENT VERIFICATION

### Test Checklist
- [ ] Website loads: `https://your-domain.com`
- [ ] Admin login works
- [ ] API endpoints respond correctly
- [ ] Database connections work
- [ ] Queue workers running
- [ ] File uploads work
- [ ] Email sending works
- [ ] SMS sending works (if enabled)
- [ ] Payment gateway works (MyFatoorah)
- [ ] Real-time features work (Pusher)
- [ ] FCM notifications work

### Performance Tests
```bash
# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();

# Test queue
php artisan queue:work --once

# Test cache
php artisan cache:clear
php artisan config:cache
```

---

## 🔧 TROUBLESHOOTING

### Common Issues

**500 Error:**
- Check storage permissions: `sudo chmod -R 775 storage bootstrap/cache`
- Check `.env` file exists and is readable
- Check error logs: `tail -f storage/logs/laravel.log`

**Queue not processing:**
- Restart supervisor: `sudo supervisorctl restart luky-backend:*`
- Check worker logs: `tail -f storage/logs/queue-worker.log`

**Database connection failed:**
- Verify PostgreSQL is running: `sudo systemctl status postgresql`
- Check database credentials in `.env`
- Test connection: `php artisan tinker` then `DB::connection()->getPdo()`

**Nginx 502 Bad Gateway:**
- Check PHP-FPM status: `sudo systemctl status php8.3-fpm`
- Check PHP-FPM socket: `ls -la /var/run/php/`
- Restart PHP-FPM: `sudo systemctl restart php8.3-fpm`

---

## 📞 SUPPORT CONTACTS

- Server Admin: [Your contact]
- Database Admin: [Your contact]
- DevOps: [Your contact]

---

## 📝 NOTES

- Always backup database before major updates
- Keep regular backups of `.env` file
- Monitor disk space regularly
- Keep logs under control (use log rotation)
- Review security updates monthly
