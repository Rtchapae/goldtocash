<template>
	<div class="assurances d-md-none">
		<div class="item" v-for="(item, idx) in items" :key="idx">
			<span>
				<img :src="item.src" :alt="item.alt">
			</span>
			<span>
				{{ item.line1 }}
				<br>{{ item.line2 }}
			</span>
		</div>
	</div>
	</template>

<script setup>
const items = [
	{ src: '/images/tag-icon.svg', alt: 'Best payout', line1: 'BEST PAYOUT', line2: 'GUARANTEED' },
	{ src: '/images/thumb-up.png', alt: 'Satisfaction guarantee', line1: '100% SATISFACTION', line2: 'GUARANTEE' },
	{ src: '/images/trophy.png', alt: 'BBB A+ rated', line1: 'A+ RATED BY BETTER', line2: 'BUSINESS BUREAU' },
	{ src: '/images/box-seam.png', alt: 'Free insured shipping', line1: 'FREE & INSURED FEDEX', line2: 'SHIPPING UP TO $100,000*' }
]
</script>


