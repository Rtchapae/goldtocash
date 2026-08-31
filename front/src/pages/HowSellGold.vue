<template>
	<div class="how-sell-gold-page">
		<HowSellGoldHero />
		<HowSellGoldItemsSection />
		<HowSellGoldTrustSection />
		<GoldCalculatorFaq variant="how-sell" />
		<GoldCalculatorSellCtaSection page-variant="how-sell" />
	</div>
</template>

<script setup>
import { onMounted } from 'vue'
import HowSellGoldHero from '@/components/howSellGold/HowSellGoldHero.vue'
import HowSellGoldItemsSection from '@/components/howSellGold/HowSellGoldItemsSection.vue'
import HowSellGoldTrustSection from '@/components/howSellGold/HowSellGoldTrustSection.vue'
import GoldCalculatorFaq from '@/components/GoldCalculatorFaq.vue'
import GoldCalculatorSellCtaSection from '@/components/GoldCalculatorSellCtaSection.vue'
import seoService from '@/services/seoService.js'

onMounted(() => {
	seoService.setMeta({
		title: 'How To Sell Gold - 3 Easy Steps | Gold To Cash',
		description:
			'Learn how to sell gold jewelry for cash in 3 easy steps. Request a free appraisal kit, get a professional evaluation, and get paid fast with Gold To Cash.',
	})
})
</script>
