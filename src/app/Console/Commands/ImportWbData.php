<?php

namespace App\Console\Commands;

use App\Services\Wb\WbImportService;
use Illuminate\Console\Command;

class ImportWbData extends Command
{
    protected $signature = 'wb:import
                            {--entity=all : Entity to import (sales|orders|stocks|incomes|all)}
                            {--from= : Date from (YYYY-MM-DD)}
                            {--to= : Date to (YYYY-MM-DD)}';

    protected $description = 'Import data from Wildberries API';

    private WbImportService $importService;

    private const ENTITIES = ['sales', 'orders', 'stocks', 'incomes', 'all'];

    public function __construct(WbImportService $importService)
    {
        parent::__construct();
        $this->importService = $importService;
    }

    public function handle(): int
    {
        $entity = $this->option('entity');
        $from = $this->option('from');
        $to = $this->option('to');

        if (!in_array($entity, self::ENTITIES)) {
            $this->error("Unknown entity: {$entity}. Allowed: " . implode(', ', self::ENTITIES));

            return 1;
        }

        $this->info("Importing entity: {$entity}");

        $result = match ($entity) {
            'sales' => $this->importService->importSales($from, $to),
            'orders' => $this->importService->importOrders($from, $to),
            'stocks' => $this->importService->importStocks($from),
            'incomes' => $this->importService->importIncomes($from, $to),
            'all' => $this->importService->importAll($from, $to),
        };

        if ($entity === 'all') {
            foreach ($result as $entityResult) {
                $this->printResult($entityResult);
            }
        } else {
            $this->printResult($result);
        }

        return 0;
    }

    private function printResult(array $result): void
    {
        $this->line(sprintf(
            '  %s: received=%d created=%d skipped=%d',
            $result['entity'],
            $result['received'],
            $result['created'],
            $result['skipped']
        ));
        if (isset($result['raw_response'])) {
            $this->line(json_encode($result['raw_response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
    }
}
