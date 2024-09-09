<template>
    <div class="report-container">
      <h1>Currency Conversion Report</h1>
      <p v-if="isAuthenticated">You are logged in.</p>
    <p v-else>Please log in to access this page.</p>
      <!-- Form for selecting range and interval -->
      <div class="report-form">
        <label for="range">Select Range:</label>
        <select v-model="selectedRange" id="range">
          <option value="one_year">One Year</option>
          <option value="six_months">Six Months</option>
          <option value="one_month">One Month</option>
        </select>
  
        <label for="interval">Select Interval:</label>
        <select v-model="selectedInterval" id="interval">
          <option value="monthly">Monthly</option>
          <option value="weekly">Weekly</option>
          <option value="daily">Daily</option>
        </select>
  
        <!-- Select currencies -->
        <label for="fromCurrency">From Currency:</label>
        <select v-model="fromCurrency" id="fromCurrency">
          <option v-for="currency in availableCurrencies" :key="currency" :value="currency">
            {{ currency }}
          </option>
        </select>
  
        <label for="toCurrency">To Currency:</label>
        <select v-model="toCurrency" id="toCurrency">
          <option v-for="currency in availableCurrencies" :key="currency" :value="currency">
            {{ currency }}
          </option>
        </select>
  
        <button @click="generateReport">Generate Report</button>
      </div>
  
      <!-- Report data table -->
      <div v-if="reportData.length > 0" class="report-table">
        <h2>Report Results</h2>
        <table>
          <thead>
            <tr>
              <th>Period</th>
              <th>Average Rate</th>
              <th>Total Converted Amount</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in reportData" :key="index">
              <td>{{ item.period }}</td>
              <td>{{ item.avg_rate }}</td>
              <td>{{ item.total_converted_amount }}</td>
            </tr>
          </tbody>
        </table>
      </div>
  
      <!-- If no data, show this message -->
      <div v-else>
        <p>No report data available. Select range and interval to generate a report.</p>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: 'ReportView',
    computed: {
    isAuthenticated() {
      return window.isAuthenticated;
    }
  },
    data() {
      return {
        selectedRange: 'one_year',  // Default range selection
        selectedInterval: 'monthly',  // Default interval selection
        fromCurrency: 'EUR',  // Default "From" currency
        toCurrency: 'USD',  // Default "To" currency
        reportData: [],  // Report data fetched from the backend
        availableCurrencies: ['USD', 'EUR', 'GBP', 'AUD', 'CAD', 'JPY']  // List of available currencies
      };
    },
    methods: {
      generateReport() {
        axios
          .post("/api/generate-report", {
            range: this.selectedRange,
            interval: this.selectedInterval,
            from_currency: this.fromCurrency,
            to_currency: this.toCurrency,
          })
          .then((response) => {
            this.reportData = response.data.report;
          })
          .catch((error) => {
            console.error("Error generating report:", error.response.data);
          });
      },
    },
  };
  </script>
  
  <style scoped>
  .report-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
  }
  
  .report-form {
    margin-bottom: 20px;
  }
  
  .report-form label {
    margin-right: 10px;
  }
  
  .report-form select,
  .report-form input {
    margin-right: 20px;
    padding: 5px;
  }
  
  button {
    padding: 10px 15px;
    background-color: #007bff;
    color: white;
    border: none;
    cursor: pointer;
  }
  
  button:hover {
    background-color: #0056b3;
  }
  
  .report-table {
    margin-top: 20px;
  }
  
  table {
    width: 100%;
    border-collapse: collapse;
  }
  
  table th,
  table td {
    border: 1px solid #ccc;
    padding: 10px;
    text-align: left;
  }
  
  table th {
    background-color: #f4f4f4;
  }
  </style>
  