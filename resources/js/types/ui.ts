export type Appearance = 'light' | 'dark' | 'system';
export type ResolvedAppearance = 'light' | 'dark';

export type AppVariant = 'header' | 'sidebar';

export interface FlashToast {
	type: 'success' | 'info' | 'warning' | 'error';
	message: string;
}
