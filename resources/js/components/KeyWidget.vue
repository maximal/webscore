<template>
	<span class="key">{{ keyBySignature(signature) }}{{ signNumber(signature) }}</span>
</template>

<script setup lang="ts">
defineProps<{
	signature: number;
}>();


const KEY_SIGNATURES = {
	'-7': 'C♭ major / A♭ minor',
	'-6': 'G♭ major / E♭ minor',
	'-5': 'D♭ major / B♭ minor',
	'-4': 'A♭ major / F minor',
	'-3': 'E♭ major / C minor',
	'-2': 'B♭ major / G minor',
	'-1': 'F major / D minor',
	'0': 'C major / A minor',
	'1': 'G major / E minor',
	'2': 'D major / B minor',
	'3': 'A major / F♯ minor',
	'4': 'E major / C♯ minor',
	'5': 'B major / G♯ minor',
	'6': 'F♯ major / D♯ minor',
	'7': 'C♯ major / A♯ minor',
};


function keyBySignature(signature: number): string {
	const key = signature.toString();
	if (KEY_SIGNATURES[key]) {
		return KEY_SIGNATURES[key];
	}
	return key;
}

function signNumber(signature: number): string {
	if (signature === 0) {
		return '';
	}
	if (signature < 0) {
		return ' · ' + (-signature) + '♭';
	}
	return ' · ' + signature + '♯';
}
</script>

<style scoped lang="scss">

</style>
