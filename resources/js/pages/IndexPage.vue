<template>
	<AppLayout>
		<Head title="Main page" />

		<ScoresList :models="models" />
	</AppLayout>
</template>

<script setup lang="ts">
import type { Score } from '@/types/Score';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import ScoresList from '@/pages/scores/partials/ScoresList.vue';

defineProps<{
	models: Score[];
	currentPage: number;
	lastPage: number;
	perPage: number;
	total: number;
	canLogin: boolean;
	canRegister: boolean;
}>();
</script>

<style scoped lang="scss"></style>
