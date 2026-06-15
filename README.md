# Sfera

Laravel 8 project — импорт данных Wildberries (продажи, заказы, склады, доходы).

## Quick Start

```bash
docker compose up -d
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate
open http://localhost:8080
```

## Database Access

| Параметр | Значение |
|----------|----------|
| Host | `db` (изнутри контейнеров) / `127.0.0.1` (снаружи) |
| Port | `33060` |
| Database | `sfera` |
| Username | `root` |
| Password | `secret` |
| Driver | mysql |

Подключение извне:

```bash
mysql -h 127.0.0.1 -P 33060 -u root -psecret sfera
```

## Tables

| Таблица | Описание | Записей* |
|---------|----------|----------|
| `stocks` | Остатки на складах | ~5k |
| `sales` | Продажи | ~1.5k |
| `orders` | Заказы | ~900 |
| `incomes` | Поставки | 0 (нет данных в API) |

_* Примерное количество за 7-дневный период. Таблицы содержат колонку `raw_data` (JSON) с полным ответом API._

### Структура таблиц

Все таблицы имеют:
- `external_hash` CHAR(32) UNIQUE — хеш для дедупликации
- `raw_data` JSON — полный ответ API
- `imported_at` TIMESTAMP — время импорта

**stocks** — `date`, `last_change_date`, `supplier_article`, `tech_size`, `barcode`, `quantity`, `is_supply`, `is_realization`, `quantity_full`, `warehouse_name`, `in_way_to_client`, `in_way_from_client`, `nm_id`, `subject`, `category`, `brand`, `sc_code`, `price`, `discount`

**sales** — `g_number`, `date`, `last_change_date`, `supplier_article`, `tech_size`, `barcode`, `total_price`, `discount_percent`, `is_supply`, `is_realization`, `promo_code_discount`, `warehouse_name`, `country_name`, `oblast_okrug_name`, `region_name`, `income_id`, `sale_id`, `odid`, `spp`, `for_pay`, `finished_price`, `price_with_disc`, `nm_id`, `subject`, `category`, `brand`, `is_storno`

**orders** — `g_number`, `date`, `last_change_date`, `supplier_article`, `tech_size`, `barcode`, `total_price`, `discount_percent`, `warehouse_name`, `oblast`, `income_id`, `odid`, `nm_id`, `subject`, `category`, `brand`, `is_cancel`, `cancel_dt`

**incomes** — `income_id`, `number`, `date`, `last_change_date`, `supplier_article`, `tech_size`, `barcode`, `quantity`, `total_price`, `date_close`, `warehouse_name`, `nm_id`, `status`

## Import Command

```bash
# Импорт одной сущности
docker compose exec app php artisan wb:import --entity=stocks
docker compose exec app php artisan wb:import --entity=sales --from=2026-06-01 --to=2026-06-14
docker compose exec app php artisan wb:import --entity=orders --from=2026-06-01 --to=2026-06-14
docker compose exec app php artisan wb:import --entity=incomes --from=2026-06-01 --to=2026-06-14

# Импорт всех сущностей
docker compose exec app php artisan wb:import --entity=all --from=2026-06-01 --to=2026-06-14
```

## API Endpoints

| Method | Path | Params | Description |
|--------|------|--------|-------------|
| GET | `/api/health` | — | Health check |
| GET | `/` | — | App status |

## API Source

- Auth: параметр `key` в query string
- Date format: `Y-m-d`
- Pagination: `page` + `limit` (max 500)

Конфигурация в `.env`:
```
WB_API_HOST=
WB_API_KEY=
```

## Project Structure

| Layer | Location |
|-------|----------|
| HTTP client | `app/Services/Wb/WbApiClient.php` |
| Import logic | `app/Services/Wb/WbImportService.php` |
| CLI command | `app/Console/Commands/ImportWbData.php` |
| Models | `app/Models/Stock.php`, `Sale.php`, `Order.php`, `Income.php` |
| Migrations | `database/migrations/` |
| API config | `config/services.php` |

## Useful Commands

```bash
docker compose exec app bash
docker compose exec app php artisan route:list
docker compose exec app php artisan migrate:status
docker compose down
```
