# DIR-EXPENSE: LXC Deployment Guide

This document provides definitive instructions for deploying the **DIR-EXPENSE** application to a Proxmox or standalone Linux Container (LXC). 

## 1. Minimal Specification
- **OS**: Ubuntu 22.04 or 24.04 LTS
- **Disk**: 10GB (SSD preferred)
- **RAM**: 1GB (2GB recommended for build processes)
- **PHP**: 8.4

## 2. Server Preparation

Connect to your LXC via SSH and install the core stack:

```bash
# Update and Upgrade
sudo apt update && sudo apt upgrade -y

# Add PHP 8.4 Repository
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install Stack
sudo apt install -y nginx php8.4-fpm php8.4-cli php8.4-mbstring php8.4-xml php8.4-curl \
    php8.4-sqlite3 php8.4-gd php8.4-zip php8.4-bcmath php8.4-intl nodejs npm

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

## 3. Deployment & Installation

Navigate to `/var/www` and clone your repository (replace the URL with your actual PAT-authenticated or SSH URL):

```bash
cd /var/www
git clone https://github.com/mivu2k/dir-exp.git
cd dir-exp

# Set Permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Install PHP & JS Dependencies
composer install --no-dev --optimize-autoloader
npm install && npm run build
```

## 4. Environment Configuration

```bash
cp .env.example .env
nano .env
```

**Key Production Settings:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-domain-or-ip

DB_CONNECTION=sqlite
# Database is automatically created at database/database.sqlite
```

**Database Initialization:**
```bash
# Create SQLite DB
touch database/database.sqlite

# Security & Migrations
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
```

## 5. Architectural Stability Notes

> [!IMPORTANT]
> **Pre-Sorted Migrations**: All database migrations have been renamed to follow a strictly sequential dependency order (`094346` -> `094347` -> `094348`). This eliminates "Foreign Key" or "Table not found" errors during the `migrate --force` step.

> [!TIP]
> **Atomic Voucher Logic**: The application uses a database-level "creating" hook to generate voucher numbers (e.g., `DIR-202404-001`). This logic is collision-proof and accounts for soft-deleted records, ensuring high reliability in multi-user environments.


## 6. Nginx Web Server Setup

`sudo nano /etc/nginx/sites-available/dir-expense`

```nginx
server {
    listen 80;
    server_name your-domain-or-ip;
    root /var/www/dir-exp/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

**Enable and Restart:**
```bash
sudo ln -s /etc/nginx/sites-available/dir-expense /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

## 7. Performance Optimization

Run these commands once for maximum production speed:
```bash
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
