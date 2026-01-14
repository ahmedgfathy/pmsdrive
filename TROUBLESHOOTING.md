# Troubleshooting Guide

## Issue 1: CSRF Token Mismatch / 419 Unauthorized Error

### Symptoms:
- Error when trying to login
- After refresh, automatically logged in

### Root Cause:
- Missing CSRF cookie request before login
- Session configuration issues

### Fix Applied:
1. **Added `withXSRFToken` to axios defaults** in `resources/js/bootstrap.js`
2. **Added CSRF cookie request** before login in `Login.vue`
3. **Configured Sanctum stateful domains** in `.env`
4. **Set SESSION_SAME_SITE=lax** for proper cookie handling

### How to Clear Browser Cache:
1. **Chrome/Edge**: Ctrl+Shift+Delete → Clear cached images and files
2. **Firefox**: Ctrl+Shift+Delete → Cached Web Content
3. **Hard Refresh**: Ctrl+F5 or Ctrl+Shift+R
4. **Private/Incognito Mode**: Test in a new private window

## Issue 2: Old Design Showing After Update

### Symptoms:
- New design not visible after login
- Components show old styling even after refresh

### Root Cause:
- Browser caching old JavaScript/CSS assets
- Asset versioning not forcing reload

### Fix Applied:
1. **Cleared public/build directory** and rebuilt assets
2. **New asset hash generated**: `app-Dm7Xve4O.js` (was `app-XFcIiW7d.js`)
3. **Cleared Laravel caches**: config, route, application

### How to Force Asset Reload:
```bash
# Backend (run on server)
cd /home/xinreal/pmsdrive
rm -rf public/build/*
npm run build
php artisan config:clear
php artisan cache:clear
php artisan route:clear

# Frontend (in browser)
1. Hard refresh: Ctrl+Shift+R or Ctrl+F5
2. Clear browser cache completely
3. Open in Incognito/Private mode to test
4. Close all tabs and restart browser
```

## Current Configuration

### Email Settings:
- Server: mail.pms.eg:587
- Encryption: TLS
- Username: complaints@pms.eg
- From: pmsoffshore

### Authentication:
- Driver: Sanctum (session-based)
- Session: Database driver
- CSRF Protection: Enabled
- Stateful Domains: localhost, 127.0.0.1:8000

### Admin Account:
- Email: ahmed.fathy@pms.eg
- Password: admin123
- ID: 1

## Testing Steps

1. **Clear Everything**:
   ```bash
   # Clear browser cache (Ctrl+Shift+Delete)
   # Or use Incognito mode
   ```

2. **Login Test**:
   - Go to http://localhost:8000
   - Enter credentials
   - Should NOT see CSRF error
   - Should redirect to dashboard with NEW design

3. **Share Feature Test**:
   - Navigate to Drive tab
   - Find any file
   - Click orange share icon (should be visible next to download/delete buttons)
   - Share modal should open with 3 tabs

4. **Design Verification**:
   - Dashboard: Modern gradient cards with charts
   - Drive: Premium design with color-coded file icons
   - Shared: Orange-themed design matching Drive

## If Issues Persist

1. **Check Browser Console** (F12):
   - Look for 401/419 errors
   - Check if assets are loading (Network tab)
   - Verify JavaScript errors

2. **Check Laravel Logs**:
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Verify Session Table**:
   ```bash
   php artisan tinker
   DB::table('sessions')->count(); // Should show sessions
   ```

4. **Test API Directly**:
   ```bash
   curl -X POST http://localhost:8000/api/login \
     -H "Content-Type: application/json" \
     -H "Accept: application/json" \
     -d '{"email":"ahmed.fathy@pms.eg","password":"admin123"}'
   ```

## Assets Information

### Current Build:
- JavaScript: `assets/app-Dm7Xve4O.js` (428.48 kB)
- Main CSS: `assets/app-BK47oj64.css` (70.86 kB)
- Additional CSS: `assets/app-AmxW4RWc.css` (0.18 kB)

### Components Updated:
- ✅ ShareModal.vue (new)
- ✅ Drive.vue (share button added)
- ✅ Login.vue (CSRF fix)
- ✅ bootstrap.js (CSRF token config)

### Routes Added:
- POST /api/files/{id}/share/user
- POST /api/files/{id}/share/link
- POST /api/files/{id}/share/email
- GET /api/files/shared
- DELETE /api/files/{fileId}/share/{userId}
- GET /api/share/{token} (public)
