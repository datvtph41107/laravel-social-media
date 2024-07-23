import "./bootstrap";
import "../css/app.css";

import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { ZiggyVue } from "../../vendor/tightenco/ziggy/dist/vue.m";
import CKEditor from "@ckeditor/ckeditor5-vue";
import { plugin as VueTippy } from "vue-tippy";
import "tippy.js/dist/tippy.css";
import "tippy.js/themes/light.css";

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(
                VueTippy,
                // optional
                {
                    directive: "tippy", // => v-tippy
                    component: "tippy", // => <tippy/>
                    componentSingleton: "tippy-singleton", // => <tippy-singleton/>,
                    defaultProps: {
                        placement: "top-start",
                        allowHTML: true,
                    }, // => Global default options * see all props
                }
            )
            .use(plugin)
            .use(CKEditor)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: "#4B5563",
    },
});
