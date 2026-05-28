import 'bootstrap/js/src/collapse';
import 'bootstrap/js/src/dropdown';
import { detectImageFormatsDefault } from 'image-formats-support';

export default function initVanillaApp(document: Document): void {
	//
	document.body.classList.remove('no-js');
	//
	detectImageFormatsDefault(document)
}
