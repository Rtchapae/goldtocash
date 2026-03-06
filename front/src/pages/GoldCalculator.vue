<template>
	<div class="container section-calculator-page">
		<h1 class="mb-5">Gold Calculator</h1>

		<ClientOnly>
			<ValueCalculator />
		</ClientOnly>

		<TrustpilotReviews />

		<GoldCalculatorContentIntro :price-per-t-o-z="pricePerTOz" />

		<ClientOnly>
			<ValueCalculator />
		</ClientOnly>

		<GoldCalculatorContentOutro />
	</div>
</template>

<script setup>
import TrustpilotReviews from '@/components/TrustpilotReviews.vue'
import ValueCalculator from '@/components/ValueCalculator.vue'
import ClientOnly from '@/components/ClientOnly.vue'
import GoldCalculatorContentIntro from '@/components/GoldCalculatorContentIntro.vue'
import GoldCalculatorContentOutro from '@/components/GoldCalculatorContentOutro.vue'
import { getCurrentGoldPrice } from '@/api/calculator'
import { ref, onMounted } from 'vue'

const pricePerTOz = ref(0)

onMounted(async () => {
	try {
		const priceData = await getCurrentGoldPrice()
		pricePerTOz.value = priceData.numeric
	} catch (error) {
		console.error('Failed to load gold price for page:', error)
	}
})
</script>

<style scoped>
.navbar {
	background: var(--black);
	top: 0;
}
</style>