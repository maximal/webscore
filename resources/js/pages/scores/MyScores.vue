<template>
	<AppLayout>

		<Head title="My Scores"/>

		<template #header>
			<h3>My Scores</h3>
		</template>

		<ScoresList :models="models"/>

	</AppLayout>
</template>

<script lang="ts" setup>
import type { Score } from '@/types/Score';
import ScoresList from '@/pages/scores/partials/ScoresList.vue';
import AppLayout from "@/layouts/AppLayout.vue";
import { Head } from '@inertiajs/vue3';

defineProps<{
	models: Score[];
}>();
</script>

<style lang="scss" scoped>

</style>
