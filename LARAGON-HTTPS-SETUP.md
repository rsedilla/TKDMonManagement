# Laragon HTTPS Setup (For Development)

## Method 1: Using Laragon's Built-in SSL

1. **Right-click Laragon tray icon**
2. **Go to**: Apache → SSL → yourdomain.test
3. **Or create custom domain**: 
   - Right-click Laragon → Quick app → filamentcms.test
   - Then enable SSL for that domain

## Method 2: Manual SSL Certificate Creation

### Step 1: Create SSL Certificate in Laragon
```bash
# Open Laragon Terminal and run:
cd C:\laragon
bin\apache\bin\openssl.exe req -x509 -nodes -days 365 -newkey rsa:2048 -keyout ssl\filamentcms.key -out ssl\filamentcms.crt
```

### Step 2: Configure Apache Virtual Host
Create file: `C:\laragon\etc\apache2\sites-enabled\filamentcms.conf`

```apache
<VirtualHost *:80>
    ServerName filamentcms.test
    DocumentRoot "C:/laragon/www/FilamentCMS/public"
    Redirect permanent / https://filamentcms.test/
</VirtualHost>

<VirtualHost *:443>
    ServerName filamentcms.test
    DocumentRoot "C:/laragon/www/FilamentCMS/public"
    
    SSLEngine on
    SSLCertificateFile "C:/laragon/ssl/filamentcms.crt"
    SSLCertificateKeyFile "C:/laragon/ssl/filamentcms.key"
    
    <Directory "C:/laragon/www/FilamentCMS/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### Step 3: Update Hosts File
Add to `C:\Windows\System32\drivers\etc\hosts`:
```
127.0.0.1 filamentcms.test
```

### Step 4: Update Laravel Configuration
Update `.env`:
```env
APP_URL=https://filamentcms.test
SESSION_SECURE_COOKIE=true
```

### Step 5: Restart Laragon
- Stop and start Laragon services
- Visit: https://filamentcms.test

## Browser Security Warning
- You'll see "Not Secure" or certificate warning
- This is normal for self-signed certificates in development
- Click "Advanced" → "Proceed to filamentcms.test (unsafe)" in Chrome
- Or add certificate to trusted root certificates

## Why This Matters for Your App

Without HTTPS, these security features won't work:
- ✅ Secure cookies (SESSION_SECURE_COOKIE=true)
- ✅ Strict SameSite policy
- ✅ Some modern browser security features
- ✅ Realistic production testing environment
