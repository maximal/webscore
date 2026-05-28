<template>
	<section :data-rating="rating" :data-count="count">
	</section>
</template>

<script setup lang="ts">
const props = defineProps<{
	rating: number | null;
	count: number;
}>();
</script>

<style scoped lang="scss">

</style>
