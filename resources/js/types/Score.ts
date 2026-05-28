import type { MeasurePosition } from '@/types/MeasurePosition';
import type { ScoreAccess } from '@/types/ScoreAccess';

export interface Score {
	id: string;
	title: string;
	file: string;
	disk: string;
	author_id: number;
	slug: string;
	status: 'created' | 'processing' | 'ready' | 'invalid' | 'failed';

	//
	access: ScoreAccess;
	msc_download: ScoreAccess;
	pdf_download: ScoreAccess;
	xml_download: ScoreAccess;
	midi_download: ScoreAccess;
	access_token: string | null;
	comments_shown: boolean;
	comments_enabled: boolean;
	text: string | null;
	text_html: string | null;
	thumbnail: string | null;

	// File meta
	subtitle: string | null;
	composer: string | null;
	poet: string | null;
	duration: number | null;
	tempo: number | null;
	tempo_text: string | null;
	time_signature: string | null;
	file_version: number | null;
	has_harmonies: boolean | null;
	lyrics: string | null;
	key: number | null;
	measures: number | null;
	pages: number | null;
	parts: number | null;
	page_width: number | null;
	page_height: number | null;
	page_twosided: boolean | null;
	is_valid: boolean | null;
	musescore_version: string | null;
	meta: object | null;
	positions: MeasurePosition[] | null;

	// Linked aggregates
	comments_count: number;
	rates_count: number;
	rate_average: number | null;

	// Timestamps
	pdf_generated_at: string | null;

	// Mutators / Computed properties
	dir: string;
}
