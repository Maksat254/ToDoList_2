import axios from 'axios';

import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

const token = localStorage.getItem('token');
if (token) {
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}


import { createRouter, createWebHistory } from "vue-router";
import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap/dist/js/bootstrap.bundle.js';
import AddTask from '@/Pages/Task/AddTask.vue';
import { createApp } from "vue";
import App from './App.vue';




const routes = [
    {
        path: "/",
        component: () => import("./Components/TaskList.vue"),
    },
    {
        path: "/admin/add-task",
        component: () => import("./Pages/Admin/Admin.vue")
    },
    {
        path: "/admin",
        component: ()=>import("./Pages/Admin/Admin.vue")
    },
    {
        path: '/admin/tasks',
        component: () => import('./Pages/Dashboard.vue'),
    }
    ]



export default createRouter({
    history: createWebHistory(),
    routes,
});
const app = createApp(App);
app.use(router);
app.mount('#app');
