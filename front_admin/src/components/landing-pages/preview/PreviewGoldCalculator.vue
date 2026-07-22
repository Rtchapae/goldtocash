<template>
	<div class="lp-gold-calculator" :class="`lp-gold-calculator--${data.layout || 'centered'}`">
		<h3 v-if="data.showHeading" class="lp-gold-calculator__title">Gold Calculator</h3>
		<div class="lp-kit-form__placeholder">
			<i class="fas fa-calculator me-2"></i>
			Gold calculator widget
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
