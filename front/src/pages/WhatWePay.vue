<template>
	<div class="what-we-pay-page">
		<WhatWePayHero />
		<WhatWePayFactors />
		<WhatWePayCalculator />
		<WhatWePayOfferSteps />
		<WhatWePayBlog />
		<GoldCalculatorFaq variant="what-we-pay" />
	</div>
</template>

<script setup>
import { onMounted } from 'vue'
import WhatWePayHero from '@/components/whatWePay/WhatWePayHero.vue'
import WhatWePayFactors from '@/components/whatWePay/WhatWePayFactors.vue'
import WhatWePayCalculator from '@/components/whatWePay/WhatWePayCalculator.vue'
import WhatWePayOfferSteps from '@/components/whatWePay/WhatWePayOfferSteps.vue'
import WhatWePayBlog from '@/components/whatWePay/WhatWePayBlog.vue'
import GoldCalculatorFaq from '@/components/GoldCalculatorFaq.vue'
import seoService from '@/services/seoService.js'

onMounted(() => {
	seoService.setMeta({
		title: 'What We Pay for Gold & Silver | Guaranteed Best Pricing | Gold To Cash',
		description:
			'See how Gold to Cash prices gold and silver: current market spot pricing, weight, and purity. Use our free gold calculator and get a fair offer with free insured shipping.',
	})
})
</script>
