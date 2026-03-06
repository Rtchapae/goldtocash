<template>
	<BaseModal id="editDocsSettings" title="Edit Government ID" :open="open" @close="$emit('close')">
		<form @submit.prevent="$emit('submit', form)">
			<table class="table table-striped">
				<tbody>
					<tr>
						<td>Government ID</td>
						<td>
							<select v-model="form.type" id="verification-docs-type" name="verification-docs-type" class="form-control">
								<option value="license">Driver License</option>
								<option value="passport">Passport</option>
								<option value="military">Military ID</option>
								<option value="state">State ID</option>
							</select>
						</td>
					</tr>

					<tr v-if="form.type === 'license'">
						<td>License Number</td>
						<td><input v-model="form.idNumber" id="verification-docs-license-number" name="verification-docs-license-number" type="text" class="form-control"></td>
					</tr>
					<tr v-if="form.type === 'license'">
						<td>Issuer State</td>
						<td><input v-model="form.issuer" id="verification-docs-license-state" name="verification-docs-license-state" type="text" class="form-control"></td>
					</tr>

					<tr v-if="form.type === 'passport'">
						<td>Passport Number</td>
						<td><input v-model="form.idNumber" id="verification-docs-passport-number" name="verification-docs-passport-number" type="text" class="form-control"></td>
					</tr>
					<tr v-if="form.type === 'passport'">
						<td>Issuer Country</td>
						<td><input v-model="form.issuer" id="verification-docs-passport-country" name="verification-docs-passport-country" type="text" class="form-control"></td>
					</tr>

					<tr v-if="form.type === 'military'">
						<td>Military ID Number</td>
						<td><input v-model="form.idNumber" id="verification-docs-military-number" name="verification-docs-military-number" type="text" class="form-control"></td>
					</tr>
					<tr v-if="form.type === 'military'">
						<td>Issuer State</td>
						<td><input v-model="form.issuer" id="verification-docs-military-state" name="verification-docs-military-state" type="text" class="form-control"></td>
					</tr>

					<tr v-if="form.type === 'state'">
						<td>State ID Number</td>
						<td><input v-model="form.idNumber" id="verification-docs-state-number" name="verification-docs-state-number" type="text" class="form-control"></td>
					</tr>
					<tr v-if="form.type === 'state'">
						<td>Issuer State</td>
						<td><input v-model="form.issuer" id="verification-docs-state-state" name="verification-docs-state-state" type="text" class="form-control"></td>
					</tr>
				</tbody>
			</table>
		</form>

		<template #footer>
			<button type="button" class="btn btn-secondary btn-fw" @click="$emit('close')" :disabled="isLoading">Cancel</button>
			<button type="button" class="btn btn-primary btn-fw" @click="$emit('submit', form)" :disabled="isLoading">
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
	initial: { type: Object, default: null },
	isLoading: { type: Boolean, default: false },
})

defineEmits(['close', 'submit'])

const form = reactive({
	type: 'state',
	idNumber: '',
	issuer: '',
})

// Helper to sync initial data to form
const syncInitialToForm = (v) => {
	if (!v) return
	form.type = v?.type ?? 'state'
	form.idNumber = v?.idNumber ?? ''
	form.issuer = v?.issuer ?? ''
}

// Update form when initial data changes
watch(
	() => props.initial,
	(v) => {
		if (props.open && v) {
			syncInitialToForm(v)
		}
	},
	{ immediate: true, deep: true }
)

// Also update form when modal opens (in case initial data was already loaded)
watch(
	() => props.open,
	(isOpen) => {
		if (isOpen && props.initial) {
			syncInitialToForm(props.initial)
		}
	}
)
</script>


