<template>
	<div class="lp-block-preview" :class="`lp-block-preview--${block.type}`">
		<component :is="previewComponent" :block="block" />
	</div>
</template>

<script setup>
import { computed } from 'vue'
import PreviewHeading from './preview/PreviewHeading.vue'
import PreviewText from './preview/PreviewText.vue'
import PreviewImage from './preview/PreviewImage.vue'
import PreviewTextImage from './preview/PreviewTextImage.vue'
import PreviewCta from './preview/PreviewCta.vue'
import PreviewSpacer from './preview/PreviewSpacer.vue'
import PreviewDivider from './preview/PreviewDivider.vue'

const props = defineProps({
	block: { type: Object, required: true },
})

const components = {
	heading: PreviewHeading,
	text: PreviewText,
	image: PreviewImage,
	text_image: PreviewTextImage,
	cta: PreviewCta,
	spacer: PreviewSpacer,
	divider: PreviewDivider,
}

const previewComponent = computed(() => components[props.block.type] || PreviewText)
</script>
