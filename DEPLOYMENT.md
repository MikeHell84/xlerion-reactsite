# Deployment Guide for Xlerion ReactJS Site

## Prerequisites

- PHP 8.0 or higher with PDO extension
- MariaDB 10.5 or higher (or MySQL 8.0+)
- Apache 2.4 or Nginx
- Node.js 18+ and npm (for building on server, or build locally)
- Git (for deployment)

## Option 1: Deploy with Apache

### 1. Server Requirements

Ensure the following Apache modules are enabled:
```bash
sudo a2enmod rewrite
sudo a2enmod headers
sudo a2enmod deflate
sudo a2enmod expires
sudo systemctl restart apache2
```

### 2. Clone Repository

```bash
cd /var/www/
git clone <repository-url> xlerion
cd xlerion
```

### 3. Configure Environment

```bash
cp .env.example .env
nano .env  # Edit with production credentials
```

Update `.env` with production values:
```env
DB_HOST=localhost
DB_PORT=3306
DB_NAME=xlerion_db
DB_USER=xlerion_user
DB_PASS=secure_password_here
DB_CHARSET=utf8mb4

APP_ENV=production
APP_DEBUG=false
APP_URL=https://xlerion.com

API_VERSION=v1
```

### 4. Set Up Database

```bash
mysql -u root -p
```

```sql
CREATE DATABASE xlerion_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'xlerion_user'@'localhost' IDENTIFIED BY 'secure_password_here';
GRANT ALL PRIVILEGES ON xlerion_db.* TO 'xlerion_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

Import schema:
```bash
mysql -u xlerion_user -p xlerion_db < database/schema.sql
```

### 5. Build React Application

```bash
npm install --production
npm run build
```

### 6. Set File Permissions

```bash
# Set ownership
sudo chown -R www-data:www-data /var/www/xlerion

# Set directory permissions
sudo find /var/www/xlerion -type d -exec chmod 755 {} \;

# Set file permissions
sudo find /var/www/xlerion -type f -exec chmod 644 {} \;

# Protect .env file
sudo chmod 600 /var/www/xlerion/.env

# Make sure public directory is readable
sudo chmod 755 /var/www/xlerion/public
```

### 7. Configure Apache Virtual Host

Create `/etc/apache2/sites-available/xlerion.conf`:

```apache
<VirtualHost *:80>
    ServerName xlerion.com
    ServerAlias www.xlerion.com
    ServerAdmin admin@xlerion.com
    
    DocumentRoot /var/www/xlerion/public
    
    <Directory /var/www/xlerion/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    # API endpoint configuration
    Alias /api /var/www/xlerion/api
    <Directory /var/www/xlerion/api>
        Options -Indexes
        AllowOverride All
        Require all granted
        
        <FilesMatch "\.php$">
            SetHandler "proxy:unix:/var/run/php/php8.1-fpm.sock|fcgi://localhost"
        </FilesMatch>
    </Directory>
    
    # Logging
    ErrorLog ${APACHE_LOG_DIR}/xlerion_error.log
    CustomLog ${APACHE_LOG_DIR}/xlerion_access.log combined
    
    # Security headers
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-XSS-Protection "1; mode=block"
</VirtualHost>
```

Enable the site:
```bash
sudo a2ensite xlerion
sudo systemctl reload apache2
```

### 8. SSL/HTTPS Setup (Recommended)

Install Certbot:
```bash
sudo apt install certbot python3-certbot-apache
```

Get SSL certificate:
```bash
sudo certbot --apache -d xlerion.com -d www.xlerion.com
```

## Option 2: Deploy with Nginx

### 1. Nginx Configuration

Create `/etc/nginx/sites-available/xlerion`:

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name xlerion.com www.xlerion.com;
    
    root /var/www/xlerion/public;
    index index.html;
    
    # Logging
    access_log /var/log/nginx/xlerion_access.log;
    error_log /var/log/nginx/xlerion_error.log;
    
    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    
    # Main location - serve React app
    location / {
        try_files $uri $uri/ /index.html;
    }
    
    # API endpoint
    location /api {
        alias /var/www/xlerion/api;
        
        location ~ \.php$ {
            include snippets/fastcgi-php.conf;
            fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
            fastcgi_param SCRIPT_FILENAME $request_filename;
        }
        
        # CORS headers
        add_header Access-Control-Allow-Origin * always;
        add_header Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS" always;
        add_header Access-Control-Allow-Headers "Content-Type, Authorization" always;
    }
    
    # Deny access to sensitive files
    location ~ /\. {
        deny all;
    }
    
    location ~ /(config|database|src|node_modules) {
        deny all;
    }
    
    # Static assets caching
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff|woff2|ttf|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

Enable the site:
```bash
sudo ln -s /etc/nginx/sites-available/xlerion /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 2. SSL with Certbot

```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d xlerion.com -d www.xlerion.com
```

## Option 3: Build Locally, Deploy Files

### 1. Build on Development Machine

```bash
npm install
npm run build
```

### 2. Deploy Files

Upload these directories/files to your server:
- `api/` - All PHP API files
- `config/` - Configuration files
- `database/` - Database schema
- `public/` - Built React application
- `.env` - Environment configuration (create on server, don't upload from dev)
- `server.php` - PHP router (optional, for testing)

### 3. Configure on Server

Follow steps 3-6 from Apache deployment above.

## Post-Deployment

### 1. Test the Site

```bash
curl http://localhost/
curl http://localhost/api/example.php
```

### 2. Monitor Logs

Apache:
```bash
tail -f /var/log/apache2/xlerion_error.log
```

Nginx:
```bash
tail -f /var/log/nginx/xlerion_error.log
```

### 3. Database Backups

Set up automated backups:
```bash
# Create backup script
cat > /usr/local/bin/backup-xlerion-db.sh << 'EOF'
#!/bin/bash
BACKUP_DIR="/var/backups/xlerion"
DATE=$(date +%Y%m%d_%H%M%S)
mkdir -p $BACKUP_DIR
mysqldump -u xlerion_user -p'secure_password_here' xlerion_db > $BACKUP_DIR/xlerion_db_$DATE.sql
# Keep only last 7 days
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
EOF

chmod +x /usr/local/bin/backup-xlerion-db.sh

# Add to crontab (daily at 2 AM)
echo "0 2 * * * /usr/local/bin/backup-xlerion-db.sh" | sudo crontab -
```

## Updating the Site

### 1. Pull Latest Changes

```bash
cd /var/www/xlerion
git pull origin main
```

### 2. Update Dependencies (if needed)

```bash
npm install
```

### 3. Rebuild

```bash
npm run build
```

### 4. Update Database (if schema changed)

```bash
# Review schema changes first
mysql -u xlerion_user -p xlerion_db < database/schema.sql
```

### 5. Clear Cache (if applicable)

```bash
# Apache
sudo systemctl reload apache2

# Nginx
sudo systemctl reload nginx

# PHP OPcache
sudo systemctl restart php8.1-fpm
```

## Troubleshooting

### Issue: 500 Internal Server Error

1. Check Apache/Nginx error logs
2. Verify PHP version: `php -v`
3. Check `.env` file exists and has correct permissions
4. Verify database connection credentials

### Issue: API Returns 404

1. Verify rewrite rules are enabled
2. Check `.htaccess` files are in place
3. For Apache: Ensure `AllowOverride All` is set
4. For Nginx: Verify location blocks are correct

### Issue: React App Shows Blank Page

1. Check browser console for JavaScript errors
2. Verify `public/js/bundle.js` exists
3. Rebuild the application: `npm run build`
4. Check file permissions

### Issue: Database Connection Fails

1. Verify MariaDB is running: `sudo systemctl status mariadb`
2. Test connection: `mysql -u xlerion_user -p xlerion_db`
3. Check `.env` file has correct credentials
4. Verify user has necessary privileges

## Security Checklist

- [ ] `.env` file is not publicly accessible
- [ ] Database user has minimal required privileges
- [ ] SSL/HTTPS is configured and forced
- [ ] Security headers are set
- [ ] Directory listing is disabled
- [ ] File permissions are correct (644 for files, 755 for directories)
- [ ] Sensitive directories (config, database, src) are not web-accessible
- [ ] Regular backups are configured
- [ ] Error reporting is disabled in production (APP_DEBUG=false)
- [ ] Keep PHP, MariaDB, and system packages updated

## Performance Optimization

1. **Enable Gzip/Brotli compression**
2. **Use a CDN** for static assets
3. **Enable browser caching** (already configured in .htaccess)
4. **Use PHP OPcache**
5. **Optimize database** with proper indexes
6. **Consider Redis** for session/cache management
7. **Minify assets** (already done by webpack)

## Support

For issues, check:
- Application logs
- Web server logs
- PHP error logs
- Database logs
- Browser developer console
