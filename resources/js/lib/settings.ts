import type { Setting } from '@/types/Setting';

export function settingValueOrDefault(
	key: string,
	defaultValue: string | number | boolean,
): string | number | boolean {
	const setting: Setting | undefined = Settings[key];
	return setting !== undefined ? (setting.value as string) : defaultValue;
}

export function getTextSetting(key: string, defaultValue: string = ''): string {
	return settingValueOrDefault(key, defaultValue) as string;
}

export function getStringSetting(key: string, defaultValue: string = ''): string {
	return (settingValueOrDefault(key, defaultValue) as string).replace(/[\r\n]+/g, ' ');
}

export function getFloatSetting(key: string, defaultValue: number = 0.0): number {
	return settingValueOrDefault(key, defaultValue) as number;
}

export function getIntSetting(key: string, defaultValue: number = 0): number {
	return Math.floor(settingValueOrDefault(key, defaultValue) as number);
}

export function getBoolSetting(key: string, defaultValue: boolean = false): boolean {
	return settingValueOrDefault(key, defaultValue) as boolean;
}
