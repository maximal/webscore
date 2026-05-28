<script setup lang="ts">

</script>

<template>
	<main class="d-flex align-items-center py-4 bg-body-tertiary">
		<section class="w-100 m-auto p-4">
			<slot />
		</section>
	</main>
</template>

<style scoped lang="scss">
main {
	height: 100vh;
}

section {
	max-width: 330px;
}
</style>
