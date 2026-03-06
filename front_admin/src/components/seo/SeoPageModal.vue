<template>
	<div v-if="show" class="modal fade show d-block" tabindex="-1" role="dialog" style="background-color: rgba(0, 0, 0, 0.5);">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">{{ isEditing ? 'Edit SEO Page' : 'Create SEO Page' }}</h5>
					<button type="button" class="btn-close" @click="closeModal" :disabled="loading"></button>
				</div>
				<div class="modal-body">
					<form @submit.prevent="handleSubmit">
						<div class="row">
							<div class="col-md-6 mb-3">
								<label class="form-label">Route Name <span class="text-danger">*</span></label>
								<input
									v-model="formData.route_name"
									type="text"
									class="form-control"
									required
									placeholder="e.g., home, about, contact"
									:disabled="loading"
								/>
								<small class="form-text text-muted">Unique identifier for the route (e.g., 'home', 'about')</small>
							</div>
							<div class="col-md-6 mb-3">
								<label class="form-label">Page URL <span class="text-danger">*</span></label>
								<input
									v-model="formData.page_url"
									type="text"
									class="form-control"
									required
									placeholder="e.g., /, /about, /contact"
									:disabled="loading"
								/>
								<small class="form-text text-muted">The URL path for this page</small>
							</div>
						</div>

						<div class="row">
							<div class="col-12 mb-3">
								<label class="form-label">Page Title <span class="text-danger">*</span></label>
								<input
									v-model="formData.page_title"
									type="text"
									class="form-control"
									required
									placeholder="Human readable page title"
									:disabled="loading"
								/>
								<small class="form-text text-muted">Display name for the page</small>
							</div>
						</div>

						<div class="row">
							<div class="col-12 mb-3">
								<label class="form-label">Meta Title</label>
								<input
									v-model="formData.meta_title"
									type="text"
									class="form-control"
									placeholder="SEO title for search engines"
									maxlength="255"
									:disabled="loading"
								/>
								<small class="form-text text-muted">Leave empty to use page title. Recommended: 50-60 characters</small>
							</div>
						</div>

						<div class="row">
							<div class="col-12 mb-3">
								<label class="form-label">Meta Description</label>
								<textarea
									v-model="formData.meta_description"
									class="form-control"
									rows="3"
									placeholder="SEO description for search engines"
									maxlength="500"
									:disabled="loading"
								></textarea>
								<small class="form-text text-muted">Recommended: 150-160 characters</small>
							</div>
						</div>

						<div class="row">
							<div class="col-12 mb-3">
								<label class="form-label">Meta Keywords</label>
								<div v-for="(keyword, index) in formData.meta_keywords" :key="index" class="d-flex mb-2">
									<input
										v-model="formData.meta_keywords[index]"
										type="text"
										class="form-control me-2"
										placeholder="Enter keyword"
										:disabled="loading"
									/>
									<button
										type="button"
										class="btn btn-sm btn-outline-danger"
										@click="removeKeyword(index)"
										:disabled="loading"
									>
										<iconify-icon icon="solar:trash-bin-minimalistic-outline" />
									</button>
								</div>
								<button
									type="button"
									class="btn btn-sm btn-outline-primary"
									@click="addKeyword"
									:disabled="loading"
								>
									<iconify-icon icon="solar:add-circle-outline" class="me-1" />
									Add Keyword
								</button>
								<small class="form-text text-muted d-block mt-1">Optional SEO keywords for the page</small>
							</div>
						</div>

						<div class="row">
							<div class="col-12 mb-3">
								<div class="form-check">
									<input
										v-model="formData.is_active"
										class="form-check-input"
										type="checkbox"
										id="is_active"
										:disabled="loading"
									/>
									<label class="form-check-label" for="is_active">
										Active
									</label>
								</div>
								<small class="form-text text-muted">Inactive pages won't be used for SEO</small>
							</div>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" @click="closeModal" :disabled="loading">
						Cancel
					</button>
					<button type="button" class="btn btn-primary" @click="handleSubmit" :disabled="loading || !isFormValid">
						<span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
						{{ isEditing ? 'Update' : 'Create' }} SEO Page
					</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'

const props = defineProps({
	show: {
		type: Boolean,
		default: false,
	},
	seoPage: {
		type: Object,
		default: null,
	},
	loading: {
		type: Boolean,
		default: false,
	},
})

const emit = defineEmits(['close', 'save'])

const formData = ref({
	route_name: '',
	page_url: '',
	page_title: '',
	meta_title: '',
	meta_description: '',
	meta_keywords: [],
	is_active: true,
})

const isEditing = computed(() => !!props.seoPage)

const isFormValid = computed(() => {
	return formData.value.route_name.trim() &&
		   formData.value.page_url.trim() &&
		   formData.value.page_title.trim()
})

watch(() => props.seoPage, (newSeoPage) => {
	if (newSeoPage) {
		formData.value = {
			route_name: newSeoPage.route_name || '',
			page_url: newSeoPage.page_url || '',
			page_title: newSeoPage.page_title || '',
			meta_title: newSeoPage.meta_title || '',
			meta_description: newSeoPage.meta_description || '',
			meta_keywords: Array.isArray(newSeoPage.meta_keywords) ? [...newSeoPage.meta_keywords] : [],
			is_active: newSeoPage.is_active !== undefined ? newSeoPage.is_active : true,
		}
	} else {
		formData.value = {
			route_name: '',
			page_url: '',
			page_title: '',
			meta_title: '',
			meta_description: '',
			meta_keywords: [],
			is_active: true,
		}
	}
}, { immediate: true })

const addKeyword = () => {
	formData.value.meta_keywords.push('')
}

const removeKeyword = (index) => {
	formData.value.meta_keywords.splice(index, 1)
}

const handleSubmit = () => {
	if (!isFormValid.value) return

	const cleanedKeywords = formData.value.meta_keywords.filter(keyword => keyword.trim() !== '')

	emit('save', {
		...formData.value,
		meta_keywords: cleanedKeywords,
	})
}

const closeModal = () => {
	emit('close')
}
</script>
