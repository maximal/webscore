import { createInertiaApp } from '@inertiajs/vue3';
import createServer from '@inertiajs/vue3/server';
import { createSSRApp, type DefineComponent, h } from 'vue';
import { renderToString } from 'vue/server-renderer';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createServer((page) =>
	createInertiaApp({
		page,
		render: renderToString,
		title: (title) => `${title} · ${appName}`,
		resolve: (name): Promise<DefineComponent> => {
			const pages = import.meta.glob('./pages/**/*.vue');
			return pages[`./pages/${name}.vue`]() as Promise<DefineComponent>;
		},
		setup({ App, props, plugin }) {
			return createSSRApp({ render: () => h(App, props) }).use(plugin);
		},
	}),
);
