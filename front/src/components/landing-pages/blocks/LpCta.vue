<template>
	<div class="lp-cta-wrap" :class="`lp-cta-wrap--${data.align || 'center'}`">
		<a :href="data.url || '#'" class="lp-cta button" :class="[`lp-cta--${data.style || 'primary'}`, data.style === 'primary' ? 'primary' : '']">
			{{ data.text || 'Learn more' }}
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
