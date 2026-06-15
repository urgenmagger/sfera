# Sfera

Laravel 8 + Octane (Swoole) — импорт и API выдача данных Wildberries (продажи, заказы, склады, доходы).

**Стек:** PHP 8.2, Laravel 8, Laravel Octane (Swoole), MySQL 8, Docker

## Test with Postman

1. Postman → Import → Link → `https://raw.githubusercontent.com/urgenmagger/sfera/main/postman.json`
2. Поменять переменную `base_url` на адрес сервера (например `http://157.22.252.36`)

## Quick Start (dev)

```bash
docker compose up -d
open http://localhost:8081
```

Dev-режим: nginx → php-fpm, код монтируется из `./src`, изменения подхватываются без пересборки.

## Production (Octane)

```bash
docker compose -f docker-compose.prod.yml up -d --build
open http://localhost
```

Prod-режим: Octane (Swoole) на порту `APP_PORT` (по умолчанию 80), код встроен в образ, без nginx.

Port override: `APP_PORT=8080 docker compose -f docker-compose.prod.yml up -d`

## API Endpoints

| Метод | Путь | Параметры |
|-------|------|-----------|
| GET | `/api/sales` | `dateFrom`, `dateTo`, `page`, `limit`, `key` |
| GET | `/api/orders` | `dateFrom`, `dateTo`, `page`, `limit`, `key` |
| GET | `/api/stocks` | `dateFrom`, `page`, `limit`, `key` |
| GET | `/api/incomes` | `dateFrom`, `dateTo`, `page`, `limit`, `key` |

- Auth: `?key=<WB_API_KEY>` в query string
- Pagination: `page`, `limit` (default 500, max 500)
- Date format: `Y-m-d`

Пример:

```bash
curl "http://localhost/api/sales?dateFrom=2026-06-14&dateTo=2026-06-15&limit=500&key=<token>"
```

## Import Command

```bash
# Отдельная сущность
docker compose exec app php artisan wb:import --entity=stocks
docker compose exec app php artisan wb:import --entity=sales --from=2026-06-14 --to=2026-06-14

# Все сущности
docker compose exec app php artisan wb:import --entity=all --from=2026-06-14 --to=2026-06-14
```

## Database

| Параметр | Значение |
|----------|----------|
| Host | `db` / `127.0.0.1` |
| Port | `33060` |
| Database | `sfera` |
| Username | `root` |
| Password | `secret` |

```bash
mysql -h 127.0.0.1 -P 33060 -u root -psecret sfera
```

## Tables

| Таблица | Колонки | Внешний ключ |
|---------|---------|--------------|
| `stocks` | `date`, `last_change_date`, `supplier_article`, `tech_size`, `barcode`, `quantity`, `is_supply`, `is_realization`, `quantity_full`, `warehouse_name`, `in_way_to_client`, `in_way_from_client`, `nm_id`, `subject`, `category`, `brand`, `sc_code`, `price`, `discount` | — |
| `sales` | `g_number`, `date`, `last_change_date`, `supplier_article`, `tech_size`, `barcode`, `total_price`, `discount_percent`, `is_supply`, `is_realization`, `promo_code_discount`, `warehouse_name`, `country_name`, `oblast_okrug_name`, `region_name`, `income_id`, `sale_id`, `odid`, `spp`, `for_pay`, `finished_price`, `price_with_disc`, `nm_id`, `subject`, `category`, `brand`, `is_storno` | — |
| `orders` | `g_number`, `date`, `last_change_date`, `supplier_article`, `tech_size`, `barcode`, `total_price`, `discount_percent`, `warehouse_name`, `oblast`, `income_id`, `odid`, `nm_id`, `subject`, `category`, `brand`, `is_cancel`, `cancel_dt` | — |
| `incomes` | `income_id`, `number`, `date`, `last_change_date`, `supplier_article`, `tech_size`, `barcode`, `quantity`, `total_price`, `date_close`, `warehouse_name`, `nm_id`, `status` | — |

Все таблицы: `external_hash CHAR(32) UNIQUE`, `raw_data JSON`, `imported_at TIMESTAMP`.

## Project Structure

| Layer | Location |
|-------|----------|
| API Controller | `app/Http/Controllers/WbDataController.php` |
| Auth Middleware | `app/Http/Middleware/CheckApiKey.php` |
| HTTP Client | `app/Services/Wb/WbApiClient.php` |
| Import Logic | `app/Services/Wb/WbImportService.php` |
| CLI Command | `app/Console/Commands/ImportWbData.php` |
| Models | `app/Models/Stock.php`, `Sale.php`, `Order.php`, `Income.php` |
| Migrations | `database/migrations/` |

## VPS Deploy

```bash
git clone <repo-url> sfera && cd sfera
cp .env.production .env
# заполнить: APP_URL, DB_PASSWORD, WB_API_HOST, WB_API_KEY

# swap (1 ГБ RAM)
sudo fallocate -l 1G /swapfile && sudo chmod 600 /swapfile
sudo mkswap /swapfile && sudo swapon /swapfile
echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab

docker compose -f docker-compose.prod.yml up -d --build
docker compose -f docker-compose.prod.yml exec app php artisan wb:import --entity=all --from=2026-06-14 --to=2026-06-14
```

## Useful Commands

```bash
docker compose exec app bash
docker compose exec app php artisan route:list
docker compose exec app php artisan migrate:status
docker compose down
```

## Postman

Импортируй `postman.json` в Postman. Переменные:
- `base_url` — хост (по умолчанию `http://localhost:8081` для dev)
- `api_key` — токен из `.env` (`WB_API_KEY`)

<details>
<summary>Техническое задание (ТЗ)</summary>

Тестовое API на фреймворке Laravel

Реализована выдача сущностей: Продажи, Заказы, Склады, Доходы.

- Авторизация — параметр `key` в query string
- Формат даты: `Y-m-d`, дата+время: `Y-m-d H:i:s`
- Все эндпоинты — JSON с пагинацией
- Лимит по умолчанию 500, параметр `limit`
- Пагинация: `page`
- Пример: `/api/orders?dateFrom=...&dateTo=...&page=1&limit=500&key=...`

**GET /api/sales** — `dateFrom`, `dateTo`
**GET /api/orders** — `dateFrom`, `dateTo`
**GET /api/stocks** — `dateFrom` (только текущий день)
**GET /api/incomes** — `dateFrom`, `dateTo`

Стек: docker/docker-compose, PHP 8.1 / 8.2, Laravel 8, Laravel Octane

</details>
