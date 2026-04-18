# DIR-EXPENSE: Final Deployment Guide (LXC & Production)

This guide provides a comprehensive roadmap for deploying **DIR-EXPENSE** in a production-grade Linux Container (LXC).

## 1. System Requirements & Stack
- **OS**: Ubuntu 22.04+ (Recommended)
- **PHP**: 8.4 (with sqlite3, fpm, gd, zip, bcmath, intl)
- **Web Server**: Nginx
- **Database**: SQLite 3

## 2. Server Provisioning

Run these commands inside your LXC to prepare the environment:

```bash
# Core System Update
sudo apt update && sudo apt upgrade -y

# Add PHP 8.4 Repository
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install Essential Stack
sudo apt install -y nginx php8.4-fpm php8.4-cli php8.4-mbstring php8.4-xml php8.4-curl \
    php8.4-sqlite3 php8.4-gd php8.4-zip php8.4-bcmath php8.4-intl nodejs npm

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

## 3. Deployment Steps

Clone the repository and initialize the project components:

```bash
cd /var/www
git clone https://github.com/mivu2k/dir-exp.git
cd dir-exp

# Set Production Permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Install Dependencies
composer install --no-dev --optimize-autoloader
npm install && npm run build
```

## 4. Environment Stabilization

```bash
cp .env.example .env
nano .env
```

**Mandatory Adjustments:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-app-domain.com

DB_CONNECTION=sqlite
# The database file must exist at /var/www/dir-exp/database/database.sqlite
```

**Initialize Database & Assets:**
```bash
# Create and secure the database
touch database/database.sqlite
sudo chown www-data:www-data database/database.sqlite
sudo chmod 664 database/database.sqlite

# Migration & Seeding
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force

# Create the storage symlink for attachments
php artisan storage:link
```

## 5. Nginx & FPM Configuration

`sudo nano /etc/nginx/sites-available/dir-expense`

```nginx
server {
    listen 80;
    server_name your-app-domain.com;
    root /var/www/dir-exp/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    index index.php;
    charset utf-8;

    # Performance logging
    access_log off;
    error_log  /var/log/nginx/dir-expense.error.log error;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.4-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

**Activate Site:**
```bash
sudo ln -s /etc/nginx/sites-available/dir-expense /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl restart nginx
```

## 6. Maintenance & Automation

> [!TIP]
> **Scheduler (Crontab)**: Ensure Laravel's background tasks run correctly (e.g., for automated reporting or cleanups).
> Add this line to `crontab -e`:
> `* * * * * cd /var/www/dir-exp && php artisan schedule:run >> /dev/null 2>&1`

> [!IMPORTANT]
> **SQLite Backup**: Since SQLite is a file-based database, backups are simple but critical. 
> Create a daily cron to copy `database/database.sqlite` to a secure off-container location.

## 7. Troubleshooting LXC Permissions

If you encounter "Permission Denied" errors in an **Unprivileged LXC Container**, ensure the `www-data` user (ID 33) has recursive ownership of the web root:
`sudo chown -R 33:33 /var/www/dir-exp`

## 8. Final Optimization
```bash
php artisan optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
