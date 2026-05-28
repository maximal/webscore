<script setup lang="ts">
import { APP_VERSION } from '@/config';
import { ref } from 'vue';

const shortVersion = ref<string>(APP_VERSION.replace(/\+.+$/, ''));
</script>

<template>
	<!-- Page Footer -->
	<footer class="footer mt-auto py-3 border-top text-center">
		<slot v-if="$slots.footer" name="footer" />
		<div v-else class="text-body-secondary small">
			Works on
			<a href="https://github.com/maximal/webscore" target="_blank">WebScore</a>
			engine v{{ shortVersion }}
			&middot;
			©&nbsp;<a href="https://maximals.ru" target="_blank">MaximAL</a>
			of&nbsp;<a href="https://sijeko.ru" target="_blank" class="sijeko">Sij<em>e</em>ko</a>
			2024—2026
		</div>
	</footer>
</template>

<style scoped lang="scss">
$sijekoColor: #ff7000;

footer {
	.text-body-secondary {
		opacity: 0.2;
		transition: opacity 0.4s linear;
	}

	&:hover {
		.text-body-secondary {
			opacity: 1;
		}
	}

	.sijeko {
		transition: color 1s linear;

		em {
			color: $sijekoColor;
			font-style: normal;
			transition: color 1s linear;
		}

		&:hover {
			color: $sijekoColor;

			em {
				color: var(--bs-link-color);
			}
		}
	}
}
</style>
