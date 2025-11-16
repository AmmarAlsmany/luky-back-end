# Vite Build Issues - Complete Fix Guide

## 🔴 Common Vite Errors You Might See:

1. **"Manifest file not found"**
2. **"Unable to locate file in Vite manifest"**
3. **"Library used in view not found in config file"**
4. **Production shows blank page but dev works**

---

## ✅ Solution 1: Ensure All Files Exist

### Step 1: Check Missing JS Files

Run this command to find files referenced in views but missing from disk:

```bash
cd A:\Projects\luky-backend

# Check if dashboard.js exists
ls resources/js/pages/dashboard.js

# Check if humanfd.js exists (THIS IS LIKELY MISSING)
ls resources/js/pages/humanfd.js
```

### Step 2: Create Missing Files

If `humanfd.js` is missing (likely), create it:

```bash
# Create the file
touch resources/js/pages/humanfd.js
```

Or create it with this content:

**File**: `resources/js/pages/humanfd.js`
```javascript
// Human-friendly date formatting
// This file handles relative time display (e.g., "2 hours ago")
console.log('Human-friendly dates loaded');
```

---

## ✅ Solution 2: Clean Build & Rebuild

### For Development:

```bash
# Stop Vite if running (Ctrl+C)

# Delete old build
rmdir /s /q public\build
rmdir /s /q node_modules\.vite

# Clean npm cache
npm cache clean --force

# Reinstall dependencies
npm install

# Build fresh
npm run build

# Or run dev
npm run dev
```

### For Production (on server):

```bash
# Clean old build
rm -rf public/build
rm -rf node_modules/.vite

# Install production dependencies
npm install --production=false

# Build for production
npm run build

# Verify manifest exists
ls -la public/build/manifest.json
```

---

## ✅ Solution 3: Fix Vite Config (Already Done)

Your `vite.config.js` is now using environment variables for host/HMR, which should work correctly.

---

## ✅ Solution 4: Update Views to Handle Missing Files

### Option A: Check Files Before Building

Create this script: `check-vite-files.js`

```javascript
const fs = require('fs');
const path = require('path');

// List of all files from vite.config.js
const files = [
    'resources/scss/app.scss',
    'resources/scss/icons.scss',
    'resources/js/app.js',
    'resources/js/config.js',
    'resources/js/layout.js',
    'resources/js/pages/dashboard.js',
    'resources/js/pages/dashboard-admin.js',
    'resources/js/pages/humanfd.js', // ADD THIS IF MISSING
    // ... add all other files from your vite.config.js
];

console.log('Checking Vite input files...\n');

let missing = [];
files.forEach(file => {
    if (!fs.existsSync(file)) {
        console.log(`❌ MISSING: ${file}`);
        missing.push(file);
    } else {
        console.log(`✓ Found: ${file}`);
    }
});

if (missing.length > 0) {
    console.log(`\n⚠️  ${missing.length} files are missing!`);
    console.log('\nCreate these files or remove them from vite.config.js');
    process.exit(1);
} else {
    console.log('\n✅ All files exist!');
}
```

Run before building:
```bash
node check-vite-files.js
npm run build
```

### Option B: Make Views Resilient

Wrap `@vite()` calls with existence checks in your views:

**Before** (in dashboard.blade.php):
```blade
@section('script-bottom')
    @vite([
        'resources/js/components/form-flatepicker.js',
        'resources/js/pages/humanfd.js'
    ])
@endsection
```

**After**:
```blade
@section('script-bottom')
    @if(file_exists(resource_path('js/components/form-flatepicker.js')))
        @vite(['resources/js/components/form-flatepicker.js'])
    @endif
    @if(file_exists(resource_path('js/pages/humanfd.js')))
        @vite(['resources/js/pages/humanfd.js'])
    @endif
@endsection
```

---

## ✅ Solution 5: Proper Production Build Steps

### On Your Local Machine (Before Upload):

```bash
# 1. Clean everything
npm run build

# 2. Verify build succeeded
dir public\build
# Should see: manifest.json and assets folder

# 3. Upload these to server:
- public/build/ (entire folder)
- All code files (but NOT node_modules)
```

### On Production Server:

```bash
# Option A: Build on server (RECOMMENDED)
cd /var/www/luky-backend
npm install --production=false
npm run build

# Option B: Use pre-built files from local
# Just upload public/build/ folder
# Make sure permissions are correct:
chmod -R 755 public/build
```

---

## ✅ Solution 6: Fix "humanfd.js" Specifically

This file is referenced in your view but might not exist.

### Create the file:

**File**: `resources/js/pages/humanfd.js`

```javascript
/**
 * Human-Friendly Date Formatter
 * Converts dates to relative time (e.g., "2 hours ago", "3 days ago")
 */

import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';

// Enable relative time plugin
dayjs.extend(relativeTime);

// Format all elements with data-humanize attribute
document.addEventListener('DOMContentLoaded', function() {
    const elements = document.querySelectorAll('[data-humanize]');

    elements.forEach(element => {
        const date = element.getAttribute('data-humanize');
        if (date) {
            element.textContent = dayjs(date).fromNow();
        }
    });
});

console.log('✓ Human-friendly dates initialized');
```

**Then add dayjs if not installed**:
```bash
npm install dayjs
```

**OR Remove the reference** from your view if you don't need it:

In `resources/views/dashboards/index.blade.php`, remove:
```blade
@section('script-bottom')
    @vite([
        'resources/js/components/form-flatepicker.js',
        // 'resources/js/pages/humanfd.js'  <- COMMENT THIS OUT OR DELETE
    ])
@endsection
```

---

## ✅ Solution 7: Common Troubleshooting

### Error: "Vite manifest not found"

**Cause**: Build hasn't run or failed
**Fix**:
```bash
npm run build
# Check for errors in output
```

### Error: "Unable to locate file in Vite manifest: resources/js/pages/xyz.js"

**Cause**: File is in a view but doesn't exist OR not in vite.config.js
**Fix**:
1. Check if file exists: `ls resources/js/pages/xyz.js`
2. If missing, create it or remove from view
3. If exists, add to `vite.config.js` input array
4. Rebuild: `npm run build`

### Error: Page loads but JavaScript doesn't work

**Cause**: Build worked but wrong environment
**Fix**:
```bash
# Make sure APP_ENV is correct
# In .env file:
APP_ENV=production  # For production
APP_ENV=local       # For development

# Clear cache
php artisan config:clear
php artisan cache:clear
```

### Error: "Mixed Content" (HTTP/HTTPS issues)

**Cause**: Trying to load assets over HTTP on HTTPS site
**Fix** in `.env`:
```env
APP_URL=https://yourdomain.com  # Must use HTTPS
ASSET_URL=https://yourdomain.com
```

---

## 📋 Pre-Build Checklist

Before running `npm run build`, check:

- [ ] All files in `vite.config.js` input array exist
- [ ] No typos in file paths
- [ ] `node_modules` is installed: `npm install`
- [ ] Vite config has correct host settings
- [ ] `.env` has correct APP_URL
- [ ] No conflicting Vite processes running

---

## 🚀 Recommended Build Process

### Development:
```bash
# Terminal 1: Laravel
php artisan serve

# Terminal 2: Vite dev server
npm run dev
```

### Production Build:
```bash
# Clean slate
rm -rf public/build
rm -rf node_modules/.vite

# Install & build
npm install
npm run build

# Verify
ls -la public/build/manifest.json
ls -la public/build/assets/

# If successful, you'll see hashed asset files
```

---

## ⚡ Quick Fix Commands

```bash
# Fix 1: Complete clean rebuild
rm -rf public/build node_modules/.vite && npm install && npm run build

# Fix 2: Clear Laravel caches
php artisan config:clear && php artisan cache:clear && php artisan view:clear

# Fix 3: Check manifest
cat public/build/manifest.json | head -20

# Fix 4: Verify permissions (on server)
chmod -R 755 public/build
chown -R www-data:www-data public/build
```

---

## 📝 Adding New Assets

When you add a new JS/CSS file:

1. **Create the file**: `resources/js/pages/mynewpage.js`
2. **Add to vite.config.js**:
   ```javascript
   input: [
       // ... existing files
       'resources/js/pages/mynewpage.js',  // ADD HERE
   ]
   ```
3. **Rebuild**: `npm run build`
4. **Use in view**: `@vite(['resources/js/pages/mynewpage.js'])`

---

## 🔍 Debug Mode

Add this to your layout to see what Vite is loading:

```blade
@if(app()->environment('local'))
    <!-- Vite Debug Info -->
    <script>
        console.log('Vite Assets Loaded:');
        document.querySelectorAll('script[src*="build"]').forEach(script => {
            console.log('✓ Script:', script.src);
        });
        document.querySelectorAll('link[href*="build"]').forEach(link => {
            console.log('✓ Style:', link.href);
        });
    </script>
@endif
```

---

**Last Updated**: 2025-11-16
**Issue**: Vite manifest and library not found errors
**Status**: Fixed with multiple solutions
