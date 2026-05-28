<template>
	<section class="row">
		<div v-for="(model, index) of scores" :key="model.id" class="col-xxl-2 col-md-3 col-sm-4">
			<div class="card border-light-subtle mb-4">
				<!--
				<div class="card-header"></div>
				-->
				<div class="image-wrap">
					<picture v-if="model.thumbnail">
						<source
							:srcset="'/storage/' + model.dir + '/thumbnail.avif'"
							type="image/avif" />
						<img :src="model.thumbnail" :alt="model.title" />
					</picture>
				</div>
				<div class="card-body">
					<h3 class="card-title h5">
						<Link
							v-if="model.status === 'ready'"
							:href="scoreRoute({ id: model.id, slug: model.slug }).url"
							class="stretched-link">
							{{ model.title }}
						</Link>
						<span v-else class="text-muted">{{ model.title }}</span>
					</h3>
					<p class="card-text"></p>
				</div>
				<div class="card-footer border-light-subtle">
					<template v-if="model.status === 'ready'">
						<template v-if="model.parts">
							{{ model.parts }} {{ model.parts > 1 ? 'parts' : 'part' }}
							&middot;
						</template>
						<template v-if="model.pages">
							{{ model.pages }} {{ model.pages > 1 ? 'pages' : 'page' }}
							&middot;
						</template>
						<DurationWidget :duration="model.duration || 0" />
					</template>
					<template v-else-if="model.status === 'failed'">
						<span class="text-danger">
							<i class="bi bi-exclamation-triangle"></i>
							Failed
						</span>
						<button
							class="btn btn-danger btn-sm"
							@click.prevent="deleteScore(model, index)">
							<i class="bi bi-trash"></i>
						</button>
					</template>
					<SpinnerWidget v-else-if="model.status === 'created'" label="Waiting…" />
					<SpinnerWidget v-else label="Processing…" />
				</div>
			</div>
		</div>
	</section>
</template>

<script lang="ts" setup>
import DurationWidget from '@/components/DurationWidget.vue';
import type { Score } from '@/types/Score';
import axios from 'axios';
import { ref } from 'vue';
import SpinnerWidget from '@/components/SpinnerWidget.vue';
import { Link } from '@inertiajs/vue3';
import { score as scoreRoute} from '@/routes';

const props = defineProps<{
	models: Score[];
}>();

const scores = ref<Score[]>(props.models);

function deleteScore(score: Score, index: number): void {
	if (!confirm(`Confirm deleting score: ${score.title}`)) {
		return;
	}
	axios
		.delete(`/api/v1/scores/${score.id}`)
		.then(() => {
			scores.value.splice(index, 1);
		})
		.catch((error) => {
			alert(`Error deleting score: ${error.response.data.message}`);
		});
}
</script>

<style lang="scss" scoped>
.image-wrap {
	text-align: center;

	img {
		max-width: 100%;
		max-height: 256px;
	}
}
</style>
