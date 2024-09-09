<?php 
namespace App\Jobs;

use App\Services\CurrencyLayerService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FetchHistoricalRates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $currency;
    protected $range;

    public function __construct($currency, $range)
    {
        $this->currency = $currency;
        $this->range = $range;
    }

    public function handle(CurrencyLayerService $currencyService)
    {
        // Fetch and store historical data using $currencyService
    }
}
