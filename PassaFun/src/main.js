import '@/styles/style.css';
import '@/styles/animation.css';
import '@/styles/global.css';
import '@/styles/responsive.css';
import 'primeicons/primeicons.css';

import { createApp } from 'vue';
import { createPinia } from 'pinia';
import { createHead } from '@vueuse/head';
import axios from 'axios';
import App from './App.vue';
import route from './router/routes';
import PrimeVue from 'primevue/config';
import CustomThemePreset from '@/utils/defaultTheme';

axios.defaults.baseURL = import.meta.env.VITE_API_BASE_URL;

const app = createApp(App);
const pinia = createPinia();
const head = createHead();

app.use(route);
app.use(pinia);
app.use(head);
app.use(PrimeVue, {
  theme: {
    preset: CustomThemePreset,
    options: {
      darkModeSelector: false,
    },
  },
});

app.mount('#app');
