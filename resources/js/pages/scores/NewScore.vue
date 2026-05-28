<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios, { type  AxiosError, type AxiosResponse } from 'axios';
import type { Score } from '@/types/Score';
import { score } from '@/routes';

const dropZone = ref<HTMLElement>();
const fileInput = ref<HTMLInputElement>();
const drag = ref<boolean>(false);
const error = ref<string>('');

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
	error.value = '';

	if (file.size > 10 * 1024 * 1024 || file.size < 1024) {
		error.value = 'The file size must be between 1 KB and 10 MB.';
		return;
	}

	const form = new FormData();
	form.append('file', file);
	axios
		.post('/api/v1/demo', form)
		.then((response: AxiosResponse<Score>) => {
			const model = response.data;
			error.value = '';
			location.href = score({ id: model.id, slug: model.slug }).url;
		})
		.catch((err: AxiosError<ErrorsBag>) => {
			error.value = err.response ? err.response.data.message : '';
			console.log(error.value);
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
	if (event.dataTransfer?.files) {
		sendFile(event.dataTransfer?.files[0])
	}
}
</script>

<template>
	<AppLayout>
		<Head title="Create New Score" />

		<form @submit.prevent="submit">
			<div
				ref="dropZone"
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
					@change="submit" />
				<div v-if="error" class="invalid-feedback">{{ error }}</div>
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

	input {
	}
}
</style>
