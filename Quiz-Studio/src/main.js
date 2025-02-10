import { registerPlugins } from '@/plugins';
import App from './App.vue';
import router from '@/router';
import { createApp } from 'vue';
import axios from 'axios';

const API_URL = import.meta.env.VITE_API_URL;

axios.defaults.baseURL = API_URL;

const app = createApp(App)

registerPlugins(app)
// app.use(router)
app.mount('#app')

window.addEventListener('load', () => {
    const preloader = document.getElementById('preloader');
    if (preloader) {
        preloader.style.display = 'none';
    }
});
