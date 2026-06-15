<?php

namespace App\Services\Wb;

use App\Models\Sale;
use App\Models\Order;
use App\Models\Income;
use App\Models\Stock;

class WbImportService
{
    private WbApiClient $client;

    public function __construct(WbApiClient $client)
    {
        $this->client = $client;
    }

    public function importSales(?string $from = null, ?string $to = null): array
    {
        $from = $from ?? now()->subDays(7)->format('Y-m-d');
        $to   = $to ?? now()->format('Y-m-d');

        return $this->paginatedImport('sales', Sale::class, [
            'dateFrom' => $from,
            'dateTo'   => $to,
        ], [
            'g_number',
            'date',
            'last_change_date',
            'supplier_article',
            'tech_size',
            'barcode',
            'total_price',
            'discount_percent',
            'is_supply',
            'is_realization',
            'promo_code_discount',
            'warehouse_name',
            'country_name',
            'oblast_okrug_name',
            'region_name',
            'income_id',
            'sale_id',
            'odid',
            'spp',
            'for_pay',
            'finished_price',
            'price_with_disc',
            'nm_id',
            'subject',
            'category',
            'brand',
            'is_storno',
        ]);
    }

    public function importOrders(?string $from = null, ?string $to = null): array
    {
        $from = $from ?? now()->subDays(7)->format('Y-m-d');
        $to   = $to ?? now()->format('Y-m-d');

        return $this->paginatedImport('orders', Order::class, [
            'dateFrom' => $from,
            'dateTo'   => $to,
        ], [
            'g_number',
            'date',
            'last_change_date',
            'supplier_article',
            'tech_size',
            'barcode',
            'total_price',
            'discount_percent',
            'warehouse_name',
            'oblast',
            'income_id',
            'odid',
            'nm_id',
            'subject',
            'category',
            'brand',
            'is_cancel',
            'cancel_dt',
        ]);
    }

    public function importStocks(?string $dateFrom = null): array
    {
        return $this->paginatedImport('stocks', Stock::class, [
            'dateFrom' => $dateFrom ?? now()->format('Y-m-d'),
        ], [
            'date',
            'last_change_date',
            'supplier_article',
            'tech_size',
            'barcode',
            'quantity',
            'is_supply',
            'is_realization',
            'quantity_full',
            'warehouse_name',
            'in_way_to_client',
            'in_way_from_client',
            'nm_id',
            'subject',
            'category',
            'brand',
            'sc_code',
            'price',
            'discount',
        ]);
    }

    public function importIncomes(?string $from = null, ?string $to = null): array
    {
        $from = $from ?? now()->subDays(7)->format('Y-m-d');
        $to   = $to ?? now()->format('Y-m-d');

        return $this->paginatedImport('incomes', Income::class, [
            'dateFrom' => $from,
            'dateTo'   => $to,
        ], [
            'income_id',
            'number',
            'date',
            'last_change_date',
            'supplier_article',
            'tech_size',
            'barcode',
            'quantity',
            'total_price',
            'date_close',
            'warehouse_name',
            'nm_id',
            'status',
        ]);
    }

    public function importAll(?string $from = null, ?string $to = null): array
    {
        return [
            'sales'   => $this->importSales($from, $to),
            'orders'  => $this->importOrders($from, $to),
            'stocks'  => $this->importStocks($from),
            'incomes' => $this->importIncomes($from, $to),
        ];
    }

    private function paginatedImport(string $entity, string $modelClass, array $queryParams, array $fields): array
    {
        $page  = 1;
        $limit = 500;

        $received = 0;
        $created  = 0;
        $skipped  = 0;

        do {
            $params = array_merge($queryParams, [
                'page'  => $page,
                'limit' => $limit,
            ]);

            $response = $this->client->fetch($entity, $params);
            $items    = $this->extractItems($response);

            foreach ($items as $item) {
                $received++;

                $hash = md5(json_encode($item, JSON_UNESCAPED_UNICODE));

                $data = ['external_hash' => $hash, 'raw_data' => $item, 'imported_at' => now()];
                foreach ($fields as $field) {
                    $data[$field] = $item[$field] ?? null;
                }

                $record = $modelClass::firstOrCreate(
                    ['external_hash' => $hash],
                    $data
                );

                if ($record->wasRecentlyCreated) {
                    $created++;
                } else {
                    $skipped++;
                }
            }

            $lastPage = $response['meta']['last_page'] ?? $page;
            $page++;
        } while ($page <= $lastPage);

        return [
            'entity'   => $entity,
            'received' => $received,
            'created'  => $created,
            'skipped'  => $skipped,
        ];
    }

    private function extractItems(array $response): array
    {
        if (isset($response['data']) && is_array($response['data'])) {
            return $response['data'];
        }

        return $response;
    }
}
