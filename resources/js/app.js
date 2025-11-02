import './bootstrap';
import { createApp } from 'vue';
import { createRouter, createWebHistory } from 'vue-router'
import App from './App.vue';
import Home from './pages/Home.vue'

const routes = [
    { path: '/', name: 'home', component: Home },
]
  
const router = createRouter({
    history: createWebHistory(),
    routes,
})

createApp(App).use(router).mount('#app > div');