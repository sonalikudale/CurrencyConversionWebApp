<template>
  <div class="container">
    <div class="card">
      <div class="card-header">
        <h1 style="font-family: 'Lobster', cursive; text-align: center;">Currency Converter</h1>
        <p v-if="isAuthenticated">You are logged in.</p>
        <p v-else>Please log in to access this page.</p>
      </div>

      <div class="card-body">
        <form @submit.prevent="submit">
          <!-- Convert Amount -->
          <div class="row">
            <div class="col">
              <div class="form-group">
                <label for="convert-amount">Convert Amount</label>
                <input 
                  type="number" 
                  class="form-control" 
                  id="convert-amount" 
                  v-model="amount" 
                  placeholder="Enter amount..." 
                  required
                />
              </div>
            </div>
          </div>

          <!-- Convert From and Convert To Currencies -->
          <div class="row">
            <div class="col-sm-6">
              <div class="form-group">
                <label for="convert-from">Convert from:</label>
                <select 
                  multiple 
                  class="form-control" 
                  id="convert-from" 
                  v-model="selectedFromCurrencies"
                  :disabled="selectedFromCurrencies.length >= 5"
                >
                  <option v-for="currency in availableCurrencies" :key="currency" :value="currency">{{ currency }}</option>
                </select>
                <small class="text-muted">Select up to 5 currencies</small>
              </div>
            </div>

            <div class="col-sm-6">
              <div class="form-group">
                <label for="convert-to">Convert to:</label>
                <select 
                  multiple 
                  class="form-control" 
                  id="convert-to" 
                  v-model="selectedToCurrencies"
                  :disabled="selectedToCurrencies.length >= 5"
                >
                  <option v-for="currency in availableCurrencies" :key="currency" :value="currency">{{ currency }}</option>
                </select>
                <small class="text-muted">Select up to 5 currencies</small>
              </div>
            </div>
          </div>

          <!-- Submit Button -->
          <div class="row">
            <div class="col" style="text-align: center;">
              <button 
                type="submit" 
                class="btn btn-success" 
                :disabled="!isValid"
              >
                Submit
              </button>
            </div>
          </div>
        </form>

        <!-- Conversion Rates Display -->
        <div class="row mt-4" v-if="conversionRates && Object.keys(conversionRates).length > 0">
          <div class="col">
            <h3>Conversion Rates</h3>
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>From Currency</th>
                  <th>From Rate (to USD)</th>
                  <th>To Currency</th>
                  <th>To Rate (to USD)</th>
                  <th>Final Conversion Rate</th>
                  <th>Converted Amount</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(conversion, index) in displayedRates" :key="index">
                  <td>{{ conversion.fromCurrency }}</td>
                  <td>{{ conversion.fromRate }}</td>
                  <td>{{ conversion.toCurrency }}</td>
                  <td>{{ conversion.toRate }}</td>
                  <td>{{ conversion.finalRate }}</td>
                  <td>{{ conversion.convertedAmount }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Message if No Conversion Rates Available -->
        <div v-else>
          <p class="text-center">No conversion rates available. Please select currencies and try again.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'CurrencySelector',  // Add the name here
  computed: {
    isAuthenticated() {
      return window.isAuthenticated;
    }
  },
  data() {
    return {
      amount: null,
      availableCurrencies: ['USD', 'EUR', 'GBP', 'AUD', 'CAD', 'JPY'],
      selectedFromCurrencies: [],
      selectedToCurrencies: [],
      conversionRates: null,
      displayedRates: [] // Holds the conversion rate details for display
    };
  },
  computed: {
    isValid() {
      return this.amount && this.selectedFromCurrencies.length > 0 && this.selectedToCurrencies.length > 0;
    }
  },
  methods: {
    submit() {
      if (this.isValid) {
        console.log('Sending request:', {
          amount: this.amount,
          fromCurrencies: this.selectedFromCurrencies,
          toCurrencies: this.selectedToCurrencies
        }); // Log the request data

        axios.post('/api/currencies/convert', {
          amount: this.amount,
          fromCurrencies: this.selectedFromCurrencies,
          toCurrencies: this.selectedToCurrencies
        })
        .then(response => {
          console.log('Received API response:', response.data); // Log the API response

          if (response.data && response.data.quotes) {
            this.conversionRates = response.data.quotes;
            this.calculateConvertedRates();
          } else {
            this.conversionRates = {};
          }
        })
        .catch(error => {
          console.error('Error fetching conversion rates:', error);
        });
      } else {
        console.warn('Invalid form submission. Please check the inputs.');
      }
    },
    
    calculateConvertedRates() {
      this.displayedRates = [];

      // Iterate over the selected currencies and calculate conversion rates
      this.selectedFromCurrencies.forEach(fromCurrency => {
        this.selectedToCurrencies.forEach(toCurrency => {
          const fromRateKey = `USD${fromCurrency}`;
          const toRateKey = `USD${toCurrency}`;

          // Retrieve rates for both the "from" and "to" currencies
          const fromRate = this.conversionRates[fromRateKey] || 1; // Default to 1 if it's USD
          const toRate = this.conversionRates[toRateKey];

          if (toRate) {
            const finalRate = toRate / fromRate; // Calculate the conversion rate between two currencies
            const convertedAmount = this.amount * finalRate;

            this.displayedRates.push({
              fromCurrency: fromCurrency,
              fromRate: fromRate.toFixed(4), // Show rate to 4 decimal places
              toCurrency: toCurrency,
              toRate: toRate.toFixed(4),
              finalRate: finalRate.toFixed(4),
              convertedAmount: convertedAmount.toFixed(2)
            });
          }
        });
      });
    }
  }
};
</script>

<style scoped>
h1 {
  font-family: 'Lobster', cursive;
  text-align: center;
}

table {
  width: 100%;
  border-collapse: collapse;
}

table, th, td {
  border: 1px solid #ddd;
}

th, td {
  padding: 8px;
  text-align: left;
}

th {
  background-color: #f2f2f2;
}
</style>
