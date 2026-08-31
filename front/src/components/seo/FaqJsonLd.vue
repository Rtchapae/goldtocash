<template>
	<!-- FAQPage JSON-LD for SEO; placed next to visible FAQ block -->
	<component :is="'script'" type="application/ld+json" v-html="safeJsonLd" />
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
	/** @type {{ question: string, answer: string }[]} */
	items: {
		type: Array,
		required: true,
	},
})

const safeJsonLd = computed(() => {
	const data = {
		'@context': 'https://schema.org',
		'@type': 'FAQPage',
		mainEntity: props.items.map((row) => ({
			'@type': 'Question',
			name: row.question,
			acceptedAnswer: {
				'@type': 'Answer',
				text: row.answer,
			},
		})),
	}
	return JSON.stringify(data).replace(/</g, '\\u003c')
})
</script>
