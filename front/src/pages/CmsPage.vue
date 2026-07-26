<template>
	<section class="cms-page">
		<div v-if="isLoading" class="container page-content cms-page__inner">
			<div class="text-center py-5">
				<div class="spinner-border text-primary" role="status">
					<span class="visually-hidden">Loading...</span>
				</div>
			</div>
		</div>

		<div v-else-if="error" class="container page-content cms-page__inner">
			<div class="alert alert-danger my-5">
				{{ error }}
			</div>
		</div>

		<template v-else-if="page">
			<template v-for="(segment, si) in segments" :key="si">
				<div v-if="segment.type === 'content'" class="container page-content cms-page__inner">
					<header v-if="si === 0 && showPageTitle" class="cms-page__header">
						<h1 class="cms-page__title">{{ page.title }}</h1>
					</header>

					<div class="cms-page__blocks">
						<template v-for="block in segment.blocks" :key="block.id">
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

							<figure
								v-else-if="block.type === 'image' && block.data?.url"
								class="cms-block cms-block--image"
							>
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
						</template>
					</div>
				</div>

				<section
					v-else-if="segment.type === 'faq'"
					class="contact-faq"
					aria-labelledby="cms-faq-title"
				>
					<div class="contact-faq__inner">
						<h2 id="cms-faq-title" class="contact-faq__title">FAQs</h2>
						<div class="contact-faq__list">
							<details
								v-for="(item, idx) in (segment.block.data?.items || [])"
								:key="idx"
								class="contact-faq__item"
							>
								<summary class="contact-faq__question">
									<span class="contact-faq__question-text">{{ item.question }}</span>
									<span class="contact-faq__toggle" aria-hidden="true" />
								</summary>
								<div class="contact-faq__answer">
									<p>{{ item.answer }}</p>
								</div>
							</details>
						</div>
					</div>
					<div class="seo-faq-schema" aria-hidden="true">
						<FaqJsonLd :items="faqItems(segment.block)" />
					</div>
				</section>
			</template>
		</template>
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
	const first = blocks.find((b) => b.type !== 'faq')
	if (first?.type === 'heading' && Number(first.data?.level) === 1) {
		return false
	}
	return true
})

/** Group blocks so FAQ is a full-width section outside .container (no white gap). */
const segments = computed(() => {
	const blocks = page.value?.blocks || []
	const out = []
	let content = null
	for (const block of blocks) {
		if (block.type === 'faq') {
			if (content) {
				out.push(content)
				content = null
			}
			out.push({ type: 'faq', block })
			continue
		}
		if (!content) content = { type: 'content', blocks: [] }
		content.blocks.push(block)
	}
	if (content) out.push(content)
	return out
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
	width: 100%;
	padding: 0;
	margin: 0;
}

.cms-page .page-content {
	/* banner (~48px) + fixed navbar (~72px) + breathing room */
	padding-top: 140px;
	padding-bottom: 2.5rem;
}

.cms-page__title {
	font-size: clamp(1.75rem, 3vw, 2.5rem);
	font-weight: 700;
	margin-bottom: 1.5rem;
}

.cms-block {
	margin-bottom: 1.75rem;
}

.cms-block:last-child {
	margin-bottom: 0;
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

/* Same band as Contact Us — full width, sits flush above benefits/footer */
.contact-faq {
	width: 100%;
	background: #c39e3d;
	padding: 100px 0;
	box-sizing: border-box;
	margin: 0;
}

.contact-faq__inner {
	max-width: 1230px;
	margin: 0 auto;
	padding: 0 24px;
	box-sizing: border-box;
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 40px;
}

.contact-faq__title {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(2rem, 4vw, 48px);
	line-height: 1.15;
	text-align: center;
	color: #fff9ee;
}

.contact-faq__list {
	width: 100%;
	max-width: 1228px;
	display: flex;
	flex-direction: column;
	gap: 20px;
}

.contact-faq__item {
	border: 3px solid #000;
	background: #fff;
	box-sizing: border-box;
	width: 100%;
}

.contact-faq__question {
	display: flex;
	align-items: center;
	justify-content: space-between;
	gap: 16px;
	min-height: 80px;
	padding: 14px 20px;
	cursor: pointer;
	list-style: none;
	box-sizing: border-box;
	background: #fff;
	color: #000;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 700;
	font-size: clamp(1.125rem, 2vw, 24px);
	line-height: 1.2;
}

.contact-faq__question::-webkit-details-marker {
	display: none;
}

.contact-faq__item[open] .contact-faq__question {
	background: #000;
	color: #fff;
}

.contact-faq__question-text {
	flex: 1;
}

.contact-faq__toggle {
	position: relative;
	flex-shrink: 0;
	width: 18px;
	height: 18px;
}

.contact-faq__toggle::before,
.contact-faq__toggle::after {
	content: '';
	position: absolute;
	background: #000;
	left: 50%;
	top: 50%;
	transform: translate(-50%, -50%);
}

.contact-faq__toggle::before {
	width: 18px;
	height: 3px;
}

.contact-faq__toggle::after {
	width: 3px;
	height: 18px;
}

.contact-faq__item[open] .contact-faq__toggle::before,
.contact-faq__item[open] .contact-faq__toggle::after {
	background: #fff;
}

.contact-faq__item[open] .contact-faq__toggle::after {
	display: none;
}

.contact-faq__answer {
	padding: 16px 20px 20px;
	background: #fff;
	box-sizing: border-box;
}

.contact-faq__answer p {
	margin: 0;
	font-family: Montserrat, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
	font-weight: 500;
	font-size: clamp(1rem, 1.8vw, 20px);
	line-height: 1.4;
	color: #000;
}

@media (max-width: 991.98px) {
	.cms-page .page-content {
		padding-top: 120px;
		padding-bottom: 2rem;
	}

	.contact-faq {
		padding: 64px 0 72px;
	}

	.contact-faq__inner {
		gap: 28px;
	}

	.contact-faq__question {
		min-height: 64px;
		padding: 14px 16px;
	}
}
</style>
