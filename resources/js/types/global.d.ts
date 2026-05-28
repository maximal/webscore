import type { Auth } from '@/types/auth';
import type { Setting } from '@/types/Setting';

// Extend ImportMeta interface for Vite...
declare module 'vite/client' {
	interface ImportMetaEnv {
		readonly VITE_APP_NAME: string;
		readonly VITE_APP_ENV: string;
		readonly VITE_APP_VERSION: string;
		readonly VITE_SENTRY_DSN: string;
		// Others
		[key: string]: string | boolean | undefined;
	}

	interface ImportMeta {
		readonly env: ImportMetaEnv;
		readonly glob: <T>(pattern: string) => Record<string, () => Promise<T>>;
	}
}

declare module '@inertiajs/core' {
	export interface InertiaConfig {
		sharedPageProps: {
			name: string;
			auth: Auth;
			sidebarOpen: boolean;
			[key: string]: unknown;
		};
	}
}

declare module 'vue' {
	interface ComponentCustomProperties {
		$inertia: typeof Router;
		$page: Page;
		$headManager: ReturnType<typeof createHeadManager>;
	}
}

declare global {
	const Settings: { [key: string]: Setting };
}
