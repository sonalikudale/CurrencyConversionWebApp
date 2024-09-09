<template>
    <div>
        <h1>Select Currencies</h1>
        <form @submit.prevent="convert">
            <div v-for="currency in availableCurrencies" :key="currency">
                <input type="checkbox" v-model="selectedCurrencies" :value="currency" /> {{ currency }}
            </div>
            <button type="submit">Convert</button>
        </form>

        <table v-if="conversionRates">
            <tr v-for="(rate, currency) in conversionRates" :key="currency">
                <td>{{ currency }}</td>
                <td>{{ rate }}</td>
            </tr>
        </table>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            availableCurrencies: ['EUR', 'GBP', 'AUD', 'CAD', 'JPY'],
            selectedCurrencies: [],
            conversionRates: null,
        };
    },
    methods: {
        convert() {
            axios
                .post('/currencies/convert', {
                    currencies: this.selectedCurrencies,
                })
                .then((response) => {
                    this.conversionRates = response.data.quotes;
                });
        },
    },
};
</script>
