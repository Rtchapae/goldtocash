<template>
	<BaseModal id="mainFileCreating" title="Upload a Document" :open="open" @close="$emit('close')">
		<form @submit.prevent="$emit('submit', { type: form.type, file: form.file })">
			<table class="table table-striped">
				<tbody>
					<tr>
						<td>Document Type</td>
						<td>
							<select v-model="form.type" id="verification-file-type" name="verification-file-type" class="form-control">
								<option value="license">Driver License</option>
								<option value="passport">Passport</option>
								<option value="military">Military ID</option>
								<option value="state">State ID</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>Upload a Document</td>
						<td>
							<input
								type="file"
								name="verification-file-uploads"
								class="form-control"
								placeholder="Select Document"
								@change="onFileChange"
							>
						</td>
					</tr>
				</tbody>
			</table>
		</form>

		<template #footer>
			<button type="button" class="btn btn-secondary btn-fw" @click="$emit('close')" :disabled="isLoading">Cancel</button>
			<button type="button" class="btn btn-primary btn-fw" @click="$emit('submit', { type: form.type, file: form.file })" :disabled="isLoading || !form.file">
				<span v-if="isLoading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
				{{ isLoading ? 'Uploading...' : 'Upload' }}
			</button>
		</template>
	</BaseModal>
</template>

<script setup>
import { reactive, watch } from 'vue'
import BaseModal from './BaseModal.vue'

const props = defineProps({
	open: { type: Boolean, default: false },
	isLoading: { type: Boolean, default: false },
})

defineEmits(['close', 'submit'])

const form = reactive({
	type: 'license',
	file: null,
})

const onFileChange = (e) => {
	form.file = e?.target?.files?.[0] ?? null
}

// Reset form when modal closes
watch(
	() => props.open,
	(isOpen) => {
		if (!isOpen) {
			form.type = 'license'
			form.file = null
			// Reset file input
			const fileInput = document.querySelector('#mainFileCreating input[type="file"]')
			if (fileInput) {
				fileInput.value = ''
			}
		}
	}
)
</script>


