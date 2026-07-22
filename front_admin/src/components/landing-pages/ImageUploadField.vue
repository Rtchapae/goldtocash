<template>
	<div class="lp-image-upload">
		<div v-if="modelValue" class="lp-image-upload__preview">
			<img :src="modelValue" alt="Preview" />
			<button type="button" class="btn btn-sm btn-outline-danger mt-2" @click="clearImage">
				Remove image
			</button>
		</div>
		<label class="lp-image-upload__label btn btn-outline-primary w-100">
			<input
				type="file"
				accept="image/*"
				class="d-none"
				:disabled="isUploading"
				@change="onFileChange"
			/>
			<span v-if="isUploading">Uploading…</span>
			<span v-else>{{ modelValue ? 'Replace image' : 'Upload image' }}</span>
		</label>
	</div>
</template>

<script setup>
import { ref } from 'vue'
import { uploadLandingPageImage } from '@/api/landingPages'
import { useToast } from '@/composables/useToast'

defineProps({
	modelValue: { type: String, default: '' },
})

const emit = defineEmits(['update:modelValue'])

const toast = useToast()
const isUploading = ref(false)

const onFileChange = async (event) => {
	const file = event.target.files?.[0]
	event.target.value = ''
	if (!file) return

	isUploading.value = true
	try {
		const result = await uploadLandingPageImage(file)
		emit('update:modelValue', result.location)
		toast.success('Image uploaded')
	} catch (error) {
		toast.error(error.message || 'Failed to upload image')
	} finally {
		isUploading.value = false
	}
}

const clearImage = () => {
	emit('update:modelValue', '')
}
</script>

<style scoped>
.lp-image-upload__preview img {
	max-width: 100%;
	height: auto;
	border-radius: 0.5rem;
}

.lp-image-upload__label {
	min-height: 2.75rem;
	display: flex;
	align-items: center;
	justify-content: center;
}
</style>
