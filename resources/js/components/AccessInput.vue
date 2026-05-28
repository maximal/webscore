<template>
	<div class="btn-group btn-group-sm" role="group">
		<input
			:id="`${name}-public`"
			v-model="model"
			class="btn-check"
			type="radio"
			:name="name"
			value="public"
			@change="onInput" />
		<label class="btn btn-outline-primary" :for="`${name}-public`" title="Public Access">
			Public
		</label>

		<!--
		<input
			v-model="model"
			class="btn-check"
			type="radio"
			:name="name"
			:id="`${name}-link`"
			value="link"
			:disabled="true"
			@change="onInput" />
		<label
			class="btn btn-outline-primary"
			:for="`${name}-link`"
			title="Access By Link"
			style="opacity: 0.5">
			By Link
		</label>
		-->

		<input
			:id="`${name}-registered`"
			v-model="model"
			class="btn-check"
			type="radio"
			:name="name"
			value="registered"
			@change="onInput" />
		<label
			class="btn btn-outline-primary"
			:for="`${name}-registered`"
			title="For Registered Users">
			Registered Users
		</label>

		<input
			:id="`${name}-private`"
			v-model="model"
			class="btn-check"
			type="radio"
			:name="name"
			value="private"
			@change="onInput" />
		<label class="btn btn-outline-primary" :for="`${name}-private`" title="Private Access Only">
			Private
		</label>
	</div>
</template>

<script lang="ts" setup>
import type { ScoreAccess } from '@/types/ScoreAccess';
import { onMounted, ref } from 'vue';

const props = defineProps<{
	name: string;
	modelValue: ScoreAccess;
}>();

const model = ref<ScoreAccess>(props.modelValue);

const emit = defineEmits<{
	'update:modelValue': [value: ScoreAccess];
}>();

onMounted(() => {
	//
});

function onInput(): void {
	emit('update:modelValue', model.value);
}
</script>

<style lang="scss" scoped></style>
