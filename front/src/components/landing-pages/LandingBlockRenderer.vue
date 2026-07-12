<template>
	<component :is="blockComponent" :block="block" />
</template>

<script setup>
import { computed } from 'vue'
import LpHeading from './blocks/LpHeading.vue'
import LpText from './blocks/LpText.vue'
import LpImage from './blocks/LpImage.vue'
import LpTextImage from './blocks/LpTextImage.vue'
import LpCta from './blocks/LpCta.vue'
import LpSpacer from './blocks/LpSpacer.vue'
import LpDivider from './blocks/LpDivider.vue'

const props = defineProps({
	block: { type: Object, required: true },
})

const components = {
	heading: LpHeading,
	text: LpText,
	image: LpImage,
	text_image: LpTextImage,
	cta: LpCta,
	spacer: LpSpacer,
	divider: LpDivider,
}

const blockComponent = computed(() => components[props.block.type] || LpText)
</script>
