# Dashboard Architecture Implementation

This document describes how the project satisfies the mandatory requirements and how to operate the solution.

## Requirements Checklist

- Symfony 6/7: Uses Symfony 7.x (`composer.json`).
- PHP 8.2+: Required via composer.
- DDD: Domain entities and events live under `src/Domain`. Application handlers live under `src/Application`. Infrastructure persistence lives under `src/Infrastructure`.
- CQRS: Query object `GetDashboardQuery` + query handler, and a separate async projection handler that rebuilds the read model.
- Event-Driven Architecture: Domain event `DashboardDataImported` is dispatched after import.
- Asynchronous Processing: `DashboardDataImported` is routed to the `async` transport and handled by `RebuildDashboardReadModelHandler`.
- Caching Layer: Query handler caches read model pages with `cache.app`.
- Optimized Read Models: Dedicated `dashboard_read_model` table populated via projection.
- Unit Tests: `tests/DashboardTest.php` and `tests/ReadModel/DashboardReadModelTest.php`.
- Logging/Performance: Query handler and projection handler log execution time and key metadata.

## Data Flow

1. `app:import-dashboard` inserts 100,000 rows into `dashboard`.
2. It dispatches `DashboardDataImported`.
3. Messenger consumes the async event.
4. `RebuildDashboardReadModelHandler` rebuilds `dashboard_read_model`.
5. `GetDashboardQueryHandler` reads from the read model and caches pages.

## How To Run

1. Run migrations:
   - `php bin/console doctrine:migrations:migrate`

2. Import data:
   - `php bin/console app:import-dashboard`

3. Start async consumer:
   - `php bin/console messenger:consume async -vv`

4. Open the dashboard:
   - `GET /dashboard`

## Key Files

- Domain entity: `src/Domain/Dashboard/Model/Dashboard.php`
- Domain event: `src/Domain/Dashboard/Event/DashboardDataImported.php`
- Query + handler: `src/Application/Query/GetDashboardQuery.php`, `src/Application/QueryHandler/GetDashboardQueryHandler.php`
- Read model entity: `src/ReadModel/Dashboard/DashboardReadModel.php`
- Read model repository: `src/Infrastructure/Persistence/Doctrine/DashboardReadModelRepository.php`
- Projection handler: `src/Application/EventHandler/RebuildDashboardReadModelHandler.php`
- Messenger routing: `config/packages/messenger.yaml`
- Migration: `migrations/Version20260311151500.php`

