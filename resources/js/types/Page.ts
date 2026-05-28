import type { MeasurePosition } from '@/types/MeasurePosition';

export interface Page {
	number: number;
	image: string;

	measures: MeasurePosition[];
}
