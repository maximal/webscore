<template>

	<SideBarLayout>

		<section class="d-flex flex-nowrap gap-1 mb-2 audio">
			<div class="flex-grow-1">
				<audio
					ref="audio"
					controls
					:src="oggFile"
					:data-length="model.duration"
					@timeupdate="onTimeUpdate">
					<source :src="oggFile" type="audio/ogg; codecs=opus" />
					<source :src="mp3File" type="audio/mpeg" />
				</audio>
			</div>

			<div>
				<SpeedWidget :current="currentSpeed" @set="setCurrentSpeed" />
			</div>

			<div class="dropdown" title="Download Audio">
				<button
					class="btn btn-light btn-sm dropdown-toggle text-end"
					type="button"
					data-bs-toggle="dropdown"
					aria-expanded="false">
					<i class="bi bi-download" />
				</button>
				<ul class="dropdown-menu">
					<li>
						<RouteLink
							to="score.download"
							:params="{ id: item.id, format: 'ogg' }"
							class="dropdown-item"
							:download="item.title + '.ogg'">
							OGG
						</RouteLink>
					</li>
					<li>
						<RouteLink
							to="score.download"
							:params="{ id: item.id, format: 'ogg' }"
							class="dropdown-item"
							:download="item.title + '.mp3'">
							MP3
						</RouteLink>
					</li>
				</ul>
			</div>
		</section>


		<section class="pages" ref="pagesContainer">
			<section v-for="page of pages" :key="`page-${page}`" class="page">
				<img
					:src="page.image"
					:alt="`Page ${page.number}`"
					:title="`Page ${page.number}`"
					loading="lazy" />
				<span
					v-for="measure of page.measures"
					:key="measure.id"
					:class="`measure j-measure j-measure-${measure.id}`"
					:data-id="measure.id"
					:style="getMeasurePositionStyleString(measure)"
					:title="`${measure.id}`"
					@click.prevent="onMeasureClick(measure.id)" />
			</section>
		</section>


		<template #aside>
			<DownloadWidget :model="item" :token="token" />

			<form v-if="isAuthor && showEditor" @submit.prevent="onFormSubmit">
				<!-- Edit Form: Use `item` here -->
				<div class="mb-2">
					<input v-model="item.title" type="text" class="form-control" />
				</div>
				<div class="mb-2">
					<AccessInput v-model="item.access" name="access" />
				</div>
				<div class="mb-2">
					<textarea v-model="item.text" class="form-control" :rows="5" />
				</div>

				<div class="mb-2">
					Download
					<div class="mb-1">MuseScore source</div>
					<AccessInput v-model="item.msc_download" name="msc_download" />
					<div class="mb-1">PDF</div>
					<AccessInput v-model="item.pdf_download" name="pdf_download" />
					<div class="mb-1">MusicXML source</div>
					<AccessInput v-model="item.xml_download" name="xml_download" />
					<div class="mb-1">MIDI</div>
					<AccessInput v-model="item.midi_download" name="midi_download" />
				</div>

				<div class="form-check form-switch mb-2">
					<input
						id="comments_shown"
						v-model="item.comments_shown"
						class="form-check-input"
						type="checkbox"
						role="switch" />
					<label class="form-check-label" for="comments_shown">
						Show Comments Section
					</label>
				</div>
				<div class="form-check form-switch mb-2">
					<input
						id="comments_enabled"
						v-model="item.comments_enabled"
						class="form-check-input"
						type="checkbox"
						role="switch"
						:disabled="!item.comments_shown" />
					<label class="form-check-label" for="comments_enabled">
						Enable Comments
					</label>
				</div>
				<div class="mb-3">
					<button type="submit" class="btn btn-primary">Save</button>
				</div>
			</form>
			<template v-else>
				<h1 class="h3">
					{{ item.title }}

					<button
						v-if="isAuthor"
						class="btn btn-primary btn-sm"
						title="Edit score information"
						@click.prevent="showEditor = !showEditor">
						<i class="bi bi-pencil" />
					</button>
				</h1>

				<RatingWidget :rating="item.rate_average" :count="item.rates_count" />

				<div v-if="item.text" class="lead">
					<div class="markdown" v-html="item.text_html" />
					<hr />
				</div>
			</template>

			<p v-if="model.is_valid === false" class="valid mb-2">
				<i class="bi bi-exclamation-triangle text-danger"></i>
				File corrupted or invalid.
				<template v-if="isAuthor">
					Open this score in the latest version of MuseScore and re-save.
				</template>
			</p>
			<p v-if="model.composer" class="composer mb-2">
				<span class="text-body-secondary">Composer: </span>
				{{ model.composer }}
			</p>
			<p v-if="model.poet" class="poet mb-2">
				<span class="text-body-secondary">Lyricist: </span>
				{{ model.poet }}
			</p>
			<p v-if="model.key !== null" class="key mb-2">
				<span class="text-body-secondary">Key: </span>
				<KeyWidget :signature="model.key" />
			</p>
			<p v-if="model.parts !== null" class="parts mb-2">
				<span class="text-body-secondary">Parts: </span>
				{{ model.parts }}
			</p>
			<p v-if="model.pages && model.page_width && model.page_height" class="mb-2">
				<span class="text-body-secondary">Pages: </span>
				{{ model.pages }}
				<small class="text-body-secondary">
					({{ pageSizeText(model.page_width, model.page_height) }})
				</small>
			</p>
			<p v-if="model.duration !== null" class="duration mb-2">
				<span class="text-body-secondary">Duration: </span>
				<DurationWidget :duration="model.duration" />
				<span v-if="model.measures !== null" class="text-body-secondary">
						&middot; measures: {{ model.measures }}
					</span>
			</p>

			<div v-if="model.lyrics" class="lyrics mb-2">
				<details>
					<summary class="text-body-secondary mb-1">Lyrics</summary>
					<div>{{ model.lyrics }}</div>
				</details>
			</div>

			<template v-if="item.comments_shown">
				<hr />
				<CommentsWidget :id="item.id" type="score" :enabled="item.comments_enabled" />
			</template>
		</template>
	</SideBarLayout>
</template>

<script lang="ts" setup>
import { onMounted, ref } from 'vue';
import AccessInput from '@/components/AccessInput.vue';
import CommentsWidget from '@/components/CommentsWidget.vue';
import DownloadWidget from '@/pages/scores/partials/DownloadWidget.vue';
import DurationWidget from '@/components/DurationWidget.vue';
import KeyWidget from '@/components/KeyWidget.vue';
import RatingWidget from '@/components/RatingWidget.vue';
import RouteLink from '@/components/RouteLink.vue';
import SideBarLayout from '@/layouts/SideBarLayout.vue';
import SpeedWidget from '@/pages/scores/partials/SpeedWidget.vue';
import type { MeasurePosition, PositionMeasure } from '@/types/MeasurePosition';
import type { Page } from '@/types/Page';
import type { Score } from '@/types/Score';
import axios from 'axios';
import { pageSizeText } from '@/lib/page-sizes';
import { TimerEvent } from 'concurrently';

const props = defineProps<{
	model: Score;
	token: string | null;
	isAuthor: boolean;
}>();

const pages = ref<Page[]>();
const measuresMap = ref<Record<number, MeasurePosition>>({});
const pageMeasuresMap = ref<Record<number, MeasurePosition[]>>({});
const allPositions = ref<PositionMeasure[]>([]);
const oggFile = ref<string>(`/storage/${props.model.dir}/score.ogg`);
const mp3File = ref<string>(`/storage/${props.model.dir}/score.mp3`);
const item = ref<Score>(props.model);
const showEditor = ref<boolean>(false);
const audio = ref<HTMLAudioElement>();
const currentSpeed = ref<number>(1);
const pagesContainer = ref<HTMLElement>();

onMounted(() => {
	buildMaps();

	//pagesContainer.value?.addEventListener('scroll', () => {
	//	console.log(pagesContainer.value?.scrollTop);
	//});
});

function buildMaps(): void {
	const measuresMapLocal: Record<number, MeasurePosition> = {};
	const pageMeasuresMapLocal: Record<number, MeasurePosition[]> = {};
	const allPositionsLocal: PositionMeasure[] = [];
	for (const measure of props.model.positions ?? []) {
		measuresMapLocal[measure.id] = measure;
		const page = measure.page;
		if (pageMeasuresMapLocal[page]) {
			pageMeasuresMapLocal[page].push(measure);
		} else {
			pageMeasuresMapLocal[page] = [measure];
		}
		for (const position of measure.positions) {
			allPositionsLocal.push({ position: position, id: measure.id });
		}
	}
	allPositionsLocal.sort((a, b) => a.position - b.position);

	measuresMap.value = measuresMapLocal;
	pageMeasuresMap.value = pageMeasuresMapLocal;
	allPositions.value = allPositionsLocal;

	const pagesLocal = [];
	for (let page = 1; page <= (props.model.pages ?? 0); page++) {
		pagesLocal.push({
			number: page,
			image: `/storage/${props.model.dir}/page-${page}.svg`,
			measures: pageMeasuresMap.value[page],
		});
	}
	pages.value = pagesLocal;
}

function onTimeUpdate(event: any): void {
	highlightMeasure(findMeasure(Math.round(event.target.currentTime * 1_000)), true);
}

function onMeasureClick(id: number): void {
	highlightMeasure(id, true);
	if (audio.value) {
		audio.value.currentTime = measuresMap.value[id].positions[0] / 1_000.0;
	}
}

function highlightMeasure(activeId: number, scroll = false): void {
	const measures: NodeListOf<HTMLElement> = document.querySelectorAll('.j-measure');
	measures.forEach((element: HTMLElement) => {
		const id = parseInt(element.getAttribute('data-id') ?? '0', 10);
		if (!id) {
			return;
		}
		if (id === activeId) {
			if (!element.classList.contains('active')) {
				// Смена активного такта
				element.classList.add('active');
				if (scroll) {
					scrollMeasureIntoView(element);
				}
			}
		} else {
			element.classList.remove('active');
		}
	});
}

function isElementInViewport(element: Element): boolean {
	const rect = element.getBoundingClientRect();
	return (
		rect.top >= 0 &&
		rect.left >= 0 &&
		rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
		rect.right <= (window.innerWidth || document.documentElement.clientWidth)
	);
}

function scrollMeasureIntoView(element: HTMLElement): void {
	if (isElementInViewport(element)) {
		return;
	}

	if (!pagesContainer.value) {
		return;
	}

	const container = pagesContainer.value;
	//const elementTop = element.offsetTop;
	const elementHeight = element.offsetHeight;
	//const elementBottom = elementTop + element.offsetHeight;
	//const containerScrollTop = container.scrollTop;
	const containerHeight = container.clientHeight;

	/*
	const scrollPos = elementTop - (containerHeight - elementHeight) / 2;
	if (elementTop < containerScrollTop) {
		container.scrollTop = elementTop - (containerHeight - elementHeight) / 2;
	} else if (elementBottom > containerScrollTop + containerHeight) {
		container.scrollTop = elementBottom - containerHeight;
	}
	/**/

	// Naïve solution: won’t work in complex cases
	// Leave as of now
	element.scrollIntoView({
		behavior: 'smooth',
		block: containerHeight > elementHeight ? 'center' : 'start',
		inline: 'center',
	});
}

function findMeasure(position: number): number {
	let found = 1;
	for (const current of allPositions.value) {
		if (position < current.position) {
			return found;
		}
		found = current.id;
	}
	return found;
}

function getMeasurePositionStyleString(measure: MeasurePosition): string {
	return `top: ${measure.y}%; left: ${measure.x}%; width: ${measure.sx}%; height: ${measure.sy}%`;
}

function getMeasurePosition(measure: MeasurePosition): object {
	return {
		top: `${measure.y}%`,
		left: `${measure.x}%`,
		width: `${measure.sx}%`,
		height: `${measure.sy}%`,
	};
}

function onFormSubmit(): void {
	axios
		.put(`/api/v1/scores/${item.value.id}`, item.value)
		.then((response) => {
			item.value = response.data;
			showEditor.value = false;
		})
		.catch((error) => {
			console.log(error.response);
		})
		.finally(() => {
			//
		});
}

function getMetaDescription(): string {
	const text = ((item.value.text ?? item.value.title) + ' music score')
		.replace(/[\r\n]+/g, ' ')
		.replace(/\s\s+/g, ' ');
	if (text.length > 240) {
		return text.substring(0, 239).replace('"', '&quot;') + '…';
	}
	return text.replace('"', '&quot;');
}

function setCurrentSpeed(speed: number): void {
	if (audio.value) {
		audio.value.playbackRate = speed;
	}
	currentSpeed.value = speed;
}
</script>

<style lang="scss" scoped>
.audio {
	//margin-bottom: 1rem;

	audio {
		height: 1.95rem;
		width: 100%;
		border-radius: var(--bs-border-radius-sm);
		//background-color: transparent;
		overflow: hidden;
	}
}

.dropdown-menu {
	min-width: unset;
}

.pages {
	padding: 1rem 1rem 0 1rem;
	height: 100%;
	overflow-y: scroll;
	// box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.25);
	text-align: center;
}

.page {
	display: inline-block;
	position: relative;
	margin-bottom: 2rem;

	img {
		//height: 100%;
		max-width: 100%;
		//max-height: 600px;

		box-shadow: 0 0 10px rgba(0, 0, 0, 0.25);
	}
}

.measure {
	position: absolute;
	width: 0;
	height: 0;
	cursor: pointer;

	&:hover {
		background-color: rgba(199, 21, 133, 0.05);
	}

	&.active {
		background-color: rgba(199, 21, 133, 0.2);
	}
}
</style>
