<template>
	<section class="cms-page">
		<div class="container page-content cms-page__inner">
			<div v-if="isLoading" class="text-center py-5">
				<div class="spinner-border text-primary" role="status">
					<span class="visually-hidden">Loading...</span>
				</div>
			</div>

			<div v-else-if="error" class="alert alert-danger my-5">
				{{ error }}
			</div>

			<template v-else-if="page">
				<header v-if="showPageTitle" class="cms-page__header">
					<h1 class="cms-page__title">{{ page.title }}</h1>
				</header>

				<div class="cms-page__blocks">
					<template v-for="block in page.blocks" :key="block.id">
						<div v-if="block.type === 'heading'" class="cms-block cms-block--heading">
							<component :is="headingTag(block)" class="cms-block__heading">
								{{ block.data?.text }}
							</component>
						</div>

						<div
							v-else-if="block.type === 'richtext'"
							class="cms-block cms-block--richtext"
							v-html="block.data?.html"
						/>

						<figure v-else-if="block.type === 'image' && block.data?.url" class="cms-block cms-block--image">
							<img
								:src="block.data.url"
								:alt="block.data.alt || page.title"
								loading="lazy"
								decoding="async"
							/>
						</figure>

						<div v-else-if="block.type === 'calculator'" class="cms-block cms-block--calculator">
							<ValueCalculator :variant="calculatorVariant(block)" />
						</div>

						<section v-else-if="block.type === 'faq'" class="cms-block cms-block--faq gc-faq">
							<div class="gc-faq__inner">
								<header class="gc-faq__header">
									<h2 class="gc-faq__page-title">
										<span class="gc-faq__page-title-part gc-faq__page-title-part--cream">FAQs</span>
									</h2>
								</header>
								<div class="gc-faq__groups">
									<div class="gc-faq__list">
										<details
											v-for="(item, idx) in (block.data?.items || [])"
											:key="idx"
											class="gc-faq__item"
										>
											<summary class="gc-faq__question">
												<span class="gc-faq__question-text">{{ item.question }}</span>
												<span class="gc-faq__chev" aria-hidden="true" />
											</summary>
											<div class="gc-faq__answer">
												<p>{{ item.answer }}</p>
											</div>
										</details>
									</div>
								</div>
							</div>
							<div class="seo-faq-schema" aria-hidden="true">
								<FaqJsonLd :items="faqItems(block)" />
							</div>
						</section>
					</template>
				</div>
			</template>
		</div>
	</section>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { fetchBuilderPage } from '@/api/builderPages'
import seoService from '@/services/seoService'
import ValueCalculator from '@/components/ValueCalculator.vue'
import FaqJsonLd from '@/components/seo/FaqJsonLd.vue'

const route = useRoute()
const router = useRouter()

const page = ref(null)
const isLoading = ref(false)
const error = ref(null)

const showPageTitle = computed(() => {
	const blocks = page.value?.blocks || []
	const first = blocks[0]
	if (first?.type === 'heading' && Number(first.data?.level) === 1) {
		return false
	}
	return true
})

const headingTag = (block) => {
	const level = Math.min(3, Math.max(1, Number(block.data?.level) || 2))
	return `h${level}`
}

const calculatorVariant = (block) => (block.data?.variant === 'tabbed' ? 'tabbed' : 'default')

const faqItems = (block) =>
	(block.data?.items || [])
		.filter((i) => i?.question && i?.answer)
		.map((i) => ({ question: i.question, answer: i.answer }))

const load = async () => {
	const slug = route.params.slug
	if (!slug) return

	isLoading.value = true
	error.value = null
	page.value = null

	try {
		const res = await fetchBuilderPage(slug)
		const data = res.data || res
		page.value = {
			...data,
			blocks: Array.isArray(data.blocks) ? data.blocks : [],
		}
		seoService.setMeta({
			title: data.seo_title || data.title,
			description: data.seo_description || '',
		})
	} catch (e) {
		if (e?.status === 404) {
			router.replace({ name: 'not-found', params: { pathMatch: route.path.slice(1).split('/') } })
			return
		}
		error.value = e?.message || 'Failed to load page'
	} finally {
		isLoading.value = false
	}
}

onMounted(load)
watch(() => route.params.slug, load)
</script>

<style scoped>
.cms-page {
	padding: 2.5rem 0 4rem;
}

.cms-page__title {
	font-size: clamp(1.75rem, 3vw, 2.5rem);
	font-weight: 700;
	margin-bottom: 1.5rem;
}

.cms-block {
	margin-bottom: 1.75rem;
}

.cms-block__heading {
	font-weight: 700;
	margin: 0 0 0.75rem;
}

.cms-block--richtext :deep(p) {
	margin-bottom: 1rem;
	line-height: 1.65;
}

.cms-block--image {
	margin: 1.5rem 0;
}

.cms-block--image img {
	display: block;
	max-width: 100%;
	height: auto;
	border-radius: 4px;
}

.cms-block--calculator {
	margin: 2rem 0;
}

.cms-block--faq {
	margin: 2.5rem 0 0;
}
</style>
