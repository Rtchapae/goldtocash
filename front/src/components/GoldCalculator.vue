<template>
	<section class="section-calculator">
		<div class="container">
			<h1 v-if="showHeading" class="text-center mb-5">Gold Calculator</h1>
			<ClientOnly>
				<ValueCalculator />
			</ClientOnly>
		</div>
	</section>
</template>

<script setup>
import ValueCalculator from '@/components/ValueCalculator.vue'
import ClientOnly from '@/components/ClientOnly.vue'

defineProps({
	/** Main page shows the h1; Gold Calculator route uses hero h1 — hide to avoid duplicate titles. */
	showHeading: {
		type: Boolean,
		default: true,
	},
})
</script>
