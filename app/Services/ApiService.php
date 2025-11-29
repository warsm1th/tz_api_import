<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class ApiService
{
    private Client $client;
    private string $baseUrl ;
    private string $apiKey;

    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->client->verify = false;
        $this->client->timeout = 30;

        $this->baseUrl = env('API_BASE_URL');
        $this->apiKey = env('API_KEY');
    }

    /**
     * Получить данные из API с пагинацией
     *
     * @param string $endpoint
     * @param array $params
     * @return array
     * @throws GuzzleException
     */
    public function getData(string $endpoint, array $params = []): array
    {
        $page = 1;
        $allData = [];
        $params['key'] = $this->apiKey;

        do {
            $params['page'] = $page;
            $response = $this->client->get($this->baseUrl . $endpoint, [
                'query' => $params
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            if (empty($data['data'])) {
                break;
            }

            $allData = array_merge($allData, $data['data']);
            $page++;

            // Пауза между запросами чтобы не перегружать API 
            sleep(1);

        } while (count($data['data'])  === 500);

        return $allData;
    }


    /**
     * Получить данные о продажах
     *
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     * @throws GuzzleException
     */
    public function getSales(string $dateFrom, string $dateTo): array
    {
        $params = [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ];

        return $this->getData('/api/sales', $params);
    }

    /**
     * Получить данные о заказах
     *
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     * @throws GuzzleException
     */
    public function getOrders(string $dateFrom, string $dateTo): array
    {
        $params = [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ];

        return $this->getData('/api/orders', $params);
    }

    /**
     * Получить данные о складах
     *
     * @param string $dateFrom
     * @return array
     * @throws GuzzleException
     */
    public function getStocks(string $dateFrom): array
    {
        $params = [
            'dateFrom' => $dateFrom,
        ];

        return $this->getData('/api/stocks', $params);
    }

    /**
     * Получить данные о доходах
     *
     * @param string $dateFrom
     * @param string $dateTo
     * @return array
     * @throws GuzzleException
     */
    public function getIncomes(string $dateFrom, string $dateTo): array
    {
        $params = [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
        ];

        return $this->getData('/api/incomes', $params);
    }
}
