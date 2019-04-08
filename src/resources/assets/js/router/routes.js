import Home from '~pages/Home';
import Login from '~pages/Login';
import Module from '~pages/Module';
import NotFound from '~pages/NotFound';
import AppHeader from '~elements/AppHeader';
import AppMenu from '~elements/AppMenu';

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
        path: '/resource/:controller',
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
        path: '/resource/:controller/:action',
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
        path: '/resource/:controller/:object/:action',
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
