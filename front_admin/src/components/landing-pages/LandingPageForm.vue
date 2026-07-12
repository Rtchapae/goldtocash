<template>
	<form @submit.prevent="handleSubmit">
		<div class="row g-3">
			<div class="col-12 col-lg-8">
				<div class="mb-3">
					<label class="form-label">Page title (internal)</label>
					<input v-model="modelValue.title" type="text" class="form-control" required placeholder="Summer gold offer" />
				</div>

				<div class="mb-3">
					<label class="form-label">Public URL path</label>
					<div class="input-group">
						<span class="input-group-text">goldtocash.us</span>
						<input
							v-model="modelValue.path"
							type="text"
							class="form-control"
							required
							placeholder="/lp/summer-offer"
							@blur="normalizePath(modelValue)"
						/>
					</div>
					<div class="form-text">Use lowercase letters, numbers, and hyphens. Example: /lp/my-page</div>
				</div>

				<BlockEditor v-model="modelValue.blocks" />
			</div>

			<div class="col-12 col-lg-4">
				<div class="card">
					<div class="card-body">
						<h6 class="card-title">Publish</h6>
						<div class="form-check form-switch mb-3">
							<input id="lp-active" v-model="modelValue.active" class="form-check-input" type="checkbox" />
							<label class="form-check-label" for="lp-active">Published</label>
						</div>

						<div class="mb-3">
							<label class="form-label">SEO title</label>
							<input v-model="modelValue.seo_title" type="text" class="form-control" maxlength="255" />
						</div>
						<div class="mb-3">
							<label class="form-label">SEO description</label>
							<textarea v-model="modelValue.seo_description" class="form-control" rows="3" maxlength="500" />
						</div>

						<button type="submit" class="btn btn-primary w-100" :disabled="isSubmitting">
							{{ isSubmitting ? 'Saving…' : (isEditMode ? 'Update page' : 'Publish page') }}
						</button>
					</div>
				</div>
			</div>
		</div>
	</form>
</template>

<script setup>
import BlockEditor from './BlockEditor.vue'

defineProps({
	modelValue: { type: Object, required: true },
	isSubmitting: { type: Boolean, default: false },
	isEditMode: { type: Boolean, default: false },
})

const emit = defineEmits(['update:modelValue', 'submit'])

const normalizePath = (modelValue) => {
	let path = (modelValue.path || '').trim()
	if (!path.startsWith('/')) path = `/${path}`
	path = path.replace(/\/+/g, '/').replace(/\/$/, '') || '/'
	emit('update:modelValue', { ...modelValue, path })
}

const handleSubmit = () => {
	emit('submit')
}
</script>
