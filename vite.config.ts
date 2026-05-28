import inertia from '@inertiajs/vite';
import { wayfinder } from '@laravel/vite-plugin-wayfinder';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import { defineConfig } from 'vite';

export default defineConfig({
	plugins: [
		laravel({
			input: ['resources/scss/app.scss', 'resources/js/app.ts'],
			refresh: true,
			fonts: [
				bunny('Instrument Sans', {
					weights: [400, 500, 600],
				}),
			],
		}),
		inertia(),
		vue({
			template: {
				transformAssetUrls: {
					base: null,
					includeAbsolute: false,
				},
			},
		}),
		wayfinder({
			formVariants: true,
		}),
	],
});
