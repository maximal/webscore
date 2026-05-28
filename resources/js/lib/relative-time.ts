/**
 * Модуль для обновления тегов со временем на странице.
 *
 * @author MaximAL
 * @copyright © MaximAL 2020
 */

import { DateTime, Settings, type DateTimeFormatOptions } from 'luxon';

/**
 * Интервал обновления тегов со временем
 */
const UPDATE_INTERVAL: number = 10 * 1_000;

/**
 * Селектор по умолчанию для тегов со временем
 */
const DEFAULT_SELECTOR: string = 'time[data-live]';

/**
 * Формат даты и времени.
 * MOMENT — для Moment.js
 * LUXON — для Luxon
 */
const DATETIME_FORMAT = {
	moment: 'LLLL',
	luxon: {
		year: 'numeric',
		month: 'long',
		day: 'numeric',
		hour: 'numeric',
		minute: '2-digit',
		weekday: 'long',
		timeZoneName: 'short',
	},
};

/**
 * Инициализировать дефолтное поведение для тегов со временем.
 * Используется селектор `DEFAULT_SELECTOR`: `<time
 * data-relative="true"></time>`.
 *
 * Время в тегах обновляется раз в `UPDATE_INTERVAL` миллисекунд (по умолчанию:
 * 10 секунд).
 * ```
 * // Например:
 * initDefault();
 * ```
 */
export function initDefault(document: Document): void {
	//moment.locale(navigator.language);
	Settings.defaultLocale = navigator.language || 'en';

	document.querySelectorAll(DEFAULT_SELECTOR).forEach((element: Element) => {
		processElement(element as HTMLElement);
	});

	setInterval(() => {
		document.querySelectorAll(DEFAULT_SELECTOR).forEach((element: Element) => {
			processElement(element as HTMLElement);
		});
	}, UPDATE_INTERVAL);
}

/**
 * Инициализировать дефолтное поведение для заданных тегов со временем.
 * ```
 * // Например:
 * init(document.querySelectorAll('time'));
 * ```
 */
export function init(elements: NodeListOf<HTMLElement>): void {
	//moment.locale(navigator.language);
	Settings.defaultLocale = navigator.language || 'en';

	elements.forEach((element: HTMLElement) => processElement(element));
	setInterval(() => {
		elements.forEach((element: HTMLElement) => processElement(element));
	}, UPDATE_INTERVAL);
}

/**
 * Обработать единожды заданный тег со временем.
 * ```
 * // Например:
 * processElement(document.querySelector('time'));
 * ```
 */
export function processElement(element: HTMLElement): void {
	const value = element.getAttribute('datetime');
	if (!value) {
		return;
	}
	// Luxon
	// Если дата передана не в ISO, пробуем сконвертировать:
	// заменяем пробел на T и подставляем в конец Z (UTC)
	const time = DateTime.fromISO(value.replace(' ', 'T').replace(/(T\d\d:\d\d:\d\d)$/i, '$1Z'));
	const absolute = time.toLocaleString(DATETIME_FORMAT.luxon as DateTimeFormatOptions);
	//const relative =
	//	Math.abs(DateTime.local().diff(time).milliseconds / 1000) < 10
	//		? 'только что'
	//		: (time.toRelative() ?? '');
	const relative = time.toRelative() ?? '';
	if (element.getAttribute('data-relative') === 'true') {
		element.textContent = relative;
		// Предполагаем, что абсолютное уже установлено в title
		if (!(element.hasAttribute('title') || element.hasAttribute('data-bs-original-title'))) {
			element.setAttribute('title', absolute);
		}
	} else {
		// Предполагаем, что абсолютное уже установлено в textContent
		if ((element.textContent ?? '').trim().length === 0) {
			element.textContent = absolute;
		}
		if (element.hasAttribute('data-bs-original-title')) {
			element.setAttribute('data-bs-original-title', relative);
		} else {
			element.setAttribute('title', relative);
		}
	}
}

export function dateTimeToRelative(value: string | Date): string {
	let time;
	if (value instanceof Date) {
		time = DateTime.fromJSDate(value);
	} else {
		// Если дата передана не в ISO, пробуем сконвертировать:
		// заменяем пробел на T и подставляем в конец Z (UTC)
		time = DateTime.fromISO(value.replace(' ', 'T').replace(/(T\d\d:\d\d:\d\d)$/i, '$1Z'));
	}
	//if (Math.abs(DateTime.local().diff(time).milliseconds / 1000) < 10) {
	//	return 'just now';
	//}
	return time.toRelative() || '';
}

export function cleanUtc(value: string): string {
	return value.replace(' ', 'T').replace(/(T\d\d:\d\d:\d\d)(.\d+)?Z$/i, '$1Z');
}

export function getLocaleDate(date: Date | string): string {
	const dt: DateTime =
		date instanceof Date ? DateTime.fromJSDate(date) : DateTime.fromISO(date.replace(' ', 'T'));
	return dt.toLocaleString(DateTime.DATETIME_SHORT);
}
