# CamGovCA Deployment Guide

This guide provides step-by-step instructions for deploying the CamGovCA system in various environments.

## 🏗️ System Requirements

### Minimum Requirements
- **PHP**: 7.4 or higher
- **MySQL**: 5.7 or higher (or MariaDB 10.2+)
- **Web Server**: Apache 2.4+ or Nginx 1.18+
- **RAM**: 2GB minimum, 4GB recommended
- **Storage**: 10GB minimum for application + database

### PHP Extensions Required
- `openssl` - For certificate operations
- `mysqli` - For database connectivity
- `json` - For API responses
- `session` - For user sessions
- `mbstring` - For string operations
- `curl` - For external API calls
- `gd` - For image processing (if needed)

## 🚀 Production Deployment

### 1. Server Preparation

#### Update System
```bash
sudo apt update && sudo apt upgrade -y
```

#### Install Required Software
```bash
# Install Apache, PHP, and MySQL
sudo apt install apache2 php7.4 php7.4-mysql php7.4-openssl php7.4-json php7.4-mbstring php7.4-curl php7.4-gd mysql-server

# Or for Nginx
sudo apt install nginx php7.4-fpm php7.4-mysql php7.4-openssl php7.4-json php7.4-mbstring php7.4-curl php7.4-gd mysql-server
```

### 2. Database Setup

#### Create Database and User
```sql
CREATE DATABASE camgovca CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'camgovca_user'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON camgovca.* TO 'camgovca_user'@'localhost';
FLUSH PRIVILEGES;
```

#### Import Database Schema
```bash
mysql -u camgovca_user -p camgovca < src/database/camgovca_database_schema.sql
mysql -u camgovca_user -p camgovca < src/database/camgovca_database_data.sql
```

### 3. Application Deployment

#### Clone Repository
```bash
cd /var/www/
sudo git clone https://github.com/your-username/camgovca.git
sudo chown -R www-data:www-data camgovca/
```

#### Configure Application
```bash
# Copy configuration templates
sudo cp src/config/database.php.example src/config/database.php
sudo cp src/config/email_config.php.example src/config/email_config.php

# Edit configuration files
sudo nano src/config/database.php
sudo nano src/config/email_config.php
```

#### Set Proper Permissions
```bash
sudo chmod 755 src/public/
sudo chmod 644 src/config/*.php
sudo chmod 755 src/logs/
sudo chmod 755 src/backups/
```

### 4. Web Server Configuration

#### Apache Configuration
Create `/etc/apache2/sites-available/camgovca.conf`:
```apache
<VirtualHost *:80>
    ServerName camgovca.yourdomain.com
    ServerAdmin webmaster@yourdomain.com
    DocumentRoot /var/www/camgovca/src/public
    
    <Directory /var/www/camgovca/src/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/camgovca_error.log
    CustomLog ${APACHE_LOG_DIR}/camgovca_access.log combined
    
    # Security headers
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains"
</VirtualHost>
```

Enable the site:
```bash
sudo a2ensite camgovca.conf
sudo a2enmod rewrite
sudo systemctl reload apache2
```

#### Nginx Configuration
Create `/etc/nginx/sites-available/camgovca`:
```nginx
server {
    listen 80;
    server_name camgovca.yourdomain.com;
    root /var/www/camgovca/src/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock;
    }

    # Security headers
    add_header X-Content-Type-Options nosniff;
    add_header X-Frame-Options DENY;
    add_header X-XSS-Protection "1; mode=block";
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains";

    # Deny access to sensitive files
    location ~ /\. {
        deny all;
    }
    
    location ~ /(config|includes|database)/ {
        deny all;
    }
}
```

Enable the site:
```bash
sudo ln -s /etc/nginx/sites-available/camgovca /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 5. SSL/TLS Configuration

#### Using Let's Encrypt
```bash
sudo apt install certbot python3-certbot-apache
sudo certbot --apache -d camgovca.yourdomain.com

# Or for Nginx
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d camgovca.yourdomain.com
```

### 6. Security Hardening

#### Firewall Configuration
```bash
sudo ufw allow 22/tcp
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw enable
```

#### PHP Security
Edit `/etc/php/7.4/apache2/php.ini`:
```ini
expose_php = Off
max_execution_time = 30
memory_limit = 256M
upload_max_filesize = 10M
post_max_size = 10M
session.cookie_httponly = 1
session.cookie_secure = 1
session.use_strict_mode = 1
```

### 7. Monitoring and Logging

#### Set up Log Rotation
Create `/etc/logrotate.d/camgovca`:
```
/var/www/camgovca/src/logs/*.log {
    daily
    missingok
    rotate 30
    compress
    delaycompress
    notifempty
    create 644 www-data www-data
}
```

#### Monitoring Script
Create a monitoring script to check system health:
```bash
#!/bin/bash
# /usr/local/bin/camgovca-monitor.sh

# Check if web server is running
if ! systemctl is-active --quiet apache2; then
    echo "Apache is down!" | mail -s "CamGovCA Alert" admin@yourdomain.com
fi

# Check disk space
DISK_USAGE=$(df / | awk 'NR==2 {print $5}' | sed 's/%//')
if [ $DISK_USAGE -gt 80 ]; then
    echo "Disk usage is ${DISK_USAGE}%" | mail -s "CamGovCA Alert" admin@yourdomain.com
fi
```

## 🔧 Development Environment

### Using Docker
Create `docker-compose.yml`:
```yaml
version: '3.8'
services:
  web:
    image: php:7.4-apache
    ports:
      - "8080:80"
    volumes:
      - ./src:/var/www/html
    depends_on:
      - db
    environment:
      - APACHE_DOCUMENT_ROOT=/var/www/html/public

  db:
    image: mysql:5.7
    environment:
      MYSQL_ROOT_PASSWORD: rootpassword
      MYSQL_DATABASE: camgovca
      MYSQL_USER: camgovca_user
      MYSQL_PASSWORD: camgovca_pass
    ports:
      - "3306:3306"
    volumes:
      - mysql_data:/var/lib/mysql

volumes:
  mysql_data:
```

Run with:
```bash
docker-compose up -d
```

## 📊 Performance Optimization

### Database Optimization
```sql
-- Add indexes for better performance
CREATE INDEX idx_certificates_status ON certificates(status);
CREATE INDEX idx_certificates_expiry ON certificates(expiry_date);
CREATE INDEX idx_users_email ON users(email);
```

### PHP OPcache
Enable OPcache in `/etc/php/7.4/apache2/php.ini`:
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
```

### Caching
Consider implementing Redis for session storage and caching:
```bash
sudo apt install redis-server
```

## 🔄 Backup Strategy

### Database Backups
Create `/usr/local/bin/camgovca-backup.sh`:
```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/var/backups/camgovca"
mkdir -p $BACKUP_DIR

# Database backup
mysqldump -u camgovca_user -p camgovca > $BACKUP_DIR/db_backup_$DATE.sql

# Application backup
tar -czf $BACKUP_DIR/app_backup_$DATE.tar.gz /var/www/camgovca/

# Keep only last 7 days of backups
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete
```

Add to crontab:
```bash
0 2 * * * /usr/local/bin/camgovca-backup.sh
```

## 🚨 Troubleshooting

### Common Issues

1. **Permission Denied Errors**
   ```bash
   sudo chown -R www-data:www-data /var/www/camgovca/
   sudo chmod -R 755 /var/www/camgovca/src/public/
   ```

2. **Database Connection Issues**
   - Check database credentials in `src/config/database.php`
   - Verify MySQL service is running: `sudo systemctl status mysql`

3. **SSL Certificate Issues**
   - Check certificate validity: `openssl x509 -in /etc/letsencrypt/live/domain/fullchain.pem -text -noout`
   - Renew certificates: `sudo certbot renew`

4. **Performance Issues**
   - Check PHP error logs: `tail -f /var/log/apache2/error.log`
   - Monitor database queries: `mysql slow query log`

## 📞 Support

For deployment issues:
1. Check the logs in `src/logs/`
2. Review web server error logs
3. Verify all requirements are met
4. Contact the development team

---

**Important**: Always test the deployment in a staging environment before going live with production. 