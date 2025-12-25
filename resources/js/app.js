import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { Quasar, Notify, Loading, Dialog } from 'quasar';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import '@quasar/extras/roboto-font/roboto-font.css';
import '@quasar/extras/material-icons/material-icons.css';
import '@quasar/extras/fontawesome-v6/fontawesome-v6.css';
import '@quasar/extras/animate/fadeIn.css';
import '@quasar/extras/animate/slideInUp.css';
import 'quasar/dist/quasar.css';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(Quasar, {
                config: {
                    notify: {
                        position: 'top-right',
                    },
                    loading: {
                        delay: 400,
                    },
                    dialog: {
                        seamless: true,
                    },
                },
                plugins: {
                    Notify,
                    Loading,
                    Dialog,
                },
            })
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
