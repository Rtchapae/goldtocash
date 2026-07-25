<template>
	<div class="what-sets-apart-page">
		<WhatSetsApartHero />
		<WhatSetsApartPrices />
		<WhatSetsApartTrusted />
		<TestimonialsSection heading="Our Customers Say It Best!" :show-calculator="false" />
		<div class="wsa-trustpilot-band d-none d-md-block">
			<TrustpilotDesktop />
		</div>
		<div class="wsa-trustpilot-mobile d-md-none">
			<MobileTrustpilotReviews />
		</div>
		<WhatSetsApartMission />
		<WhatSetsApartItems />
		<WhatSetsApartSteps />
		<GoldCalculatorFaq variant="what-sets-apart" />
		<WhatSetsApartCta />
		<WhatSetsApartAbout />
	</div>
</template>

<script setup>
import { onMounted } from 'vue'
import WhatSetsApartHero from '@/components/whatSetsApart/WhatSetsApartHero.vue'
import WhatSetsApartPrices from '@/components/whatSetsApart/WhatSetsApartPrices.vue'
import WhatSetsApartTrusted from '@/components/whatSetsApart/WhatSetsApartTrusted.vue'
import TestimonialsSection from '@/components/TestimonialsSection.vue'
import TrustpilotDesktop from '@/components/TrustpilotDesktop.vue'
import MobileTrustpilotReviews from '@/components/MobileTrustpilotReviews.vue'
import WhatSetsApartMission from '@/components/whatSetsApart/WhatSetsApartMission.vue'
import WhatSetsApartItems from '@/components/whatSetsApart/WhatSetsApartItems.vue'
import WhatSetsApartSteps from '@/components/whatSetsApart/WhatSetsApartSteps.vue'
import GoldCalculatorFaq from '@/components/GoldCalculatorFaq.vue'
import WhatSetsApartCta from '@/components/whatSetsApart/WhatSetsApartCta.vue'
import WhatSetsApartAbout from '@/components/whatSetsApart/WhatSetsApartAbout.vue'
import seoService from '@/services/seoService.js'

onMounted(() => {
	seoService.setMeta({
		title: 'What Sets Us Apart - Gold Standard Service | Gold To Cash',
		description:
			'See what sets Gold to Cash apart: free insured shipping, 24-hour offers, professional testing, market-based payouts, and a trusted nationwide process.',
	})
})
</script>

<style scoped>
.wsa-trustpilot-band {
	background: #000;
	padding: 0 0 48px;
}

.wsa-trustpilot-mobile {
	width: 100%;
	background: #fff;
}
</style>
