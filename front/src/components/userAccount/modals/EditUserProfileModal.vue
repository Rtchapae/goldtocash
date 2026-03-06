<template>
	<BaseModal id="editUserProfile" title="Edit User Profile" :open="open" @close="$emit('close')">
		<form id="editUserProfileForm" @submit.prevent="$emit('submit', form)">
			<input name="modal-form-code" type="hidden" value="main-user-updating">
			<input id="modal-hidden-user-code" name="modal-user-code" type="hidden" :value="form.id || ''">

			<table class="table table-striped">
				<tbody>
					<tr>
						<td>User #</td>
						<td class="user-code">
							<input name="modal-user-code" type="text" class="form-control" :value="form.id || ''" disabled>
						</td>
					</tr>
					<tr>
						<td>First Name</td>
						<td class="user-name">
							<input v-model="form.first_name" name="modal-user-name" type="text" class="form-control">
						</td>
					</tr>
					<tr>
						<td>Last Name</td>
						<td class="user-sname">
							<input v-model="form.last_name" name="modal-user-sname" type="text" class="form-control">
						</td>
					</tr>
					<tr>
						<td>Email</td>
						<td class="user-login">
							<input v-model="form.email" name="modal-user-login" type="email" class="form-control">
						</td>
					</tr>
					<tr>
						<td>Phone</td>
						<td class="user-phone">
							<input v-model="form.phone" name="modal-user-phone" type="tel" class="form-control">
						</td>
					</tr>
					<tr>
						<td>Orders</td>
						<td class="user-orders">
							<input name="modal-user-orders" type="text" class="form-control" :value="ordersCount" disabled>
						</td>
					</tr>
					<tr>
						<td>Street</td>
						<td class="user-street">
							<input v-model="form.address" name="modal-user-street" type="text" class="form-control">
						</td>
					</tr>
					<tr>
						<td>City</td>
						<td class="user-city">
							<input v-model="form.city" name="modal-user-city" type="text" class="form-control">
						</td>
					</tr>
					<tr>
						<td>State</td>
						<td class="user-state">
							<select v-model="form.state" name="modal-user-state" class="form-control">
								<option value="" disabled hidden></option>
								<option v-for="s in US_STATES" :key="s.value" :value="s.value">{{ s.label }}</option>
							</select>
						</td>
					</tr>
					<tr>
						<td>Zip</td>
						<td class="user-zip">
							<input v-model="form.zip" name="modal-user-zip" type="text" class="form-control">
						</td>
					</tr>
					<tr>
						<td>Date Created</td>
						<td class="user-create">
							<input name="modal-user-create" type="text" class="form-control" :value="formattedCreatedAt" disabled>
						</td>
					</tr>
					<tr>
						<td>Last Update</td>
						<td class="user-update">
							<input name="modal-user-update" type="text" class="form-control" :value="formattedUpdatedAt" disabled>
						</td>
					</tr>
				</tbody>
			</table>
		</form>

		<template #footer>
			<button type="button" class="btn btn-secondary btn-fw" @click="$emit('close')" :disabled="isLoading">Cancel</button>
			<button type="submit" class="btn btn-primary btn-fw" form="editUserProfileForm" :disabled="isLoading">
				<span v-if="isLoading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
				{{ isLoading ? 'Updating...' : 'Update' }}
			</button>
		</template>
	</BaseModal>
</template>

<script setup>
import { reactive, watch, computed } from 'vue'
import BaseModal from './BaseModal.vue'
import { US_STATES } from '@/constants/states'
import { formatDateTime } from '@/utils/formatters'

const props = defineProps({
	open: { type: Boolean, default: false },
	user: { type: Object, default: null },
	ordersCount: { type: Number, default: 0 },
	isLoading: { type: Boolean, default: false },
})

defineEmits(['close', 'submit'])

const form = reactive({
	id: '',
	first_name: '',
	last_name: '',
	email: '',
	phone: '',
	address: '',
	city: '',
	state: '',
	zip: '',
	created_at: '',
	updated_at: '',
})

// Helper to sync user data to form
const syncUserToForm = (u) => {
	if (!u) return
	// Keep the same shape as backend payload; do not map/rename fields here.
	form.id = u?.id ?? ''
	form.first_name = u?.first_name ?? ''
	form.last_name = u?.last_name ?? ''
	form.email = u?.email ?? ''
	form.phone = u?.phone ?? ''
	form.address = u?.address ?? ''
	form.city = u?.city ?? ''
	form.state = u?.state ?? ''
	form.zip = u?.zip ?? ''
	form.created_at = u?.created_at ?? ''
	form.updated_at = u?.updated_at ?? ''
}

// Always update form when user data changes (safe even if modal is closed)
watch(
	() => props.user,
	(u) => {
		if (u) {
			syncUserToForm(u)
		}
	},
	{ immediate: true, deep: true }
)

// Also update form when modal opens (in case user data was already loaded before modal was created)
watch(
	() => props.open,
	(isOpen) => {
		if (isOpen && props.user) {
			syncUserToForm(props.user)
		}
	},
	{ immediate: true }
)

// Format dates with time for display
const formattedCreatedAt = computed(() => {
	return form.created_at ? formatDateTime(form.created_at) : 'N/A'
})

const formattedUpdatedAt = computed(() => {
	return form.updated_at ? formatDateTime(form.updated_at) : 'N/A'
})
</script>


