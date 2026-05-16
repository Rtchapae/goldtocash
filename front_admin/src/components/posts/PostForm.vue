<template>
	<form @submit.prevent="handleSubmit" class="post-form">
		<div class="form-group mb-4">
			<label for="title" class="form-label fw-semibold mb-2">Post title</label>
			<input
				v-model="form.title"
				type="text"
				id="title"
				class="form-control"
				placeholder="write post title here.."
				required
			/>
		</div>

		<div class="row mb-4">
			<div class="col-md-3">
				<div class="form-group">
					<label for="seo_title" class="form-label fw-semibold mb-2">SEO Title</label>
					<input
						v-model="form.seo_title"
						type="text"
						id="seo_title"
						class="form-control"
					/>
				</div>
			</div>
			<div class="col-md-9">
				<div class="form-group">
					<label for="seo_description" class="form-label fw-semibold mb-2">SEO Description</label>
					<input
						v-model="form.seo_description"
						type="text"
						id="seo_description"
						class="form-control"
						maxlength="160"
					/>
				</div>
			</div>
		</div>

		<div class="form-group mb-4">
			<label for="path_prefix" class="form-label fw-semibold mb-2">Path Prefix</label>
			<select
				v-model="form.path_prefix"
				id="path_prefix"
				class="form-control"
			>
				<option
					v-for="option in pathPrefixOptions"
					:key="option.value"
					:value="option.value"
				>
					{{ option.label }}
				</option>
			</select>
		</div>

		<div class="mb-4">
			<a class="btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#form-templates-modal">
				View Form Templates
			</a>
		</div>

		<div class="mb-4">
			<a class="btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#calculator-templates-modal">
				View Calculator Templates
			</a>
		</div>

		<div class="form-group mb-4">
			<label for="body" class="form-label fw-semibold mb-2">Post body</label>
			<Editor
				v-model="form.body"
				:init="editorInit"
				:api-key="tinymceApiKey"
			/>
		</div>

		<div class="form-group mb-4">
			<label for="image" class="form-label fw-semibold mb-2">Featured image</label>
			<div v-if="featuredPreviewUrl" class="mb-2">
				<img
					:src="featuredPreviewUrl"
					alt=""
					class="img-thumbnail post-featured-preview"
					style="max-height: 220px; max-width: 100%; object-fit: contain;"
				/>
			</div>
			<div class="d-flex flex-wrap align-items-center gap-2 mb-2">
				<input
					ref="imageInput"
					type="file"
					name="image"
					id="image"
					class="form-control flex-grow-1"
					style="min-width: 200px;"
					accept="image/*"
					@click="onFeaturedImageClick"
					@change="onFeaturedImageChange"
				/>
				<button
					v-if="canRemoveFeatured"
					type="button"
					class="btn btn-outline-danger btn-sm"
					@click="markRemoveFeatured"
				>
					Remove image
				</button>
			</div>
			<p v-if="pendingRemoveFeatured" class="text-warning small mb-1">
				On Save, the featured image will be removed (default image on the public site).
			</p>
			<p class="form-text text-muted small mb-0">
				JPEG, PNG, WebP or GIF, up to 5&nbsp;MB.
			</p>
		</div>

		<div class="form-group mb-4">
			<div class="form-check publish-checkbox">
				<input
					v-model="form.active"
					type="checkbox"
					id="active"
					class="form-check-input"
				/>
				<label for="active" class="form-check-label">Publish</label>
			</div>
		</div>

		<div class="form-group">
			<button type="submit" class="btn btn-primary" :disabled="isSubmitting">
				{{ isSubmitting ? 'Saving...' : 'Save Post' }}
			</button>
		</div>
	</form>
</template>

<script setup>
import { ref, watch, computed, onBeforeUnmount } from 'vue'
import Editor from '@tinymce/tinymce-vue'
import { API_BASE_URL } from '@/api/client'
import { resolveAdminAssetUrl } from '@/utils/apiAssetUrl'

const props = defineProps({
	modelValue: {
		type: Object,
		required: true
	},
	isSubmitting: {
		type: Boolean,
		default: false
	}
})

const emit = defineEmits(['update:modelValue', 'submit'])

const form = ref({ ...props.modelValue })
const imageInput = ref(null)
const isUpdatingFromProps = ref(false)
/** Local object URL for a newly chosen file (not persisted until Save). */
const localPreviewObjectUrl = ref(null)
/**
 * Selected featured file held separately from the file input: the input can be cleared when
 * the parent v-model updates (e.g. TinyMCE body sync), which would otherwise drop the file before Save.
 */
const pendingFeaturedFile = ref(null)
/** User chose to drop featured image on next save (server uses default on public site). */
const pendingRemoveFeatured = ref(false)

const featuredPreviewUrl = computed(() => {
	if (pendingRemoveFeatured.value) {
		return null
	}
	if (localPreviewObjectUrl.value) {
		return localPreviewObjectUrl.value
	}
	const u = form.value?.image
	if (typeof u !== 'string' || !u.trim()) {
		return null
	}
	return resolveAdminAssetUrl(u.trim())
})

const canRemoveFeatured = computed(() => {
	if (pendingRemoveFeatured.value) {
		return false
	}
	return !!(
		pendingFeaturedFile.value
		|| localPreviewObjectUrl.value
		|| (typeof form.value?.image === 'string' && form.value.image.trim())
	)
})

const markRemoveFeatured = () => {
	pendingFeaturedFile.value = null
	revokeLocalPreview()
	pendingRemoveFeatured.value = true
	if (imageInput.value) {
		imageInput.value.value = ''
	}
}

const revokeLocalPreview = () => {
	if (localPreviewObjectUrl.value) {
		URL.revokeObjectURL(localPreviewObjectUrl.value)
		localPreviewObjectUrl.value = null
	}
}

const onFeaturedImageClick = (e) => {
	// Allow choosing the same file again (change event would not fire otherwise).
	const input = e.target
	if (input && 'value' in input) {
		input.value = ''
	}
}

const onFeaturedImageChange = (e) => {
	revokeLocalPreview()
	pendingRemoveFeatured.value = false
	const file = e.target?.files?.[0]
	if (!file) {
		pendingFeaturedFile.value = null
		return
	}
	pendingFeaturedFile.value = file
	localPreviewObjectUrl.value = URL.createObjectURL(file)
}

const pathPrefixOptions = [
	{ value: '', label: '/gold-info' },
	{ value: '/sell-gold', label: '/sell-gold' }
]

/** TinyMCE resolves img src against the admin page origin; prepend API origin when they differ (Vite dev). */
function tinymceImagePrependUrl() {
	const resolved = resolveAdminAssetUrl('/storage/')
	if (!resolved || !/^https?:\/\//i.test(resolved)) {
		return ''
	}
	try {
		return new URL(resolved).origin
	} catch {
		return ''
	}
}

const tinymceApiKey = computed(() => {
	return import.meta.env.VITE_TINYMCE_API_KEY
})

const editorInit = {
	height: 800,
	menubar: false,
	plugins: 'code table lists image',
	toolbar: 'undo redo | formatselect| bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | code | table | image',
	content_style: 'body { font-family: Montserrat, sans-serif; font-size: 14px; }',
	automatic_uploads: true,
	images_reuse_filename: false,
	image_dimensions: false,
	image_advtab: false,
	image_caption: true,
	image_class_list: [
		{ title: 'None', value: '' },
		{ title: 'Rounded', value: 'img-rounded' },
		{ title: 'Circle', value: 'img-circle' },
		{ title: 'Thumbnail', value: 'img-thumbnail' }
	],
	image_list: false,
	image_title: true,
	image_description: true,
	image_prepend_url: tinymceImagePrependUrl(),
	convert_urls: false,
	relative_urls: false,
	remove_script_host: false,
	dialog_type: 'modal',
	skin: 'oxide',
	content_css: false,
	images_upload_handler: async (blobInfo, progress) => {
		return new Promise(async (resolve, reject) => {
			try {
				const formData = new FormData()
				formData.append('file', blobInfo.blob(), blobInfo.filename())
				
				const token = window.localStorage.getItem('admin_access_token')
				if (!token) {
					reject('Authentication required')
					return
				}
				
				const uploadUrl = `${API_BASE_URL}/admin/posts/upload-image`
				
				const response = await fetch(uploadUrl, {
					method: 'POST',
					headers: {
						'Authorization': `Bearer ${token}`,
						'Accept': 'application/json'
					},
					body: formData
				})
				
				if (!response.ok) {
					const errorData = await response.json().catch(() => ({ message: `HTTP error! status: ${response.status}` }))
					reject(errorData.message || `HTTP error! status: ${response.status}`)
					return
				}
				
				const json = await response.json()
				
				if (!json || typeof json.location !== 'string') {
					reject('Invalid response: ' + JSON.stringify(json))
					return
				}

				resolve(json.location)
			} catch (error) {
				reject('Image upload failed: ' + error.message)
			}
		})
	},
	setup: (editor) => {
		editor.on('change', () => {
			form.value.body = editor.getContent()
		})
		editor.on('input', () => {
			form.value.body = editor.getContent()
		})
		
		editor.on('ObjectResized', (e) => {
			if (e.target.nodeName === 'IMG') {
				console.log('Image resized in editor:', e.target.src)
			}
		})
		
		editor.on('SetContent', () => {
			console.log('Editor content set')
		})
	}
}

watch(() => props.modelValue, (newValue, oldValue) => {
	if (JSON.stringify(newValue) === JSON.stringify(oldValue)) {
		return
	}

	const prevImg = oldValue && typeof oldValue.image === 'string' ? oldValue.image : null
	const nextImg = newValue && typeof newValue.image === 'string' ? newValue.image : null
	const sameImageRef = (prevImg || '') === (nextImg || '')
	// Only drop a local file choice when the server-backed image URL actually changed (reload / other tab).
	if (!sameImageRef) {
		pendingFeaturedFile.value = null
		revokeLocalPreview()
		if (imageInput.value) {
			imageInput.value.value = ''
		}
	}

	isUpdatingFromProps.value = true
	form.value = { ...newValue }
	setTimeout(() => {
		isUpdatingFromProps.value = false
	}, 0)
}, { deep: true })

watch(() => form.value, (newValue) => {
	if (!isUpdatingFromProps.value) {
		emit('update:modelValue', { ...newValue })
	}
}, { deep: true })

const handleSubmit = () => {
	emit('submit', {
		form: { ...form.value },
		imageFile: pendingFeaturedFile.value || imageInput.value?.files?.[0] || null,
		removeFeaturedImage: pendingRemoveFeatured.value
	})
}

onBeforeUnmount(() => {
	pendingFeaturedFile.value = null
	revokeLocalPreview()
})

defineExpose({
	imageInput
})
</script>
