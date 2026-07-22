<template>
	<component :is="`h${level}`" class="lp-heading" :class="[`lp-heading--${data.align || 'left'}`]" :style="headingStyle">
		{{ data.text }}
	</component>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
	block: { type: Object, required: true },
})

const data = computed(() => props.block.data || {})
const level = computed(() => Math.min(3, Math.max(1, Number(data.value.level) || 2)))
const headingStyle = computed(() => (data.value.color ? { color: data.value.color } : {}))
</script>
