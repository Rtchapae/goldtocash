<template>
	<div>
		<NavbarHeader />
		<div class="dashboard-main-body">
			<PageHeader
				:title="isEditMode ? 'Edit landing page' : 'Create landing page'"
				:loading="isSubmitting || isLoading"
				back-to="/landing-pages"
				back-label="Back to pages"
			/>

			<div class="row">
				<div class="col-lg-12 grid-margin stretch-card">
					<div class="card">
						<div class="card-body">
							<LandingPageForm
								v-model="form"
								:is-submitting="isSubmitting"
								:is-edit-mode="isEditMode"
								@submit="handleSubmit"
							/>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import NavbarHeader from '@/components/NavbarHeader.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import { LandingPageForm } from '@/components/landing-pages'
import { createLandingPage, getLandingPage, updateLandingPage } from '@/api/landingPages'
import { createDefaultBlock } from '@/constants/landingPageBlocks'
import { useToast } from '@/composables/useToast'

const router = useRouter()
const route = useRoute()
const toast = useToast()

const isSubmitting = ref(false)
const isLoading = ref(false)
const isEditMode = computed(() => !!route.params.id)
const pageId = computed(() => (route.params.id ? parseInt(route.params.id) : null))

const form = ref({
	title: '',
	path: '/lp/',
	blocks: [createDefaultBlock('heading'), createDefaultBlock('text')],
	seo_title: '',
	seo_description: '',
	active: true,
})

const loadPage = async () => {
	if (!isEditMode.value || !pageId.value) return
	isLoading.value = true
	try {
		const response = await getLandingPage(pageId.value)
		const data = response.data || response
		form.value = {
			title: data.title || '',
			path: data.path || '/lp/',
			blocks: data.blocks?.length ? data.blocks : [createDefaultBlock('text')],
			seo_title: data.seo_title || '',
			seo_description: data.seo_description || '',
			active: data.active !== false,
		}
	} catch {
		toast.error('Failed to load page')
		router.push('/landing-pages')
	} finally {
		isLoading.value = false
	}
}

const handleSubmit = async () => {
	if (!form.value.blocks?.length) {
		toast.error('Add at least one content block')
		return
	}

	let path = (form.value.path || '').trim()
	if (!path.startsWith('/')) path = `/${path}`
	path = path.replace(/\/+/g, '/').replace(/\/$/, '') || '/'
	form.value.path = path

	isSubmitting.value = true
	try {
		const payload = {
			title: form.value.title,
			path: form.value.path,
			blocks: form.value.blocks,
			seo_title: form.value.seo_title || null,
			seo_description: form.value.seo_description || null,
			active: form.value.active,
		}

		if (isEditMode.value) {
			await updateLandingPage(pageId.value, payload)
			toast.success('Page updated')
		} else {
			await createLandingPage(payload)
			toast.success('Page published')
		}
		router.push('/landing-pages')
	} catch (error) {
		const message = error.data?.errors
			? Object.values(error.data.errors).flat().join(', ')
			: error.message || 'Failed to save page'
		toast.error(message)
	} finally {
		isSubmitting.value = false
	}
}

onMounted(loadPage)
watch(() => route.params.id, loadPage)
</script>

<style>
@import '@/styles/partials/_landing_pages.scss';
</style>
