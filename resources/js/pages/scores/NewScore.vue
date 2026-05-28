<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios, { type  AxiosError, type AxiosProgressEvent, type AxiosResponse } from 'axios';
import type { Score } from '@/types/Score';
import { score } from '@/routes';

const fileInput = ref<HTMLInputElement>();
const drag = ref<boolean>(false);
const error = ref<string>('');
const progress = ref<number | null>(null);

function openInput(): void {
	fileInput.value?.click();
}

interface ErrorsBag {
	message: string;
	errors: {
		file: string[];
	};
}

function sendFile(file: File): void {
	if (progress.value !== null) {
		return;
	}

	error.value = '';

	if (file.size > 10 * 1024 * 1024 || file.size < 1024) {
		error.value = 'The file size must be between 1 KB and 10 MB.';
		return;
	}

	progress.value = 0;
	const form = new FormData();
	form.append('file', file);
	axios
		.post('/api/v1/demo', form, {
			onUploadProgress: (progressEvent: AxiosProgressEvent): void => {
				if (!progressEvent.total) {
					return;
				}
				progress.value = 100.0 * progressEvent.loaded / progressEvent.total;
			}
		})
		.then((response: AxiosResponse<Score>) => {
			const model = response.data;
			error.value = '';
			location.href = score({ id: model.id, slug: model.slug }).url;
		})
		.catch((err: AxiosError<ErrorsBag>) => {
			error.value = err.response ? err.response.data.message : '';
			console.log(error.value);
		}).
		finally(() => {
			progress.value = null;
		});
}

function submit(): void {
	const file = fileInput.value?.files ? fileInput.value.files[0] : null;
	if (!file) {
		return;
	}
	sendFile(file);
}

function onDropZoneDragOver(event: DragEvent): void {
	event.stopPropagation();
	event.preventDefault();
	drag.value = true;
}

function onDropZoneDragLeave(): void {
	drag.value = false;
}

function onDropZoneDrop(event: DragEvent): void {
	const file = event.dataTransfer?.files[0];
	if (!file) {
		return;
	}
	sendFile(file);
}
</script>

<template>
	<AppLayout>
		<Head title="Create New Score" />

		<form @submit.prevent="submit">
			<div
				class="dropzone"
				:class="{ over: drag }"
				@dragover.prevent="onDropZoneDragOver"
				@dragleave="onDropZoneDragLeave"
				@drop.prevent="onDropZoneDrop"
				@click="openInput">
				<label for="file" class="form-label">
					Select MSC or MSCZ file or drop it here
				</label>
				<input
					ref="fileInput"
					class="form-control form-control-lg"
					:class="{ 'is-invalid': error !== '' }"
					id="file"
					type="file"
					:disabled="progress !== null"
					@change="submit" />
				<div v-if="error" class="invalid-feedback">{{ error }}</div>
				<div
					v-if="progress !== null"
					class="progress mt-3" role="progressbar"
					:aria-valuenow="progress"
					:aria-valuemin="0"
					:aria-valuemax="100">
					<div class="progress-bar" :style="`width: ${progress}%`" />
				</div>
			</div>
		</form>
	</AppLayout>
</template>

<style scoped lang="scss">
.dropzone {
	padding: 5rem 1rem;
	box-sizing: border-box;
	border: 4px dashed transparent;
	border-radius: var(--bs-border-radius-xl);
	cursor: pointer;

	&:hover {
		border: 4px dashed var(--bs-focus-ring-color);
	}

	&.over {
		border: 4px dashed var(--bs-focus-ring-color);
	}
}
</style>
