<template>
	<BaseModal id="editBankSettings" title="Edit Payment Method" :open="open" @close="$emit('close')">
		<form @submit.prevent="$emit('submit', form)">
			<table class="table table-striped">
				<tbody>
					<tr>
						<td>Payment Method</td>
						<td>
							<select v-model="form.type" id="verification-bank-type" name="verification-bank-type" class="form-control">
								<option value="check">Check</option>
								<option value="ach">Direct Deposit</option>
								<option value="paypal">PayPal</option>
								<option value="cashapp">CashApp</option>
							</select>
						</td>
					</tr>

					<tr v-if="form.type === 'check'">
						<td>Check Name</td>
						<td><input v-model="form.checkName" id="verification-bank-check-name" name="verification-bank-check-name" type="text" class="form-control"></td>
					</tr>
					<tr v-if="form.type === 'check'">
						<td>Check Address</td>
						<td><input v-model="form.checkAddress" id="verification-bank-check-address" name="verification-bank-check-address" type="text" class="form-control"></td>
					</tr>

					<tr v-if="form.type === 'ach'">
						<td>Routing Number</td>
						<td><input v-model="form.achRouting" id="verification-bank-ach-routing" name="verification-bank-ach-routing" type="text" class="form-control"></td>
					</tr>
					<tr v-if="form.type === 'ach'">
						<td>Account Number</td>
						<td><input v-model="form.achAccount" id="verification-bank-ach-account" name="verification-bank-ach-account" type="text" class="form-control"></td>
					</tr>

					<tr v-if="form.type === 'paypal'">
						<td>PayPal Email</td>
						<td><input v-model="form.paypalEmail" id="verification-bank-paypal-email" name="verification-bank-paypal-email" type="text" class="form-control"></td>
					</tr>

					<tr v-if="form.type === 'cashapp'">
						<td>CashTag</td>
						<td><input v-model="form.cashTag" id="verification-bank-cashapp-cashtag" name="verification-bank-cashapp-cashtag" type="text" class="form-control"></td>
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
	type: 'check',
	checkName: '',
	checkAddress: '',
	achRouting: '',
	achAccount: '',
	paypalEmail: '',
	cashTag: '',
})

// Helper to sync initial data to form
const syncInitialToForm = (v) => {
	if (!v) return
	form.type = v?.type ?? 'check'
	form.checkName = v?.checkName ?? ''
	form.checkAddress = v?.checkAddress ?? ''
	form.achRouting = v?.achRouting ?? ''
	form.achAccount = v?.achAccount ?? ''
	form.paypalEmail = v?.paypalEmail ?? ''
	form.cashTag = v?.cashTag ?? ''
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


