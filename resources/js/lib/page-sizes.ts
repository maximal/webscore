export function pageSizeText(width: number, height: number): string {
	let predefined = '';
	// https://paper-size.com/c/a-paper-sizes.html
	// https://www.iso.org/standard/36631.html
	// https://papersizes.io/us/legal.html
	if (width >= 419 && width <= 421 && height >= 593 && height <= 595) {
		// ~ 420 × 594 mm
		predefined = 'A2, ';
	} else if (width >= 296 && width <= 298 && height >= 419 && height <= 421) {
		// ~ 297 × 420 mm
		predefined = 'A3, ';
	} else if (width >= 209 && width <= 211 && height >= 296 && height <= 298) {
		// ~ 210 × 297 mm
		predefined = 'A4, ';
	} else if (width >= 147 && width <= 149 && height >= 209 && height <= 211) {
		// ~ 148 × 210 mm
		predefined = 'A5, ';
	} else if (width >= 104 && width <= 106 && height >= 147 && height <= 149) {
		// ~ 105 × 148 mm
		predefined = 'A6, ';
	} else if (width >= 215 && width <= 217 && height >= 355 && height <= 357) {
		// ~ 216 × 356 mm
		predefined = 'Legal, ';
	}
	return `${predefined}${width}×${height} mm`;
}
