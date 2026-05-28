<template>
	<section class="d-flex flex-wrap gap-1 mb-2">
		<a
			v-if="buttonShown(model.msc_download)"
			:href="download({ id: model.id, format: 'mscz' }).url"
			class="btn btn-secondary btn-sm"
			:download="model.title + '.mscz'">
			<i class="bi bi-download" />
			MuseScore
		</a>

		<a
			v-if="model.pdf_generated_at && buttonShown(model.pdf_download)"
			:href="download({ id: model.id, format: 'pdf' }).url"
			class="btn btn-secondary btn-sm"
			:download="model.title + '.pdf'">
			<i class="bi bi-download" />
			PDF
		</a>

		<a
			v-if="model.pdf_generated_at && buttonShown(model.xml_download)"
			:href="download({ id: model.id, format: 'xml' }).url"
			class="btn btn-secondary btn-sm"
			:download="model.title + '.xml'">
			<i class="bi bi-download" />
			MusicXML
		</a>

		<a
			v-if="model.pdf_generated_at && buttonShown(model.midi_download)"
			:href="download({ id: model.id, format: 'midi' }).url"
			class="btn btn-secondary btn-sm"
			:download="model.title + '.midi'">
			<i class="bi bi-download" />
			MIDI
		</a>
	</section>
</template>

<script lang="ts" setup>
import type { Score } from '@/types/Score';
import { ScoreAccess } from '@/types/ScoreAccess';
import { download } from '@/routes/score';
import { usePage } from '@inertiajs/vue3';

const props = defineProps<{
	model: Score;
	token: string | null;
}>();

function buttonShown(access: ScoreAccess): boolean {
	const user = usePage().props.auth.user;
	console.log(user);
	//console.log(access);
	switch (access) {
		case 'public':
			return true;
		case 'registered':
			return !!user;
		case 'link':
			return props.model.author_id === user?.id;
		case 'private':
			return false;
	}
}
</script>

<style lang="scss" scoped></style>
