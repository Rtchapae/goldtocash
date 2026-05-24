<template>
	<section class="section-blog">
		<div class="container page-content">
			<div v-if="isLoading" class="text-center py-5">
				<div class="spinner-border text-primary" role="status">
					<span class="visually-hidden">Loading...</span>
				</div>
			</div>

			<div v-else-if="error" class="alert alert-danger">
				{{ error }}
			</div>

			<div v-else-if="post" class="row align-items-center">
				<div class="col-12">
					<article class="blog-post">
						<header class="blog-title">
							<h1 class="font-bold">{{ post.title }}</h1>
							<div class="small">{{ post.date }}</div>
						</header>
						<div class="post-content">
							<img
								v-if="post.image"
								:key="`${post.id}-${post.image}`"
								class="blog-image float-right ml-16 show-for-large"
								:src="post.image"
								:alt="post.title"
							>
							<div v-html="normalizedBody"></div>
						</div>
						<a class="button primary font-bold mt-5" :href="basePath">
							<span>←</span> More Posts
						</a>
					</article>
				</div>
			</div>
		</div>
	</section>
</template>

<script setup>
import { ref, computed, onMounted, watch, nextTick, h, render, getCurrentInstance, onBeforeUnmount } from 'vue'
import { useRoute } from 'vue-router'
import { fetchPost } from '@/api/posts'
import seoService from '@/services/seoService'
import KitForm from '@/components/forms/KitForm.vue'

const route = useRoute()
const post = ref(null)
const isLoading = ref(false)
const error = ref(null)
const iframeRegex = /<iframe\b[^>]*src=["']([^"']*\/i\/(modern|traditional|sophisticated)-form[^"']*)["'][^>]*>(?:<\/iframe>)?/gi
const wrappedIframeRegex = /<span\b[^>]*>\s*<iframe\b[^>]*src=["']([^"']*\/i\/(modern|traditional|sophisticated)-form[^"']*)["'][^>]*>(?:<\/iframe>)?\s*<\/span>/gi
const instance = getCurrentInstance()
const mountedKitRoots = ref([])

const basePath = computed(() => {
	if (route.path.startsWith('/sell-gold/')) return '/sell-gold'
	if (route.path.startsWith('/sell/')) return '/sell'
	return '/gold-info'
})

const pathPrefix = computed(() => {
	if (basePath.value === '/sell-gold') return '/sell-gold'
	if (basePath.value === '/sell') return '/sell'
	return null
})

const normalizedBody = computed(() => {
	if (!post.value?.body) return ''
	return post.value.body
		.replace(wrappedIframeRegex, (_match, _src, template) => {
			return `<div class="kit-form-placeholder blog-kit-form" data-template="${template}"></div>`
		})
		.replace(iframeRegex, (_match, _src, template) => {
			return `<div class="kit-form-placeholder blog-kit-form" data-template="${template}"></div>`
		})
})

const mountKitForms = async () => {
	if (typeof window === 'undefined') return
	await nextTick()
	await new Promise(requestAnimationFrame)

	mountedKitRoots.value.forEach((el) => {
		render(null, el)
	})
	mountedKitRoots.value = []

	document.querySelectorAll('.kit-form-placeholder').forEach((el) => {
		const vnode = h(KitForm, { inline: true })
		if (instance?.appContext) {
			vnode.appContext = instance.appContext
		}
		render(vnode, el)
		mountedKitRoots.value.push(el)
	})
}

const loadPost = async () => {
	isLoading.value = true
	error.value = null
	post.value = null

	try {
		const response = await fetchPost(route.params.slug, pathPrefix.value)
		post.value = response?.data || null
		if (post.value) {
			const title = post.value.seo_title || post.value.title
			const description = post.value.seo_description || getExcerpt(post.value.body)
			seoService.setMeta({ title, description })
			seoService.sendGaPageView(route)
			await mountKitForms()
		}
	} catch (err) {
		error.value = err.message || 'Failed to load post'
		console.error('Failed to load post:', err)
	} finally {
		isLoading.value = false
	}
}

const getExcerpt = (html) => {
	if (!html) return ''
	if (typeof window === 'undefined') return ''
	const div = document.createElement('div')
	div.innerHTML = html
	const text = (div.textContent || '').replace(/\s+/g, ' ').trim()
	return text.length > 160 ? `${text.slice(0, 157)}...` : text
}

onMounted(() => {
	loadPost()
})

watch(() => route.params.slug, () => {
	loadPost()
})

watch(() => normalizedBody.value, () => {
	mountKitForms()
})

onBeforeUnmount(() => {
	mountedKitRoots.value.forEach((el) => {
		render(null, el)
	})
	mountedKitRoots.value = []
})
</script>

<style scoped>
</style>

