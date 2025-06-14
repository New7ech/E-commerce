# Deployment Guide

This guide provides instructions for setting up the application in a new environment, documenting necessary environment variables, dependencies, and setup steps.

## 1. Environment Variables

Create a `.env` file by copying from `.env.example` (`cp .env.example .env`). The following variables need to be configured:

### Standard Laravel Variables
-   `APP_NAME="Your Application Name"` (e.g., "Laravel E-commerce")
-   `APP_ENV=production` (set to `local` or `staging` for non-production environments)
-   `APP_KEY=` (generate using `php artisan key:generate`)
-   `APP_DEBUG=false` (set to `true` only for local development/debugging)
-   `APP_URL=http://yourdomain.com` (your application's public URL)
-   `LOG_CHANNEL=stack` (or `daily`, `stderr` depending on your logging preference)
-   `LOG_LEVEL=error` (or `debug`, `info` as appropriate for the environment)

### Database Configuration
-   `DB_CONNECTION=mysql` (or `pgsql`, `sqlite`, `sqlsrv`)
-   `DB_HOST=127.0.0.1` (database host)
-   `DB_PORT=3306` (database port)
-   `DB_DATABASE=your_database_name`
-   `DB_USERNAME=your_database_user`
-   `DB_PASSWORD=your_database_password`

### Mail Driver Configuration
-   `MAIL_MAILER=smtp` (or `log`, `mailgun`, `ses`, `postmark`, `sendmail`)
-   `MAIL_HOST=smtp.mailtrap.io` (your mail server host)
-   `MAIL_PORT=2525` (your mail server port)
-   `MAIL_USERNAME=null` (your mail server username)
-   `MAIL_PASSWORD=null` (your mail server password)
-   `MAIL_ENCRYPTION=tls` (or `ssl`, `null`)
-   `MAIL_FROM_ADDRESS="hello@example.com"` (default from email address)
-   `MAIL_FROM_NAME="${APP_NAME}"` (default from name)

### Session and Cache Drivers
-   `SESSION_DRIVER=file` (or `cookie`, `database`, `memcached`, `redis`)
-   `SESSION_LIFETIME=120` (session lifetime in minutes)
-   `CACHE_DRIVER=file` (or `memcached`, `redis`, `database`, `array`)
    *   If using `memcached` or `redis`, configure `MEMCACHED_HOST` or `REDIS_HOST`, `REDIS_PASSWORD`, `REDIS_PORT`.

### Queue Connection
-   `QUEUE_CONNECTION=sync` (or `database`, `redis`, `beanstalkd`, `sqs` for production workloads)
    *   If using a non-sync driver, configure relevant settings (e.g., `REDIS_HOST` for Redis).

### Payment Gateway Keys (Placeholders for Future Integration)
-   `STRIPE_KEY=` (Your Stripe publishable key)
-   `STRIPE_SECRET=` (Your Stripe secret key)
-   `PAYPAL_CLIENT_ID=`
-   `PAYPAL_SECRET=`
-   `PAYPAL_MODE=sandbox` (or `live`)

### Other Custom Configurations
-   (Add any other custom environment variables specific to your application here, e.g., third-party API keys, service URLs.)
-   `SANCTUM_STATEFUL_DOMAINS=yourdomain.com,localhost:3000` (if using Sanctum for SPA authentication)
-   `VITE_APP_URL="${APP_URL}"` (if using Vite, usually set by default)

## 2. Dependencies

### PHP Dependencies (managed by Composer)
-   `laravel/framework` (Core Laravel framework)
-   `laravel/breeze` (Authentication scaffolding, added during development)
-   `spatie/laravel-permission` (Likely used, based on project structure for roles/permissions)
-   Other dependencies as listed in `composer.json`.

### NPM Dependencies (managed by NPM/Yarn)
-   `tailwindcss`
-   `alpinejs`
-   `vite`
-   Other dependencies as listed in `package.json`.

### Placeholder for Future Payment Gateway SDKs
-   If Stripe is integrated: `stripe/stripe-php`
-   If PayPal is integrated: `paypal/paypal-checkout-sdk` or `srmklive/paypal-ipn`

## 3. Setup Steps for a New Environment

1.  **Clone Repository:**
    ```bash
    git clone <your-repository-url>
    cd <your-project-directory>
    ```

2.  **Install PHP Dependencies:**
    ```bash
    composer install --optimize-autoloader --no-dev
    ```
    (For production, `--no-dev` is recommended. For development, omit `--no-dev`.)

3.  **Install NPM Dependencies & Build Assets:**
    ```bash
    npm install
    npm run build
    ```

4.  **Create `.env` File:**
    Copy the example environment file and configure it:
    ```bash
    cp .env.example .env
    ```
    Open `.env` and fill in all necessary environment variables as documented above, especially `APP_NAME`, `APP_ENV`, `APP_KEY`, `APP_URL`, database credentials, and mail settings.

5.  **Generate Application Key:**
    If `APP_KEY` is not already set in your `.env` file:
    ```bash
    php artisan key:generate
    ```

6.  **Run Database Migrations:**
    ```bash
    php artisan migrate --force
    ```
    (The `--force` flag is recommended for production to run without prompts.)

7.  **Seed Database (Optional but Recommended):**
    If you have seeders for default data (e.g., admin user, roles, permissions, categories):
    ```bash
    php artisan db:seed
    ```
    Or specify a particular seeder:
    ```bash
    php artisan db:seed --class=YourSeederClass
    ```

8.  **Set Up Storage Link:**
    To make the `storage/app/public` directory accessible from the web:
    ```bash
    php artisan storage:link
    ```

9.  **Configure Web Server:**
    Configure your web server (e.g., Nginx, Apache) to point its document root to the project's `public` directory. Ensure URL rewriting is enabled for Laravel's front controller pattern.
    Example Nginx configuration snippet:
    ```nginx
    server {
        listen 80;
        server_name yourdomain.com;
        root /path-to-your-project/public;

        add_header X-Frame-Options "SAMEORIGIN";
        add_header X-XSS-Protection "1; mode=block";
        add_header X-Content-Type-Options "nosniff";

        index index.php;

        charset utf-8;

        location / {
            try_files $uri $uri/ /index.php?$query_string;
        }

        location = /favicon.ico { access_log off; log_not_found off; }
        location = /robots.txt  { access_log off; log_not_found off; }

        error_page 404 /index.php;

        location ~ \.php$ {
            fastcgi_pass unix:/var/run/php/php8.x-fpm.sock; # Adjust PHP version
            fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
            include fastcgi_params;
        }

        location ~ /\.(?!well-known).* {
            deny all;
        }
    }
    ```

10. **Set Up Task Scheduling:**
    Add the following Cron entry to your server to run Laravel's scheduled tasks (e.g., for queues, scheduled jobs):
    ```cron
    * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
    ```

11. **Admin User Creation/Promotion:**
    *   **Via Seeder (Recommended):** If a `UserSeeder` or `AdminUserSeeder` is configured to create an admin user and assign the 'admin' role (using Spatie permissions or by setting an `is_admin` flag), running `php artisan db:seed` (or the specific seeder) should handle this.
    *   **Via Tinker (Manual):**
        ```bash
        php artisan tinker
        ```
        Then in Tinker:
        ```php
        // Option 1: Using a boolean flag (if 'is_admin' field exists)
        // $user = App\Models\User::where('email', 'admin@example.com')->first();
        // if ($user) {
        //     $user->is_admin = true;
        //     $user->save();
        //     echo "User promoted to admin.\n";
        // } else {
        //     App\Models\User::factory()->create(['name' => 'Admin User', 'email' => 'admin@example.com', 'password' => bcrypt('password'), 'is_admin' => true]);
        //     echo "Admin user created.\n";
        // }

        // Option 2: Using Spatie Roles (if Spatie is fully configured)
        // $user = App\Models\User::firstOrCreate(['email' => 'admin@example.com'], ['name' => 'Admin User', 'password' => bcrypt('password')]);
        // $adminRole = Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        // $user->assignRole($adminRole);
        // echo "Admin user created/assigned admin role.\n";
        ```
    *   **Direct Database Update:** Manually set the `is_admin` field to `1` (or true) for the desired user in the `users` table, or assign the role in Spatie's `model_has_roles` table. This is generally discouraged for consistency.

    **Note:** The application uses an `'admin'` middleware for admin routes. Ensure this middleware is correctly configured in `app/Http/Kernel.php` to check either the `is_admin` flag or the 'admin' role via Spatie.

12. **Cache Configuration (Production):**
    For optimal performance in production, cache your configuration, routes, and views:
    ```bash
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    ```
    To clear cache during deployment updates:
    ```bash
    php artisan config:clear
    php artisan route:clear
    php artisan view:clear
    php artisan cache:clear
    ```
    Then re-cache.

## 4. Application Maintenance
To enable maintenance mode:
```bash
php artisan down
```
To disable maintenance mode:
```bash
php artisan up
```

This completes the deployment guide.
