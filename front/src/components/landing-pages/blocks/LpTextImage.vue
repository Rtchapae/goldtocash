<template>
	<div class="lp-text-image" :class="`lp-text-image--image-${data.imagePosition || 'right'}`">
		<div class="lp-text-image__content">
			<h2 v-if="data.title" class="lp-text-image__title">{{ data.title }}</h2>
			<div class="lp-text-image__text" v-html="data.text" />
		</div>
		<div v-if="data.image" class="lp-text-image__media">
			<img :src="data.image" alt="" loading="lazy" />
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
