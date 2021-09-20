import { createWebHistory, createRouter } from "vue-router"
import Home from '@/pages/Home.vue'
import AdminResources from '@/pages/admin/Resources.vue'
import CreateResource from '@/pages/admin/CreateResource.vue'
import EditResource from '@/pages/admin/EditResource.vue'

const routes = [
    {
        path: "/",
        name: "home",
        component: Home,
    },
    {
        path: "/admin/resources/create",
        name: "admin.resources.create",
        component: CreateResource,
    },
    {
        path: "/admin/resources",
        name: "admin.resources.index",
        component: AdminResources,
    },
    {
        path: "/admin/resources/:id/edit",
        name: "admin.resources.edit",
        component: EditResource,
    },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router