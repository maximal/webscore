<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';
import type { Score } from '@/types/Score';
import { newMethod } from '@/routes/score';
import { onMounted } from 'vue';
import axios, { type AxiosError, type AxiosResponse } from 'axios';

const props = defineProps<{
	model: Score;
}>();

function reload(): void {
	location.reload();
}

onMounted(() => {
	setInterval(() => {
		axios
			.get(`/api/v1/demo/${props.model.id}`)
			.then((response: AxiosResponse<Score>) => {
				if (response.data.status === 'ready') {
					reload();
				}
			})
			.catch((error: AxiosError) => {
				console.error(error);
			});
	}, 3_000);
});
</script>

<template>
	<AppLayout>
		<div v-if="model.status === 'failed' || model.status === 'invalid'" class="text-center">
			<div class="alert alert-danger mb-0 py-4" role="alert">
				Failed to process the score.
				<Link :href="newMethod().url">Try another file, please</Link>
			</div>
		</div>
		<div v-else-if="model.status !== 'ready'" class="text-center">
			<div class="spinner-border text-info" role="status">
				<span class="visually-hidden">Processing...</span>
			</div>
			<div>Score is being processed... The page will be reloaded when ready.</div>
		</div>
		<div v-else class="text-center">
			<p>Score is ready:</p>
			<div>
				<button type="button" class="btn btn-primary btn-lg" @click.prevent="reload">
					<i class="bi bi-arrow-clockwise" />
					Reload the page to view it
				</button>
			</div>
		</div>
	</AppLayout>
</template>

<style scoped lang="scss"></style>
