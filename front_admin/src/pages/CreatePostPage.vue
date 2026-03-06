<template>
	<div>
		<NavbarHeader />
		<div class="dashboard-main-body">
			<PageHeader :title="isEditMode ? 'Edit post' : 'Create new post'" :loading="isSubmitting || isLoading" />

			<div class="row">
				<div id="admin-main-tables" class="col-lg-12 grid-margin stretch-card">
					<div class="card">
						<div class="card-body post-create-body">
							<div class="card-text">
								<PostForm
									:key="`post-form-${postId || 'create'}`"
									v-model="form"
									:is-submitting="isSubmitting"
									@submit="handleSubmit"
									ref="postFormRef"
								/>

								<FormTemplatesModal />
								<CalculatorTemplatesModal />
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import { ref, onMounted, computed, watch, nextTick } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import NavbarHeader from '@/components/NavbarHeader.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import { PostForm, FormTemplatesModal, CalculatorTemplatesModal } from '@/components/posts'
import { createPost, getPost, updatePost } from '@/api/posts'
import { useToast } from '@/composables/useToast'

const router = useRouter()
const route = useRoute()
const toast = useToast()

const postFormRef = ref(null)
const isSubmitting = ref(false)
const isLoading = ref(false)

const isEditMode = computed(() => !!route.params.id)
const postId = computed(() => route.params.id ? parseInt(route.params.id) : null)

const form = ref({
	title: '',
	seo_title: '',
	seo_description: '',
	path_prefix: '',
	body: '',
	active: true
})

const loadPost = async () => {
	if (!isEditMode.value || !postId.value) {
		return
	}

	isLoading.value = true
	try {
		const response = await getPost(postId.value)
		
		const postData = response.data || response
		
		form.value = {
			title: postData.title || '',
			seo_title: postData.seo_title || '',
			seo_description: postData.seo_description || '',
			path_prefix: postData.path_prefix || '',
			body: postData.body || '',
			active: postData.active ?? true
		}
	} catch (error) {
		const errorMessage = error.status === 404
			? 'Post not found'
			: error.status === 401
			? 'Unauthorized. Please login again.'
			: error.status === 500
			? 'Server error. Please try again later.'
			: 'Failed to load post'
		toast.error(errorMessage)
		router.push('/posts')
	} finally {
		isLoading.value = false
	}
}

const handleSubmit = async ({ form: formData, imageFile }) => {
	const title = (formData.title || '').trim()
	const body = (formData.body || '').trim()
	
	if (!title || !body) {
		toast.error('Title and body are required')
		return
	}

	try {
		isSubmitting.value = true

		const submitFormData = new FormData()
		submitFormData.append('title', title)
		submitFormData.append('body', body)
		submitFormData.append('seo_title', formData.seo_title || '')
		submitFormData.append('seo_description', formData.seo_description || '')
		submitFormData.append('path_prefix', formData.path_prefix || '')
		submitFormData.append('active', formData.active ? '1' : '0')

		if (imageFile) {
			submitFormData.append('image', imageFile)
		}

		if (isEditMode.value && postId.value) {
			await updatePost(postId.value, submitFormData)
			toast.success('Post updated successfully')
		} else {
			await createPost(submitFormData)
			toast.success('Post created successfully')
		}
		
		router.push('/posts')
	} catch (error) {
		let errorMessage = error.message || (isEditMode.value ? 'Failed to update post' : 'Failed to create post')
		
		if (error.status === 401) {
			errorMessage = 'Unauthorized. Please login again.'
		} else if (error.status === 422) {
			errorMessage = error.message || 'Validation failed. Please check your input.'
		} else if (error.status === 500) {
			errorMessage = 'Server error. Please try again later.'
		}
		
		toast.error(errorMessage)
	} finally {
		isSubmitting.value = false
	}
}

watch(() => route.params.id, async (newId, oldId) => {
	if (newId !== oldId) {
		form.value = {
			title: '',
			seo_title: '',
			seo_description: '',
			path_prefix: '',
			body: '',
			active: true
		}
		await nextTick()
		if (newId) {
			loadPost()
		}
	}
}, { immediate: false })

onMounted(() => {
	loadPost()
})
</script>
