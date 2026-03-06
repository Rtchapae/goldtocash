import { createApp } from 'vue';
import { createPinia } from 'pinia';
import router from './router';
import App from './App.vue';
import VueApexCharts from 'vue3-apexcharts'
import './styles/main.scss'

const app = createApp(App);
app.use(createPinia());
app.use(VueApexCharts)
app.use(router);
app.mount('#app');

