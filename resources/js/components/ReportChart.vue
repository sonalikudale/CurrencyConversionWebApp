<template>
    <div>
      <canvas id="reportChart"></canvas>
    </div>
  </template>
  
  <script>
  import { Line } from 'chart.js';
  
  export default {
    props: ['reportData'],
    mounted() {
      const ctx = document.getElementById('reportChart').getContext('2d');
      new Line(ctx, {
        type: 'line',
        data: {
          labels: this.reportData.dates,
          datasets: [{
            label: 'Currency Rates',
            data: this.reportData.rates,
            borderColor: 'rgba(75, 192, 192, 1)',
            fill: false
          }]
        }
      });
    }
  };
  </script>
  