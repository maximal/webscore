<template>
	<span class="duration">{{ getDurationString(duration) }}</span>
</template>

<script setup lang="ts">
defineProps<{
	duration: number;
}>();

const KEY_SIGNATURES = {
	'-7': '__-7 major, -7 minor',
	'-6': '__-6 major, -6 minor',
	'-5': '__-5 major, -5 minor',
	'-4': '__-4 major, -4 minor',
	'-3': '__-3 major, -3 minor',
	'-2': '__-2 major, -2 minor',
	'-1': '__-1 major, -1 minor',
	'0': 'C major, A minor',
	'1': 'F major, D minor',
	'2': '__2 major, 2 minor',
	'3': '__3 major, 3 minor',
	'4': '__4 major, 4 minor',
	'5': '__5 major, 5 minor',
	'6': '__6 major, 6 minor',
	'7': '__7 major, 7 minor',
};

function getDurationString(duration: number): string {
	const seconds = Math.round(duration % 60);
	const minutes = Math.floor(duration / 60) % 60;
	const hours = Math.floor(duration / 60 / 60);
	if (hours > 0) {
		return hours + ':' + (minutes < 10 ? '0' : '') + minutes +
			':' + (seconds < 10 ? '0' : '') + seconds
	}
	return minutes + ':' + (seconds < 10 ? '0' : '') + seconds
}
</script>

<style scoped lang="scss">

</style>
