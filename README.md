# Sfera

Laravel 8 project skeleton — Wildberries data import service.

## Quick Start

```bash
# Start Docker containers
docker compose up -d

# Install PHP dependencies
docker compose exec app composer install

# Generate application key
docker compose exec app php artisan key:generate

# Open in browser
open http://localhost:8080
```

## Health Check

```bash
curl http://localhost:8080
curl http://localhost:8080/api/health
# -> {"status":"ok"}
```

## Import Command

```bash
# Import single entity
docker compose exec app php artisan wb:import --entity=stocks
docker compose exec app php artisan wb:import --entity=sales --from=2024-01-01 --to=2024-01-31

# Import all entities
docker compose exec app php artisan wb:import --entity=all
```

## Project Structure (WIP)

| Layer | Location | Status |
|-------|----------|--------|
| HTTP client | `app/Services/Wb/WbApiClient.php` | TODO: implement HTTP requests |
| Import logic | `app/Services/Wb/WbImportService.php` | TODO: fetch + DB persist |
| CLI command | `app/Console/Commands/ImportWbData.php` | Skeleton ready |
| API config | `config/services.php` → `wb_api` | Configured |
| Env vars | `WB_API_HOST`, `WB_API_KEY` in `.env` | Configured |

**Note:** Migrations, models, and DB persistence are not yet implemented — they will be added in the next step.

## Useful Commands

```bash
# Enter container shell
docker compose exec app bash

# List routes
docker compose exec app php artisan route:list

# Stop containers
docker compose down
```
