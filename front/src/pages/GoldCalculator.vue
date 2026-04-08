<template>
	<div class="gold-calculator-page">
		<GoldCalculatorScrapIntroSection v-if="isScrapLikeLayout" :variant="scrapLikeVariant" />

		<GoldCalculatorUseBlock v-if="!isScrapLikeLayout" variant="default" />

		<TrustpilotReviews v-if="!isScrapLikeLayout" />

		<GoldCalculatorHowItWorks :faq-variant="faqVariant" />
	</div>
</template>

<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import TrustpilotReviews from '@/components/TrustpilotReviews.vue'
import GoldCalculatorHowItWorks from '@/components/GoldCalculatorHowItWorks.vue'
import GoldCalculatorUseBlock from '@/components/GoldCalculatorUseBlock.vue'
import GoldCalculatorScrapIntroSection from '@/components/GoldCalculatorScrapIntroSection.vue'

const route = useRoute()
const faqVariant = computed(() => route.meta.faqVariant || 'calculator')

/** Scrap/dental routes: inline embed + section stack; generic route uses top calculator block + reviews. */
const isScrapLikeLayout = computed(() => {
	const v = faqVariant.value
	return v === 'scrap' || v === 'dental'
})

const scrapLikeVariant = computed(() => (faqVariant.value === 'dental' ? 'dental' : 'scrap'))
</script>

<style scoped>
.navbar {
	background: var(--black);
	top: 0;
}
</style>