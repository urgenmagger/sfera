<?php

namespace App\Services\Wb;

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
    $response = $this->client->fetch('stocks', [
        'dateFrom' => $dateFrom ?? now()->format('Y-m-d'),
        'page' => 1,
        'limit' => 5,
    ]);

    return [
        'entity' => 'stocks',
        'received' => count($this->extractItems($response)),
        'created' => 0,
        'skipped' => 0,
        'raw_response' => $response,
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
