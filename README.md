# Dashboard Project

This project is a small, production-style demo that showcases DDD + CQRS + event-driven architecture on Symfony 7 with PHP 8.2.

## Architecture Decisions

- **DDD boundaries**: Domain logic and events live under `src/Domain`. Application handlers live under `src/Application`. Persistence adapters live under `src/Infrastructure`.
- **CQRS**: Reads use a dedicated query (`GetDashboardQuery`) and handler, separated from write-side operations.
- **Event-driven + async**: After importing data, a domain event (`DashboardDataImported`) is dispatched and handled asynchronously to rebuild the read model.
- **Optimized read model**: `dashboard_read_model` is a denormalized projection for fast reads.
- **Caching**: Read-side queries are cached for a short TTL to reduce database load.
- **Logging/performance**: Query and projection handlers log timing and key metadata to support basic performance tracking.

## Setup Instructions

1. Install dependencies:
```powershell
composer install
```

2. Configure the database in `.env`:
```
DATABASE_URL="mysql://root@127.0.0.1:3306/dashboard_db"
```

3. Run migrations:
```powershell
php bin/console doctrine:migrations:migrate
```

4. Import sample data (100,000 rows):
```powershell
php bin/console app:import-dashboard
```

5. Start the async worker:
```powershell
php bin/console messenger:consume async -vv
```

## How to Run the Project

1. Start your web server (XAMPP/Apache).
2. Open the dashboard page:
```
http://localhost/dashboard
```

If the page is empty, rebuild the read model:
```powershell
php bin/console app:rebuild-dashboard-read-model
```

## How to Execute Unit Tests

1. Ensure the test database exists (MySQL):
```powershell
php bin/console doctrine:database:create --env=test
php bin/console doctrine:migrations:migrate --env=test
```

2. Run tests:
```powershell
php bin/phpunit
```

