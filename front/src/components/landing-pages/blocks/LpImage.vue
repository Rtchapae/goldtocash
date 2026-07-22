<template>
	<figure class="lp-image" :class="[`lp-image--${data.width || 'full'}`, `lp-image--${data.align || 'center'}`]">
		<img v-if="data.src" :src="data.src" :alt="data.alt || ''" loading="lazy" />
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
