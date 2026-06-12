<?php

namespace App\Services\Wb;

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
        // TODO: Fetch sales from API, then insert into DB
        return $this->emptyResult('sales');
    }

    public function importOrders(?string $from = null, ?string $to = null): array
    {
        // TODO: Fetch orders from API, then insert into DB
        return $this->emptyResult('orders');
    }

    public function importStocks(?string $dateFrom = null): array
    {
        $page = 1;
        $limit = 500;

        $received = 0;
        $created = 0;
        $skipped = 0;

        do {
            $response = $this->client->fetch('stocks', [
                'dateFrom' => $dateFrom ?? now()->format('Y-m-d'),
                'page' => $page,
                'limit' => $limit,
            ]);

            $items = $this->extractItems($response);

            foreach ($items as $item) {
                $received++;

                $hash = md5(json_encode($item, JSON_UNESCAPED_UNICODE));

                $stock = Stock::firstOrCreate(
                    ['external_hash' => $hash],
                    [
                        'date' => $item['date'] ?? null,
                        'last_change_date' => $item['last_change_date'] ?? null,
                        'supplier_article' => $item['supplier_article'] ?? null,
                        'tech_size' => $item['tech_size'] ?? null,
                        'barcode' => $item['barcode'] ?? null,
                        'quantity' => $item['quantity'] ?? null,
                        'is_supply' => $item['is_supply'] ?? null,
                        'is_realization' => $item['is_realization'] ?? null,
                        'quantity_full' => $item['quantity_full'] ?? null,
                        'warehouse_name' => $item['warehouse_name'] ?? null,
                        'in_way_to_client' => $item['in_way_to_client'] ?? null,
                        'in_way_from_client' => $item['in_way_from_client'] ?? null,
                        'nm_id' => $item['nm_id'] ?? null,
                        'subject' => $item['subject'] ?? null,
                        'category' => $item['category'] ?? null,
                        'brand' => $item['brand'] ?? null,
                        'sc_code' => $item['sc_code'] ?? null,
                        'price' => $item['price'] ?? null,
                        'discount' => $item['discount'] ?? null,
                        'raw_data' => $item,
                        'imported_at' => now(),
                    ]
                );

                if ($stock->wasRecentlyCreated) {
                    $created++;
                } else {
                    $skipped++;
                }
            }

            $lastPage = $response['meta']['last_page'] ?? $page;

            $page++;
        } while ($page <= $lastPage);

        return [
            'entity' => 'stocks',
            'received' => $received,
            'created' => $created,
            'skipped' => $skipped,
        ];
    }
    public function importIncomes(?string $from = null, ?string $to = null): array
    {
        // TODO: Fetch incomes from API, then insert into DB
        return $this->emptyResult('incomes');
    }

    public function importAll(?string $from = null, ?string $to = null): array
    {
        // TODO: Call all import methods sequentially
        return [
            'sales' => $this->importSales($from, $to),
            'orders' => $this->importOrders($from, $to),
            'stocks' => $this->importStocks($from),
            'incomes' => $this->importIncomes($from, $to),
        ];
    }
    private function extractItems(array $response): array
    {
        if (isset($response['data']) && is_array($response['data'])) {
            return $response['data'];
        }

        return $response;
    }

    private function emptyResult(string $entity): array
    {
        return [
            'entity' => $entity,
            'received' => 0,
            'created' => 0,
            'skipped' => 0,
        ];
    }
}
