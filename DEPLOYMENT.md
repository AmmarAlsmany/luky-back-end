# 🚀 Luky Platform - Production Deployment Guide

## Production Domain
**URL:** https://techspireksa.com

---

## 📋 Pre-Deployment Checklist

### 1. Environment Configuration
- [x] `.env` updated with production domain
- [x] `APP_ENV=production`
- [x] `APP_DEBUG=false`
- [x] Database credentials configured
- [ ] Gmail SMTP password added to `MAIL_PASSWORD`
- [x] Firebase credentials moved to `storage/app/firebase/`
- [x] MyFatoorah API configured for production

### 2. Build Assets
```bash
# Install dependencies
npm install

# Build for production
npm run build
```

### 3. Laravel Optimization
```bash
# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Cache for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

---

## 🔧 Server Requirements

### PHP Requirements
- PHP >= 8.1
- PostgreSQL >= 13
- Composer
- Node.js >= 18.x
- NPM

### PHP Extensions
- BCMath
- Ctype
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO
- PDO_PGSQL
- Tokenizer
- XML
- GD
- cURL

---

## 📦 Deployment Steps

### 1. Upload Files to Server
```bash
# Using Git (Recommended)
git clone <repository-url> /var/www/luky
cd /var/www/luky

# Or using FTP/SFTP
# Upload all files except:
# - node_modules/
# - vendor/
# - .env (upload separately)
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Install Node dependencies
npm install

# Build frontend assets
npm run build
```

### 3. Configure Environment
```bash
# Copy and edit .env file
cp .env.example .env
nano .env

# Generate application key (if not set)
php artisan key:generate
```

### 4. Set Permissions
```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/luky

# Set directory permissions
sudo find /var/www/luky -type d -exec chmod 755 {} \;
sudo find /var/www/luky -type f -exec chmod 644 {} \;

# Set storage and cache permissions
sudo chmod -R 775 /var/www/luky/storage
sudo chmod -R 775 /var/www/luky/bootstrap/cache

# Secure Firebase credentials
sudo chmod 600 /var/www/luky/storage/app/firebase/*.json
```

### 5. Database Setup
```bash
# Run migrations
php artisan migrate --force

# Seed initial data (if needed)
php artisan db:seed --class=CleanupDummyDataSeeder
```

### 6. Upload Firebase Credentials
```bash
# Upload via SCP
scp storage/app/firebase/luky-96cae-firebase-adminsdk-fbsvc-96f53ee261.json \
    user@server:/var/www/luky/storage/app/firebase/

# Set permissions
sudo chmod 600 /var/www/luky/storage/app/firebase/*.json
sudo chown www-data:www-data /var/www/luky/storage/app/firebase/*.json
```

### 7. Configure Web Server

#### Nginx Configuration
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name techspireksa.com www.techspireksa.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name techspireksa.com www.techspireksa.com;
    
    root /var/www/luky/public;
    index index.php;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/techspireksa.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/techspireksa.com/privkey.pem;
    
    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    # Logging
    access_log /var/log/nginx/luky-access.log;
    error_log /var/log/nginx/luky-error.log;

    # PHP-FPM
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 8. SSL Certificate (Let's Encrypt)
```bash
# Install Certbot
sudo apt install certbot python3-certbot-nginx

# Get SSL certificate
sudo certbot --nginx -d techspireksa.com -d www.techspireksa.com

# Auto-renewal (already set up by certbot)
sudo certbot renew --dry-run
```

### 9. Setup Queue Worker (Optional)
```bash
# Create supervisor config
sudo nano /etc/supervisor/conf.d/luky-worker.conf
```

```ini
[program:luky-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/luky/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/luky/storage/logs/worker.log
stopwaitsecs=3600
```

```bash
# Start supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start luky-worker:*
```

### 10. Setup Cron Jobs
```bash
# Edit crontab
sudo crontab -e -u www-data

# Add Laravel scheduler
* * * * * cd /var/www/luky && php artisan schedule:run >> /dev/null 2>&1
```

---

## ✅ Post-Deployment Verification

### 1. Check Application
- [ ] Visit https://techspireksa.com
- [ ] Login with super admin credentials
- [ ] Test all major features
- [ ] Check browser console for errors

### 2. Check Logs
```bash
# Laravel logs
tail -f /var/www/luky/storage/logs/laravel.log

# Nginx logs
tail -f /var/log/nginx/luky-error.log
```

### 3. Test Email
```bash
php artisan tinker
>>> Mail::raw('Test email', function($msg) { $msg->to('test@example.com')->subject('Test'); });
```

### 4. Test SMS
```bash
# Test SMS sending through your SMS provider
```

### 5. Test Push Notifications
```bash
# Test FCM push notifications
```

---

## 🔐 Security Checklist

- [x] `APP_DEBUG=false` in production
- [x] Strong `APP_KEY` generated
- [ ] Database credentials secure
- [ ] Firebase credentials protected (chmod 600)
- [x] `.env` file not in Git
- [ ] SSL certificate installed
- [ ] Security headers configured
- [ ] File permissions set correctly
- [ ] Firewall configured (allow 80, 443, 22 only)

---

## 📊 Monitoring

### Application Monitoring
- Set up error tracking (Sentry, Bugsnag, etc.)
- Monitor Laravel logs
- Set up uptime monitoring

### Server Monitoring
- CPU and memory usage
- Disk space
- Database performance
- Queue workers status

---

## 🔄 Update Process

```bash
# Pull latest code
cd /var/www/luky
git pull origin main

# Update dependencies
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Run migrations
php artisan migrate --force

# Clear and cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart services
sudo supervisorctl restart luky-worker:*
sudo systemctl reload nginx
```

---

## 📞 Support

- **Admin Email:** admin@luky.sa
- **Default Password:** password (CHANGE IMMEDIATELY!)
- **Domain:** https://techspireksa.com

---

## 📝 Notes

1. **Change default admin password immediately after first login**
2. **Add Gmail app password to `.env` for email functionality**
3. **Test payment gateway in sandbox mode before going live**
4. **Backup database regularly**
5. **Monitor error logs daily**
