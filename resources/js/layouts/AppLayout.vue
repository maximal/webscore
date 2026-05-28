<script setup lang="ts">
import type { BreadcrumbItem } from '@/types';
import NavBar from '@/components/NavBar.vue';
import LayoutFooter from '@/components/LayoutFooter.vue';

defineProps<{
	breadcrumbs?: BreadcrumbItem[];
}>();
</script>

<template>
	<NavBar />
	<main class="container-fluid py-3">
		<slot />
	</main>
	<LayoutFooter />
</template>
