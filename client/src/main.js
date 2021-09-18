import { createApp } from 'vue'
import App from './App.vue'
import './assets/tailwind.css'
import router from './router'
import Zttp from './services/Zttp'
import routes from './api_routes'

const httpClient = new Zttp({
    routes,
    baseUrl: process.env.VUE_APP_API_BASE_URL,
})

createApp(App).use(router).provide('zttp',).mount('#app')
