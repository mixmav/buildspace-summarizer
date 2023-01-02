import './bootstrap';

import { createApp, h } from 'vue';
import { createPinia } from 'pinia'
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { InertiaProgress } from '@inertiajs/progress';

import Base from '@/Pages/Layout/Base.vue';

import { Link } from '@inertiajs/inertia-vue3';
import Toast, { POSITION } from "vue-toastification";
import "vue-toastification/dist/index.css";

InertiaProgress.init({
	color: '#9d4edd',
	delay: 0
});

createInertiaApp({
	resolve: (name) => {
		let page = resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue'));
		page.then((module) => {
			if (module.default.layout === undefined) {
				module.default.layout = [Base];
			}
		});

		return page;
	},
	setup({ el, App, props, plugin }) {
		const pinia = createPinia()

		const app = createApp({ render: () => h(App, props) })
			.use(plugin)
			.use(pinia)
			.use(Toast, {
				position: POSITION.BOTTOM_RIGHT
			})
			.component('Link', Link);
			app.mount(el)
	},
});