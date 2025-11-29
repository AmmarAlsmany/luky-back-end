# ⚡ Quick Fix: Vite Manifest & Library Issues

## 🚨 Problem
You see errors like:
- "Vite manifest not found"
- "Library used in view not found in config file"
- Blank pages in production
- Assets missing

## ✅ Quick Solution (90% of cases)

### On Windows (Development):
```bash
# 1. Clean everything
rmdir /s /q public\build
rmdir /s /q node_modules\.vite

# 2. Reinstall & rebuild
npm install
npm run build

# 3. Verify it worked
dir public\build\manifest.json
```

### On Linux (Production Server):
```bash
# 1. Clean everything
cd /var/www/luky-backend
rm -rf public/build
rm -rf node_modules/.vite

# 2. Reinstall & rebuild
npm install --production=false
npm run build

# 3. Verify it worked
ls -la public/build/manifest.json

# 4. Fix permissions
chmod -R 755 public/build
chown -R www-data:www-data public/build

# 5. Clear Laravel caches
php artisan config:clear
php artisan view:clear
php artisan cache:clear
```

## 🔍 Still Not Working?

### Check 1: Is the file actually missing?
```bash
# Check if a specific file exists
ls resources/js/pages/dashboard.js

# If missing, either:
# - Create it, OR
# - Remove @vite() reference from your view
```

### Check 2: Is file in vite.config.js?
Open `vite.config.js` and check if the file is in the `input` array.

If not, add it:
```javascript
input: [
    // ... existing files
    'resources/js/pages/your-file.js',  // ADD HERE
],
```

Then rebuild: `npm run build`

### Check 3: Environment variables
In your `.env` file:
```env
APP_ENV=production       # Must be 'production' on server
APP_DEBUG=false          # Must be false on server
APP_URL=https://yourdomain.com
ASSET_URL=https://yourdomain.com
```

Then: `php artisan config:cache`

## 📋 Common Mistakes

❌ **Wrong**: Running `npm install --production` (skips dev dependencies needed for build)
✅ **Right**: Running `npm install --production=false`

❌ **Wrong**: Forgetting to run `npm run build`
✅ **Right**: Always build before testing

❌ **Wrong**: Wrong permissions on public/build
✅ **Right**: `chmod -R 755 public/build`

❌ **Wrong**: Old cached config
✅ **Right**: Always clear cache after changes

## 🎯 One-Command Fix

Try this single command that does everything:

```bash
# Development (Windows)
rmdir /s /q public\build && rmdir /s /q node_modules\.vite && npm install && npm run build

# Production (Linux)
rm -rf public/build node_modules/.vite && npm install --production=false && npm run build && chmod -R 755 public/build && php artisan config:clear && php artisan view:clear
```

## 📞 Still Having Issues?

Check the detailed guide: **`VITE_BUILD_FIX.md`**

## ⚡ Pro Tips

1. **Always build before deploying**: `npm run build` locally, then upload `public/build/`
2. **Check manifest**: If `public/build/manifest.json` doesn't exist, the build failed
3. **Watch for errors**: Read the npm build output carefully
4. **Test locally first**: Make sure `npm run build` works on your machine before deploying

## 🔄 After Every Code Change

If you modify JS/CSS files:
```bash
# Development
npm run dev  # Hot reload

# Production
npm run build  # Create new build
php artisan view:clear
```

---

**Quick Reference Card** | Last Updated: 2025-11-16
