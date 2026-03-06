<template>
	<BaseModal id="editOrderDetails" title="Edit Order Details" @close="$emit('close')">
		<form @submit.prevent="$emit('submit', form)">
			<input id="modal-hidden-user-code" name="modal-user-code" type="hidden" :value="form.userId || ''">
			<input id="modal-hidden-order-code" name="modal-order-code" type="hidden" :value="form.orderId || ''">

			<table class="table table-striped">
				<tbody>
					<tr><td>Name</td><td class="user-name"><input name="modal-user-name" type="text" class="form-control" :value="form.userName" disabled></td></tr>
					<tr><td>Email</td><td class="user-login"><input name="modal-user-login" type="text" class="form-control" :value="form.email" disabled></td></tr>
					<tr><td>Phone</td><td class="user-phone"><input name="modal-user-phone" type="text" class="form-control" :value="form.phone" disabled></td></tr>
					<tr><td>Address Street</td><td class="order-street"><input v-model="form.street" name="modal-order-street" type="text" class="form-control"></td></tr>
					<tr><td>Address City</td><td class="order-city"><input v-model="form.city" name="modal-order-city" type="text" class="form-control"></td></tr>
					<tr>
						<td>Address State</td>
						<td class="order-state">
							<select v-model="form.state" name="modal-order-state" class="form-control">
								<option value="" disabled selected hidden></option>
								<option v-for="s in US_STATES" :key="s.value" :value="s.value">{{ s.label }}</option>
							</select>
						</td>
					</tr>
					<tr><td>Address Zip</td><td class="order-zip"><input v-model="form.zip" name="modal-order-zip" type="number" class="form-control"></td></tr>
					<tr><td>Order Number</td><td class="order-code"><input name="modal-order-code" type="text" class="form-control" :value="form.orderId" disabled></td></tr>
					<tr><td>Date Created</td><td class="order-create"><input name="modal-order-create" type="text" class="form-control" :value="form.createdAt" disabled></td></tr>
					<tr><td>Amount</td><td class="order-price"><input name="modal-order-price" type="number" class="form-control" :value="form.amount" disabled></td></tr>
					<tr>
						<td>Status</td>
						<td class="order-status">
							<select v-model="form.status" name="modal-order-status" class="form-control" disabled>
								<option v-for="o in statusOptions" :key="o.value" :value="o.value">{{ o.label }}</option>
							</select>
						</td>
					</tr>
				</tbody>
			</table>
		</form>

		<template #footer>
			<button type="button" class="btn btn-secondary btn-fw" @click="$emit('close')">Cancel</button>
			<button type="button" class="btn btn-primary btn-fw" @click="$emit('submit', form)">Update</button>
		</template>
	</BaseModal>
</template>

<script setup>
import { computed, reactive, watch } from 'vue'
import BaseModal from './BaseModal.vue'
import { US_STATES } from '@/constants/states'

const props = defineProps({
	order: { type: Object, default: null },
})

defineEmits(['close', 'submit'])

const statusOptions = computed(() => ([
	{ value: 0, label: 'Kit Requested' },
	{ value: 1, label: 'In Transit' },
	{ value: 2, label: 'Items Received' },
	{ value: 3, label: 'Appraisal' },
	{ value: 4, label: '- Offer Sent' },
	{ value: 5, label: '- - Offer Accepted' },
	{ value: 6, label: '- - - Paid' },
	{ value: 7, label: '- - Offer Denied' },
	{ value: 8, label: '- - - Items Sent Back' },
	{ value: 9, label: '- No Sale' },
	{ value: 10, label: '- - Items Sent Back' },
]))

const form = reactive({
	userId: '',
	userName: '',
	email: '',
	phone: '',
	street: '',
	city: '',
	state: '',
	zip: '',
	orderId: '',
	createdAt: '',
	amount: '',
	status: 0,
})

watch(
	() => props.order,
	(o) => {
		form.userId = o?.userId ?? ''
		form.userName = o?.userName ?? ''
		form.email = o?.email ?? ''
		form.phone = o?.phone ?? ''
		form.street = o?.street ?? ''
		form.city = o?.city ?? ''
		form.state = o?.state ?? ''
		form.zip = o?.zip ?? ''
		form.orderId = o?.id ?? ''
		form.createdAt = o?.createdAt ?? ''
		form.amount = o?.amount ?? ''
		form.status = Number.isFinite(Number(o?.status)) ? Number(o?.status) : 0
	},
	{ immediate: true }
)
</script>


