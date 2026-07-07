FROM php:8.1-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Create storage directories if they don't exist
RUN mkdir -p /var/www/html/storage/app/public \
    && mkdir -p /var/www/html/storage/framework/cache \
    && mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/views \
    && mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/bootstrap/cache

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy Apache configuration
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Copy SQL file for database import
COPY database.sql /tmp/database.sql

# Create startup script with inline PHP import
RUN printf '#!/bin/bash\necho "Starting Shortzz Backend..."\nphp artisan config:clear\nphp artisan config:cache\nphp artisan route:cache\nphp artisan view:cache\necho "Importing database schema..."\nphp -r "\n\\$host = getenv(\"DB_HOST\");\n\\$port = getenv(\"DB_PORT\");\n\\$dbname = getenv(\"DB_DATABASE\");\n\\$user = getenv(\"DB_USERNAME\");\n\\$pass = getenv(\"DB_PASSWORD\");\nif (!\\$host || !\\$dbname || !\\$user || !\\$pass) { echo \"DB vars not set, skipping\\n\"; exit(0); }\ntry {\n  \\$pdo = new PDO(\"mysql:host=\\$host;port=\\$port;dbname=\\$dbname;charset=utf8mb4\", \\$user, \\$pass, [PDO::MYSQL_ATTR_SSL_CA=>\"/etc/ssl/certs/ca-certificates.crt\", PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT=>false]);\n  echo \"Connected to TiDB!\\n\";\n  \\$tables = \\$pdo->query(\"SHOW TABLES\")->fetchAll();\n  if (count(\\$tables) > 0) { echo \"Tables exist (\".count(\\$tables).\"), skipping.\\n\"; exit(0); }\n  echo \"Importing SQL...\\n\";\n  \\$sql = file_get_contents(\"/tmp/database.sql\");\n  \\$sql = preg_replace(\"/SET SQL_MODE.*?;/\", \"\", \\$sql);\n  \\$sql = preg_replace(\"/START TRANSACTION;/\", \"\", \\$sql);\n  \\$sql = preg_replace(\"/COMMIT;/\", \"\", \\$sql);\n  \\$sql = preg_replace(\"/\\/\\*!40101.*?\\*\\//\", \"\", \\$sql);\n  \\$stmts = array_filter(array_map(\"trim\", explode(\";\", \\$sql)));\n  \\$count = 0;\n  foreach (\\$stmts as \\$s) {\n    if (empty(\\$s) || preg_match(\"/^--/\", \\$s) || preg_match(\"/^\\/\\*/, \\$s)) continue;\n    try { \\$pdo->exec(\\$s); \\$count++; } catch (PDOException \\$e) {}\n  }\n  echo \"Imported \\$count statements.\\n\";\n} catch (PDOException \\$e) { echo \"DB error: \".\\$e->getMessage().\"\\n\"; }\n" 2>&1 || true\nchown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache\nexec apache2-foreground\n' > /start.sh && chmod +x /start.sh

EXPOSE 80

CMD ["/start.sh"]
