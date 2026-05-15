<template>
	<span
		class="order-url-trunc text-monospace"
		:title="full"
	>{{ full || '—' }}</span>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
	url: {
		type: String,
		default: ''
	}
})

const full = computed(() => (props.url || '').trim())
</script>

<style scoped>
.order-url-trunc {
	display: inline-block;
	max-width: 100%;
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
	vertical-align: middle;
}
</style>
