import { createWebHistory, createRouter } from "vue-router"
import Home from "../pages/Home"
import About from "../pages/About"

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
]

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router