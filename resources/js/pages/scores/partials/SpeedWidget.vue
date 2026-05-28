<template>
	<div class="dropdown" title="Playback Speed">
		<button
			class="btn btn-light btn-sm dropdown-toggle text-end"
			type="button"
			data-bs-toggle="dropdown"
			aria-expanded="false">
			×{{ current }}
		</button>
		<ul class="dropdown-menu text-end">
			<li v-for="speed of SPEEDS" :key="speed">
				<button
					class="dropdown-item small"
					:class="{ active: current === speed }"
					@click="setSpeed(speed)">
					{{ speed }}<span class="invisible">{{ postfix(speed) }}</span>
				</button>
			</li>
		</ul>
	</div>
</template>

<script lang="ts" setup>
const SPEEDS = [0.25, 0.5, 0.75, 0.9, 1, 1.1, 1.25, 1.5, 1.75, 2, 2.5, 3];

defineProps<{
	current: number;
}>();

const emit = defineEmits<{
	set: [speed: number];
}>();

function setSpeed(speed: number): void {
	emit('set', speed);
}

function postfix(speed: number): string {
	const parts = speed.toFixed(2).split('.', 2);
	if (parts.length !== 2) {
		return '';
	}
	switch (parts[1]) {
		case '00':
			return '.00';
		case '25':
		case '75':
			return '';
		default:
			return '0';
	}
}
</script>

<style lang="scss" scoped>
.dropdown-menu {
	min-width: unset;
}

button {
	font-variant: tabular-nums;
}
</style>
