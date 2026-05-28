import { createInertiaApp } from '@inertiajs/vue3';
import { initializeTheme } from '@/composables/useAppearance';
import initVanillaApp from '@/lib/vanilla';
import { initializeFlashToast } from '@/lib/flashToast';
import { APP_NAME } from '@/config';

const appName = APP_NAME || 'Application';

createInertiaApp({
	title: (title) => (title ? `${title} · ${appName}` : appName),
	progress: {
		color: '#4B5563',
	},
}).then(() => initVanillaApp(document));

// This will set light / dark mode on page load...
initializeTheme();

// This will listen for flash toast data from the server...
initializeFlashToast();
