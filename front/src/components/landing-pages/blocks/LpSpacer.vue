<template>
	<div class="lp-spacer" :class="`lp-spacer--${data.height || 'md'}`" aria-hidden="true" />
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
	block: { type: Object, required: true },
})

const data = computed(() => props.block.data || {})
</script>
