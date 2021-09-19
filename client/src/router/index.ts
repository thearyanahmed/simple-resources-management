import { createWebHistory, createRouter } from "vue-router"
import About from "../pages/About.vue"
import Home from '../pages/Home.vue'
import AdminResources from '../pages/admin/Resources.vue'

const routes = [
    {
        path: "/",
        name: "home",
        component: Home,
    },
    {
        path: "/about",
        name: "about",
        component: About,
    },
    {
        path: "/admin/resources",
        name: "admin_resources",
        component: AdminResources,
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router