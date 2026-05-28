<template>
	<Link href="#"><slot/></Link>
</template>

<script lang="ts" setup>
import { Link } from '@inertiajs/vue3';
defineProps<{
	to: string;
	params?: object;
}>();
</script>
