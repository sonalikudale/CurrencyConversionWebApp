<?php

namespace Database\Factories;

use App\Models\CurrencyConversion;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CurrencyConversionFactory extends Factory
{
    protected $model = CurrencyConversion::class;

    public function definition()
    {
        return [
            'from_currency' => $this->faker->currencyCode(),
            'to_currency' => $this->faker->currencyCode(),
            'rate' => $this->faker->randomFloat(4, 0.5, 1.5),
            'converted_amount' => $this->faker->randomFloat(2, 100, 1000),
            'amount' => $this->faker->randomFloat(2, 50, 500), // Added amount field
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => Carbon::now(),
        ];
    }
}
