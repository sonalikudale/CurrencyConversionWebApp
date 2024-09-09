<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CurrencyConversion;
use Illuminate\Support\Str;

class CurrencyConversionSeeder extends Seeder
{
    public function run()
    {
        $currencies = ['USD', 'EUR', 'GBP', 'AUD', 'CAD', 'JPY']; // Add more as needed

        foreach ($currencies as $fromCurrency) {
            foreach ($currencies as $toCurrency) {
                if ($fromCurrency !== $toCurrency) { // Avoid self-conversion
                    CurrencyConversion::factory()->create([
                        'from_currency' => $fromCurrency,
                        'to_currency' => $toCurrency,
                    ]);
                }
            }
        }
    }
}
