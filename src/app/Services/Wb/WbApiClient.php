<?php

namespace App\Services\Wb;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class WbApiClient
{
    private string $host;
    private string $key;

    public function __construct()
    {
        $this->host = rtrim((string) config('services.wb_api.host'), '/');
        $this->key = (string) config('services.wb_api.key');
    }

    public function fetch(string $entity, array $params = []): array
    {
        if ($this->host === '') {
            throw new RuntimeException('WB API host is not configured.');
        }

        if ($this->key === '') {
            throw new RuntimeException('WB API key is not configured.');
        }

        $url = $this->host . '/api/' . $entity;

        $response = Http::timeout(30)->get($url, array_merge($params, [
            'key' => $this->key,
        ]));

        if (!$response->successful()) {
            throw new RuntimeException(
                'WB API request failed. Status: ' . $response->status() . '. Body: ' . $response->body()
            );
        }

        $json = $response->json();

        if (!is_array($json)) {
            throw new RuntimeException('WB API response is not valid JSON.');
        }

        return $json;
    }
}
