export interface MeasurePosition {
	id: number;
	x: number;
	y: number;
	sx: number;
	sy: number;
	page: number;
	positions: number[];
}

export interface PositionMeasure {
	position: number;
	id: number;
}
