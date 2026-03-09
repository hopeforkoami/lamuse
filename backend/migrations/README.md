# Database Migrations System

This project uses a custom migration runner to manage database schema changes and track executed migrations.

## Commands

To run and manage migrations, use the `runner.php` script located in `backend/migrations/`.

### Docker Usage (Recommended)

If you are running the project using Docker, use the following commands:

- **Run all pending migrations:**
  ```bash
  docker exec lamuse-php-1 php /var/www/backend/migrations/runner.php --all
  ```

- **Check migration status:**
  ```bash
  docker exec lamuse-php-1 php /var/www/backend/migrations/runner.php --status
  ```

- **Run a specific migration file:**
  ```bash
  docker exec lamuse-php-1 php /var/www/backend/migrations/runner.php --file=20240309000001_create_users_table
  ```

### Local Execution

If you have PHP installed locally (version 8.2+ required) and your environment is configured, you can run it directly from the project root:

```bash
php backend/migrations/runner.php --all
```

## Seeding Data

After running migrations, you can seed the database with initial data:

- **Main Seeder:**
  ```bash
  docker exec lamuse-php-1 php /var/www/backend/migrations/seed.php
  ```

- **Dashboard Seeder:**
  ```bash
  docker exec lamuse-php-1 php /var/www/backend/migrations/seed_dashboard.php
  ```

## Structure

- `backend/migrations/runner.php`: The main entry point for managing migrations.
- `backend/migrations/list/`: Contains individual versioned migration files.
- `migrations` table: Automatically created in the database to track executed files.
