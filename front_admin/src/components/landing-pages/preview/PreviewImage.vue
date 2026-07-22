<template>
	<figure class="lp-image" :class="[`lp-image--${data.width || 'full'}`, `lp-image--${data.align || 'center'}`]">
		<img v-if="data.src" :src="data.src" :alt="data.alt || ''" />
		<div v-else class="lp-image__placeholder">Image</div>
		<figcaption v-if="data.caption">{{ data.caption }}</figcaption>
	</figure>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
	block: { type: Object, required: true },
})

const data = computed(() => props.block.data || {})
</script>
