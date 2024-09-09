<?php 
namespace App\Services;

use Illuminate\Support\Facades\Http;

class CurrencyLayerService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.currencylayer.api_key');
    }

    public function getConversionRates(array $currencies)
    {
        $currencyList = implode(',', $currencies);
        $response = Http::get('http://apilayer.net/api/live', [
            'access_key' => $this->apiKey,
            'currencies' => $currencyList,
            'source' => 'USD',
            'format' => 1,
        ]);

        return $response->json();
    }

    public function getHistoricalRates($currency, $date)
    {
        $response = Http::get('http://apilayer.net/api/historical', [
            'access_key' => $this->apiKey,
            'date' => $date,
            'currencies' => $currency,
            'source' => 'USD',
            'format' => 1,
        ]);

        return $response->json();
    }
}
