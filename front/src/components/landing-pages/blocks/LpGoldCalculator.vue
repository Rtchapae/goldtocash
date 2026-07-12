<template>
	<div class="lp-gold-calculator" :class="`lp-gold-calculator--${data.layout || 'centered'}`">
		<GoldCalculator :show-heading="data.showHeading !== false" />
	</div>
</template>

<script setup>
import { computed } from 'vue'
import GoldCalculator from '@/components/GoldCalculator.vue'

const props = defineProps({
	block: { type: Object, required: true },
})

const data = computed(() => props.block.data || {})
</script>
