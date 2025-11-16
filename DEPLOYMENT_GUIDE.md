# Quick Deployment Guide - Luky Backend

## 🚀 Step-by-Step Deployment

### Step 1: Prepare Your Server

**Requirements**:
- PHP 8.2 or higher
- PostgreSQL 13 or higher
- Nginx or Apache
- Composer
- Node.js & NPM

### Step 2: Upload Files

```bash
# Upload all files EXCEPT:
- .env (will create new one)
- node_modules/
- vendor/
- storage/logs/*
- storage/framework/cache/*
```

### Step 3: Server Setup

```bash
# Navigate to project directory
cd /path/to/luky-backend

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node dependencies (including dev for build tools)
npm install --production=false

# Build assets for production
npm run build

# IMPORTANT: Verify build succeeded
ls -la public/build/manifest.json
# You should see the manifest file. If not, check VITE_BUILD_FIX.md

# Create .env file
cp .env.example .env
nano .env  # Edit with production values
```

### Step 4: Configure .env File

**CRITICAL - Update These Values**:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_DATABASE=your_production_db
DB_USERNAME=your_production_user
DB_PASSWORD=your_production_password

MYFATOORAH_API_KEY=your_live_api_key
MYFATOORAH_API_URL=https://api.myfatoorah.com
MYFATOORAH_WEBHOOK_SECRET=your_webhook_secret

SMS_APP_ID=your_sms_app_id
SMS_APP_SECRET=your_sms_app_secret

FCM_SERVER_KEY=your_fcm_key
FCM_CREDENTIALS_PATH=/var/www/firebase-credentials.json
```

### Step 5: Generate Application Key

```bash
php artisan key:generate
```

### Step 6: Run Migrations

```bash
php artisan migrate --force
```

### Step 7: Set Permissions

```bash
chmod -R 755 storage bootstrap/cache
chmod 644 .env
chown -R www-data:www-data storage bootstrap/cache
```

### Step 8: Cache Configuration

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 9: Configure Web Server

**Nginx Configuration** (`/etc/nginx/sites-available/luky`):

```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/luky-backend/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable site:
```bash
sudo ln -s /etc/nginx/sites-available/luky /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Step 10: Setup SSL Certificate

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d yourdomain.com
```

### Step 11: Setup Queue Worker (Optional but Recommended)

Create supervisor config: `/etc/supervisor/conf.d/luky-worker.conf`

```ini
[program:luky-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/luky-backend/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/luky-backend/storage/logs/worker.log
stopwaitsecs=3600
```

Start supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start luky-worker:*
```

### Step 12: Setup Cron Jobs

```bash
crontab -e
```

Add:
```cron
* * * * * cd /var/www/luky-backend && php artisan schedule:run >> /dev/null 2>&1
```

---

## ✅ Post-Deployment Verification

### Test These Endpoints:

1. **Health Check**:
   ```bash
   curl https://yourdomain.com/up
   ```

2. **API Status**:
   ```bash
   curl https://yourdomain.com/api/v1/cities
   ```

3. **Admin Login** (via browser):
   - Visit: `https://yourdomain.com/login`
   - Login with admin credentials

4. **Test OTP** (via Postman/Insomnia):
   ```
   POST https://yourdomain.com/api/v1/auth/send-otp
   {
     "phone": "+966xxxxxxxxx"
   }
   ```

---

## 🔍 Troubleshooting

### Issue: 500 Internal Server Error

**Check**:
```bash
tail -f storage/logs/laravel.log
tail -f /var/log/nginx/error.log
```

**Common Fixes**:
- Verify file permissions
- Check .env configuration
- Clear cache: `php artisan cache:clear`
- Check database connection

### Issue: Assets Not Loading / Vite Manifest Error

**Symptoms**:
- "Vite manifest not found"
- "Unable to locate file in Vite manifest"
- Blank page or missing CSS/JS

**Fix**:
```bash
# Clean rebuild
rm -rf public/build node_modules/.vite
npm install --production=false
npm run build

# Verify build succeeded
ls -la public/build/manifest.json

# Set correct permissions
chmod -R 755 public/build
chown -R www-data:www-data public/build

# Clear Laravel cache
php artisan config:clear
php artisan view:clear
```

**See detailed guide**: Check `VITE_BUILD_FIX.md` for comprehensive solutions

### Issue: Mixed Content Errors (HTTP/HTTPS)

**Fix** in `.env`:
```env
APP_URL=https://yourdomain.com
ASSET_URL=https://yourdomain.com
```

Then:
```bash
php artisan config:cache
```

### Issue: Database Connection Failed

**Check**:
- Database credentials in .env
- PostgreSQL is running: `sudo systemctl status postgresql`
- Database exists: `sudo -u postgres psql -l`

### Issue: Rate Limiting Too Strict

**Adjust** in `routes/api.php`:
```php
->middleware('throttle:10,1')  // 10 requests per minute
```

---

## 📊 Monitoring

### Setup Monitoring:

1. **Laravel Log Monitor**:
   ```bash
   tail -f storage/logs/laravel.log
   ```

2. **Database Backups**:
   ```bash
   # Add to crontab
   0 2 * * * pg_dump luky | gzip > /backups/luky-$(date +\%Y\%m\%d).sql.gz
   ```

3. **Disk Space**:
   ```bash
   df -h
   ```

4. **Server Resources**:
   ```bash
   htop
   ```

---

## 🔐 Security Checklist

- [ ] SSL certificate installed and working
- [ ] .env file not accessible via web
- [ ] APP_DEBUG=false in production
- [ ] Strong database passwords
- [ ] Firewall configured (allow only 80, 443, 22)
- [ ] SSH keys only (disable password auth)
- [ ] Regular security updates: `sudo apt update && sudo apt upgrade`
- [ ] Backup strategy in place
- [ ] Rate limiting tested
- [ ] Webhook signatures verified

---

## 📱 Mobile App Configuration

**Update Mobile App .env**:

```env
API_BASE_URL=https://yourdomain.com/api/v1
WEBSOCKET_URL=wss://yourdomain.com
```

---

## 🆘 Emergency Rollback

If something goes wrong:

```bash
# 1. Disable site
sudo rm /etc/nginx/sites-enabled/luky
sudo systemctl reload nginx

# 2. Restore backup
# (restore from your backup)

# 3. Re-enable site
sudo ln -s /etc/nginx/sites-available/luky /etc/nginx/sites-enabled/
sudo systemctl reload nginx
```

---

## 📞 Need Help?

1. Check `SECURITY_FIXES_APPLIED.md`
2. Review Laravel logs
3. Check server error logs
4. Verify .env configuration

**Common Commands**:
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Restart services
sudo systemctl restart php8.2-fpm
sudo systemctl restart nginx
sudo supervisorctl restart luky-worker:*
```

---

**Last Updated**: 2025-11-16
**For**: Luky Platform v1.0
