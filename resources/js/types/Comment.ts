import type { User } from '@/types/auth';

export interface Comment {
	id: number;
	author_id: number;
	object_type: string;
	object_id: string;
	text: string;
	text_html: string;
	number: number;
	parent_id: string | null;
	root_id: string | null;
	created_at: string;
	updated_at: string;

	// Mutators / Computed properties
	author: User;
	children: Comment[];
	parent: Comment | null;
	root: Comment | null;
}
