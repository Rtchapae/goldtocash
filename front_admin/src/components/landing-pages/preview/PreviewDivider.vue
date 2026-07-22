<template>
	<hr class="lp-divider" :class="`lp-divider--${data.style || 'line'}`" />
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
	block: { type: Object, required: true },
})

const data = computed(() => props.block.data || {})
</script>
