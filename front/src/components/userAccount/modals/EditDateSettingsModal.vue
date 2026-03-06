<template>
	<BaseModal id="editDateSettings" title="Edit Date of Birth" :open="open" @close="$emit('close')">
		<form @submit.prevent="$emit('submit', { dateOfBirth: form.dateOfBirth })">
			<table class="table table-striped">
				<tbody>
					<tr>
						<td>Date of Birth</td>
						<td>
							<input
								v-model="form.dateOfBirth"
								id="verification-date-of-birth"
								name="verification-date-of-birth"
								type="date"
								class="form-control"
							>
						</td>
					</tr>
				</tbody>
			</table>
		</form>

		<template #footer>
			<button type="button" class="btn btn-secondary btn-fw" @click="$emit('close')" :disabled="isLoading">Cancel</button>
			<button type="button" class="btn btn-primary btn-fw" @click="$emit('submit', { dateOfBirth: form.dateOfBirth })" :disabled="isLoading">
				<span v-if="isLoading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
				{{ isLoading ? 'Updating...' : 'Update' }}
			</button>
		</template>
	</BaseModal>
</template>

<script setup>
import { reactive, watch } from 'vue'
import BaseModal from './BaseModal.vue'

const props = defineProps({
	open: { type: Boolean, default: false },
	dateOfBirth: { type: String, default: '' },
	isLoading: { type: Boolean, default: false },
})

defineEmits(['close', 'submit'])

const form = reactive({ dateOfBirth: '' })

// Helper to sync date of birth to form
const syncDateToForm = (v) => {
	form.dateOfBirth = v || ''
}

// Update form when dateOfBirth changes
watch(
	() => props.dateOfBirth,
	(v) => {
		if (props.open) {
			syncDateToForm(v)
		}
	},
	{ immediate: true }
)

// Also update form when modal opens (in case dateOfBirth was already loaded)
watch(
	() => props.open,
	(isOpen) => {
		if (isOpen && props.dateOfBirth) {
			syncDateToForm(props.dateOfBirth)
		}
	}
)
</script>


