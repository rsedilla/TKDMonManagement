# HTTPS Setup Guide for Production

## Option 1: SSL Certificate from Certificate Authority (Recommended)

### Step 1: Obtain SSL Certificate
- **Free Option**: Let's Encrypt (most popular)
- **Paid Options**: Comodo, DigiCert, GlobalSign
- **Cloud Providers**: AWS Certificate Manager, Cloudflare

### Step 2: Install Certificate on Web Server

#### For Apache (.htaccess already configured):
```apache
<VirtualHost *:443>
    ServerName yourdomain.com
    DocumentRoot /path/to/your/FilamentCMS/public
    
    SSLEngine on
    SSLCertificateFile /path/to/certificate.crt
    SSLCertificateKeyFile /path/to/private.key
    SSLCertificateChainFile /path/to/ca_bundle.crt
</VirtualHost>

# Redirect HTTP to HTTPS
<VirtualHost *:80>
    ServerName yourdomain.com
    Redirect permanent / https://yourdomain.com/
</VirtualHost>
```

#### For Nginx:
```nginx
server {
    listen 443 ssl;
    server_name yourdomain.com;
    root /path/to/your/FilamentCMS/public;
    
    ssl_certificate /path/to/certificate.crt;
    ssl_certificate_key /path/to/private.key;
    
    # Modern SSL configuration
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}

# Redirect HTTP to HTTPS
server {
    listen 80;
    server_name yourdomain.com;
    return 301 https://$server_name$request_uri;
}
```

## Option 2: Using Cloudflare (Easiest)

1. Sign up at cloudflare.com
2. Add your domain
3. Change nameservers to Cloudflare's
4. Enable SSL/TLS in Cloudflare dashboard
5. Set SSL mode to "Full (strict)"

## Option 3: Using Let's Encrypt (Free)

### Install Certbot:
```bash
# Ubuntu/Debian
sudo apt install certbot python3-certbot-apache

# CentOS/RHEL
sudo yum install certbot python3-certbot-apache
```

### Generate Certificate:
```bash
sudo certbot --apache -d yourdomain.com -d www.yourdomain.com
```

### Auto-renewal:
```bash
sudo crontab -e
# Add this line:
0 12 * * * /usr/bin/certbot renew --quiet
```

## Laravel Configuration for HTTPS

Update your `.env` file:
```env
APP_URL=https://yourdomain.com
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=strict
```

## Testing HTTPS

1. **SSL Labs Test**: https://www.ssllabs.com/ssltest/
2. **Check Certificate**: `openssl s_client -connect yourdomain.com:443`
3. **Browser Test**: Look for padlock icon

## Common Issues

### Mixed Content Warnings
- Ensure all resources (CSS, JS, images) use HTTPS URLs
- Use relative URLs or `asset()` helper in Laravel

### Redirect Loops
- Check web server configuration
- Ensure Laravel `APP_URL` matches your domain

### Certificate Errors
- Verify certificate chain is complete
- Check certificate expiration date
- Ensure certificate matches domain name
