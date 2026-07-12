<template>
	<div class="lp-text-image" :class="`lp-text-image--image-${data.imagePosition || 'right'}`">
		<div class="lp-text-image__content">
			<h3 v-if="data.title" class="lp-text-image__title">{{ data.title }}</h3>
			<div class="lp-text-image__text" v-html="data.text || ''" />
		</div>
		<div class="lp-text-image__media">
			<img v-if="data.image" :src="data.image" alt="" />
			<div v-else class="lp-image__placeholder">Image</div>
		</div>
	</div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
	block: { type: Object, required: true },
})

const data = computed(() => props.block.data || {})
</script>
