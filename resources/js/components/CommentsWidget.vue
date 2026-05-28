<template>
	<section id="comments">
		<h2 v-if="loaded" class="h4">{{ pluralizeComments() }}</h2>
		<h2 v-else class="h4">Comments</h2>

		<template v-if="!loading">
			<article
				v-for="(item, index) of items"
				:id="`comment-${item.number}`"
				:key="item.id"
				:data-index="index"
				class="mb-3">
				<div class="author">
					<strong>{{ item.author.name }}</strong>
					<span class="comment-misc small">
						&middot;

						<!-- TODO: Подсветка комментариев
						<a :href="`#comment-${item.number}`">
							<time :datetime="item.created_at"
								  :title="getLocaleDate(item.created_at)">
								{{ dateTimeToRelative(item.created_at) }}
							</time>
						</a>
						-->

						<a :href="`#comment-${item.number}`" class="comment-link">
							<time
								:datetime="item.created_at"
								:title="getLocaleDate(item.created_at)"
								data-live
								data-relative="true">
								{{ dateTimeToRelative(item.created_at) }}
							</time>
						</a>
						<template v-if="item.author_id === $page.props.auth.user?.id">
							&middot;
							<button
								class="btn btn-outline-light btn-sm"
								title="Delete comment"
								@click="deleteModel(item, index)">
								<i class="bi bi-trash text-danger" />
							</button>
						</template>
					</span>
				</div>
				<div class="markdown" v-html="item.text_html" />
			</article>
			<p v-if="items.length === 0">No comments yet.</p>

			<template v-if="$page.props.auth.user && enabled">
				<form @submit.prevent="createModel" class="mb-3">
					<div class="mb-1">
						<textarea
							ref="textarea"
							v-model="model.text"
							class="form-control"
							name="text"
							:class="errors.text ? 'is-invalid' : ''"
							placeholder="Comment text..."
							:maxlength="500"
							@input="onInput"
							@keydown="onKeyDown" />
					</div>
					<PrimaryButton :disabled="model.text.trim() === '' || submitting">
						Submit
					</PrimaryButton>
					<div v-if="errors.message" class="form-text text-danger">
						{{ errors.message }}
					</div>
				</form>
			</template>
		</template>
		<template v-else>
			<div class="spinner-border text-primary" role="status">
				<span class="visually-hidden">Loading...</span>
			</div>
		</template>
	</section>
</template>

<script setup lang="ts">
import PrimaryButton from './PrimaryButton.vue';
import { dateTimeToRelative, getLocaleDate } from '@/lib/relative-time';
import type { Comment } from '@/types/Comment';
import autosize from 'autosize';
import axios, { type AxiosError } from 'axios';
import { onMounted, ref } from 'vue';

const props = defineProps<{
	models?: Comment[];
	type: string;
	id: string;
	enabled: boolean;
}>();

interface FormErrors {
	message?: string;
	text?: string[];
}

const items = ref<Comment[]>(props.models || []);
const loading = ref(true);
const loaded = ref(false);
const submitting = ref(false);
const model = ref<Comment>({
	object_type: props.type,
	object_id: props.id,
	text: '',
} as Comment);
const textarea = ref<HTMLTextAreaElement>();
const errors = ref<FormErrors>({});

onMounted(() => {
	if (!props.models) {
		loadModels();
	} else {
		loading.value = false;
		loaded.value = true;
	}
	if (textarea.value) {
		autosize(textarea.value as Element);
	}
});

function loadModels(): void {
	loading.value = true;
	loaded.value = false;
	axios
		.get(`/api/v1/comments?type=${props.type}&id=${props.id}`)
		.then((response) => {
			items.value = response.data;
			loaded.value = true;
		})
		.finally(() => {
			loading.value = false;
		});
}

function createModel(): void {
	submitting.value = true;
	axios
		.post('/api/v1/comments', model.value as object)
		.then((response) => {
			items.value.push(response.data);
			model.value.text = '';
			errors.value = {};
		})
		.catch((error) => {
			errors.value = errorToFormErrors(error);
		})
		.finally(() => {
			submitting.value = false;
		});
}

function deleteModel(model: Comment, index: number): void {
	if (!confirm('Confirm deleting comment')) {
		return;
	}
	submitting.value = true;
	axios
		.delete(`/api/v1/comments/${model.id}`)
		.then(() => {
			items.value.splice(index, 1);
		})
		.catch((error) => {
			console.error(error.response.data.message);
		})
		.finally(() => {
			submitting.value = false;
		});
}

function pluralizeComments(): string {
	switch (items.value.length) {
		case 0:
			return `Comments`;
		case 1:
			return `One comment`;
		default:
			return `${items.value.length} comments`;
	}
}

function onInput(): void {
	//emit('update:modelValue', model.value.trim().length > 1 ? model.value : null);
}

function onKeyDown(event: KeyboardEvent): void {
	if ((event.ctrlKey || event.metaKey) && event.key === 'Enter') {
		// Ctrl+Enter / Cmd+Enter
		//emit('submit', event);
	}
}

function errorToFormErrors(error: AxiosError): FormErrors {
	const data = (error.response?.data ?? {}) as any;
	return {
		message: data.message ?? '',
		text: data.errors?.text,
	};
}
</script>

<style lang="scss" scoped>
article {
	.comment-misc {
		opacity: 0.25;
	}
	&:hover {
		.comment-misc {
			opacity: 1;
		}
	}
}
</style>

<style lang="scss">
.comment-link {
	text-decoration: none;
	color: var(--bs-secondary-color);

	&:hover {
		text-decoration: underline;
	}
}
.markdown {
	font-size: 87.5%;
	line-height: 1.2;

	p {
		margin-bottom: 0.5rem;
	}
}
</style>
