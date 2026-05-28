<script setup lang="ts">
import NavBar from '@/components/NavBar.vue';
import LayoutFooter from '@/components/LayoutFooter.vue';
</script>

<template>
	<main class="container-fluid d-flex flex-column">
		<NavBar />
		<!-- Content Wrapper -->
		<div class="row flex-grow-1">
			<section class="col-md-8 d-flex flex-column h-100">
				<slot />
			</section>
			<aside class="col-md-4 d-flex flex-column h-100">
				<slot name="aside" />
				<LayoutFooter />
			</aside>
		</div>
	</main>
</template>

<style scoped lang="scss">
main {
	height: 100vh;
}

div {
	flex: 1 1 auto;
	min-height: 0;
	overflow: hidden;
}

aside {
	overflow: auto;
}
</style>
