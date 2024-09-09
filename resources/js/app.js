import './bootstrap';
import { createApp } from 'vue';
import router from './router';  // Import the router

// Import your Vue components
import CurrencySelector from './components/CurrencySelector.vue';
import ExampleComponent from './components/ExampleComponent.vue';

// Create Vue app instance
const app = createApp({});

// Register components globally
app.component('currency-selector', CurrencySelector);
app.component('example-component', ExampleComponent);

// Use the router in the app instance
app.use(router);

// Mount the app to the #app div in app.blade.php
app.mount('#app');
