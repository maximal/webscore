<template>
	<span
		class="spinner-border spinner-border-sm text-primary"
		role="status"
		aria-hidden="true" />
	{{ label ?? 'Processing…' }}
</template>

<script lang="ts" setup>
defineProps<{
	label?: string;
}>();
</script>
