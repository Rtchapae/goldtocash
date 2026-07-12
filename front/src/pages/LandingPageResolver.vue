<template>
	<div v-if="isLoading" class="section-landing-page">
		<div class="container text-center py-5">
			<div class="spinner-border text-primary" role="status">
				<span class="visually-hidden">Loading...</span>
			</div>
		</div>
	</div>
	<LandingPage v-else-if="page" :page="page" />
	<NotFound v-else />
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { fetchLandingPageByPath } from '@/api/landingPages'
import seoService from '@/services/seoService'
import LandingPage from '@/pages/LandingPage.vue'
import NotFound from '@/pages/NotFound.vue'

const route = useRoute()
const page = ref(null)
const isLoading = ref(true)

const loadPage = async () => {
	isLoading.value = true
	page.value = null

	try {
		const response = await fetchLandingPageByPath(route.path)
		const data = response.data || response
		if (data?.blocks) {
			page.value = data
			applySeo(data)
		}
	} catch {
		page.value = null
	} finally {
		isLoading.value = false
	}
}

const applySeo = (data) => {
	const title = data.seo_title || data.title
	const description = data.seo_description || ''
	seoService.setMeta({ title, description })
	seoService.sendGaPageView(route)
}

onMounted(loadPage)
watch(() => route.path, loadPage)
</script>
