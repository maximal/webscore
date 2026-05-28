/**
 * Различные настройки клиентского приложения
 */

// App name
export const APP_NAME = import.meta.env.VITE_APP_NAME || 'Application';

// App environment
export const APP_ENV = import.meta.env.VITE_APP_ENV || 'local';

// App version in the form of: <major>.<minor>.<build>+<SHA>
export const APP_VERSION = import.meta.env.VITE_APP_VERSION || '0.1.666+abcdef7';

// Telegram Bot
//export const TELEGRAM_BOT_USERNAME = import.meta.env.VITE_TELEGRAM_BOT_USERNAME;

// Sentry DSN
export const SENTRY_DSN = import.meta.env.VITE_SENTRY_DSN;
