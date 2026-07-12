<template>
	<div class="lp-cta-wrap" :class="`lp-cta-wrap--${data.align || 'center'}`">
		<a
			:href="data.url || '#'"
			class="lp-cta"
			:class="`lp-cta--${data.style || 'primary'}`"
			@click.prevent
		>
			{{ data.text || 'Button' }}
		</a>
	</div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
	block: { type: Object, required: true },
})

const data = computed(() => props.block.data || {})
</script>
