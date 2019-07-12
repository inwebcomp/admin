import Home from '~pages/Home';
import Login from '~pages/Login';
import Module from '~pages/Module';
import NotFound from '~pages/NotFound';
import AppHeader from '~elements/AppHeader';
import AppMenu from '~elements/AppMenu';

import Vue from 'vue'

Vue.component('app-header', AppHeader)
Vue.component('app-sidebar', AppMenu)

export default [
    {
        name: 'home',
        path: '/',
        components: {
            default: Home,
            header: AppHeader,
            sidebar: AppMenu
        },
        props: true
    },
    {
        name: 'login',
        path: '/login',
        components: {
            default: Login
        }
    },
    {
        name: 'index',
        path: '/resource/:resourceName',
        components: {
            default: Module,
            header: AppHeader,
            sidebar: AppMenu
        },
        props: {
            default: true,
            sidebar: true
        }
    },
    {
        name: 'action',
        path: '/resource/:resourceName/:action',
        components: {
            default: Module,
            header: AppHeader,
            sidebar: AppMenu
        },
        props: {
            default: true,
            sidebar: true
        }
    },
    {
        name: 'module',
        path: '/resource/:resourceName/:resourceId/:action',
        components: {
            default: Module,
            header: AppHeader,
            sidebar: AppMenu
        },
        props: {
            default: true,
            sidebar: true
        }
    },
    {
        name: '404',
        path: "*",
        components: {
            default: NotFound,
            header: AppHeader,
            sidebar: AppMenu
        },
        props: true
    }
]
