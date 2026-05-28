import type { InertiaLinkProps } from '@inertiajs/vue3';
import type { ClassValue } from 'clsx';
import { clsx } from 'clsx';
import { twMerge } from 'tailwind-merge';

export function cn(...inputs: ClassValue[]): string {
	return twMerge(clsx(inputs));
}

export function toUrl(href: NonNullable<InertiaLinkProps['href']>): string {
	return typeof href === 'string' ? href : href?.url;
}
