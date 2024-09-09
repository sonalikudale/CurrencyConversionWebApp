<?php

namespace App\Http\Controllers;

use App\Services\CurrencyLayerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Jobs\GenerateReport;
use App\Models\Report;
use Illuminate\Support\Facades\Log;
use App\Models\CurrencyConversion;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;



class CurrencyController extends Controller
{
    protected $currencyService;

    public function __construct(CurrencyLayerService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    public function index()
    {
        $currencies = ['USD', 'EUR', 'GBP', 'AUD', 'CAD', 'JPY']; // Add more as needed
        return view('currencies.index', compact('currencies'));
    }

    public function convert(Request $request)
    {
        $amount = $request->input('amount');
        $fromCurrencies = $request->input('fromCurrencies');
        $toCurrencies = $request->input('toCurrencies');
        
        // Log the incoming request data
        Log::info('Conversion request received', [
            'amount' => $amount,
            'fromCurrencies' => $fromCurrencies,
            'toCurrencies' => $toCurrencies,
        ]);
        
        // Validate the inputs
        if (empty($fromCurrencies) || count($fromCurrencies) > 5 || empty($toCurrencies) || count($toCurrencies) > 5) {
            return response()->json(['error' => 'Invalid currency selection. Select up to 5 currencies each for From and To.'], 400);
        }
        
        $apiKey = config('services.currencylayer.api_key');
        
        // API request
        $response = Http::get('http://api.currencylayer.com/live', [
            'access_key' => $apiKey,
            'currencies' => implode(',', $toCurrencies),
            'source' => 'USD',  // Using USD as the base currency
        ]);
    
        // Log the API response
        Log::info('Currency Layer API Response:', $response->json());
        
        if ($response->successful()) {
            $conversionRates = $response->json()['quotes'];
    
            foreach ($toCurrencies as $toCurrency) {
                $conversionKey = 'USD' . $toCurrency;
    
                if (isset($conversionRates[$conversionKey])) {
                    $rate = $conversionRates[$conversionKey];
                    $convertedAmount = $amount * $rate;
    
                    // Log the data being inserted
                    Log::info('Inserting conversion data', [
                        'amount' => $amount,
                        'from_currency' => $fromCurrencies[0], // Assuming a single fromCurrency for now
                        'to_currency' => $toCurrency,
                        'rate' => $rate,
                        'converted_amount' => $convertedAmount,
                    ]);
    
                    // Store the conversion data in the database
                    CurrencyConversion::create([
                        'amount' => $amount,
                        'from_currency' => $fromCurrencies[0], // Assuming a single fromCurrency for now
                        'to_currency' => $toCurrency,
                        'rate' => $rate,
                        'converted_amount' => $convertedAmount,
                    ]);
                }
            }
    
            return response()->json($response->json());
        
        } else {
            // Log the error if the request failed
            Log::error('Currency Layer API Error Response:', $response->json());
    
            return response()->json(['error' => 'Failed to fetch conversion rates.'], $response->status());
        }
    }

public function generateReport(Request $request)
{
    // Log the incoming request data
    Log::info('Generate Report Request Data:', [
        'range' => $request->input('range'),
        'interval' => $request->input('interval'),
        'from_currency' => $request->input('from_currency'),
        'to_currency' => $request->input('to_currency'),
    ]);

    
    $range = $request->input('range');  // e.g., 'one_year', 'six_months', 'one_month'
    $interval = $request->input('interval');  // e.g., 'monthly', 'weekly', 'daily'
    $fromCurrency = $request->input('from_currency');
    $toCurrency = $request->input('to_currency');

    // Determine date range based on selection
    $endDate = Carbon::now();
    if ($range === 'one_year') {
        $startDate = Carbon::now()->subYear();
    } elseif ($range === 'six_months') {
        $startDate = Carbon::now()->subMonths(6);
    } elseif ($range === 'one_month') {
        $startDate = Carbon::now()->subMonth();
    } else {
        return response()->json(['error' => 'Invalid range selected.'], 400);
    }

    // Query the currency_conversions table based on the selected range and interval
    $query = CurrencyConversion::whereBetween('created_at', [$startDate, $endDate])
        ->where('from_currency', $fromCurrency)
        ->where('to_currency', $toCurrency);

    // Group data based on the interval
    if ($interval === 'monthly') {
        $reportData = $query->select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as period'),
            DB::raw('AVG(rate) as avg_rate'),
            DB::raw('SUM(converted_amount) as total_converted_amount')
        )->groupBy('period')->get();
    } elseif ($interval === 'weekly') {
        $reportData = $query->select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%u") as period'),  // "%Y-%u" for week number
            DB::raw('AVG(rate) as avg_rate'),
            DB::raw('SUM(converted_amount) as total_converted_amount')
        )->groupBy('period')->get();
    } elseif ($interval === 'daily') {
        $reportData = $query->select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d") as period'),
            DB::raw('AVG(rate) as avg_rate'),
            DB::raw('SUM(converted_amount) as total_converted_amount')
        )->groupBy('period')->get();
    } else {
        return response()->json(['error' => 'Invalid interval selected.'], 400);
    }

    // Log the report data
    Log::info('Generated Report Data:', ['data' => $reportData->toArray()]);

    // Return the report data
    return response()->json(['report' => $reportData]);
}


    


}
