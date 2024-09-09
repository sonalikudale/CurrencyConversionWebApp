import { createRouter, createWebHistory } from 'vue-router';
import CurrencySelector from './components/CurrencySelector.vue';
import ReportView from './components/ReportView.vue';

const isAuthenticated = window.isAuthenticated; // This will be either true or false

// Define routes
const routes = [
  {
    path: '/',
    name: 'CurrencySelector',
    component: CurrencySelector,
    meta: { requiresAuth: true }
  },
  {
    path: '/report',
    name: 'ReportView',
    component: ReportView,
    meta: { requiresAuth: true }
  },
  // Add additional routes here
];

// Create the router instance
const router = createRouter({
  history: createWebHistory(),
  routes
});

// Navigation guard
router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/login'); // Redirect to login if not authenticated
  } else {
    next();
  }
});

export default router;
