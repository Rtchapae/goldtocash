<template>
	<section class="section-landing-page">
		<div class="container page-content landing-page-content">
			<LandingBlockRenderer v-for="block in page.blocks" :key="block.id" :block="block" />
		</div>
	</section>
</template>

<script setup>
import LandingBlockRenderer from '@/components/landing-pages/LandingBlockRenderer.vue'

defineProps({
	page: { type: Object, required: true },
})
</script>

<style scoped>
.section-landing-page {
	padding: 2rem 0 3rem;
}

@media (max-width: 767px) {
	.section-landing-page {
		padding: 1.25rem 0 2rem;
	}

	.landing-page-content {
		padding-left: 0.75rem;
		padding-right: 0.75rem;
	}
}
</style>
