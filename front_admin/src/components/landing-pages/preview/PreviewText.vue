<template>
	<div
		class="lp-text"
		:class="[`lp-text--${data.fontSize || 'md'}`, `lp-text--${data.align || 'left'}`]"
		v-html="data.content || '<p>Text</p>'"
	/>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
	block: { type: Object, required: true },
})

const data = computed(() => props.block.data || {})
</script>
