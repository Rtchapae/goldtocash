<template>
	<div
		v-if="show"
		class="modal fade show"
		tabindex="-1"
		role="dialog"
		aria-labelledby="editOrderDetailsLabel"
		:aria-modal="show"
		:style="{ display: show ? 'block' : 'none', paddingRight: '15px' }"
		@click.self="close"
	>
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form @submit.prevent="handleSubmit">
					<div class="modal-header">
						<h5 class="modal-title" id="editOrderDetailsLabel">Edit Order Details</h5>
						<button
							type="button"
							class="close"
							aria-label="Close"
							@click="close"
						>
							<span aria-hidden="true">×</span>
						</button>
					</div>
					<div class="modal-body" v-if="form">
						<table class="table table-striped">
							<tbody>
								<tr>
									<td>Name</td>
									<td class="user-name">
										<input
											type="text"
											class="form-control"
											:value="formatName(order)"
											disabled
										>
									</td>
								</tr>
								<tr>
									<td>Email</td>
									<td class="user-login">
										<input
											type="text"
											class="form-control"
											:value="order?.email || ''"
											disabled
										>
									</td>
								</tr>
								<tr>
									<td>Phone</td>
									<td class="user-phone">
										<input
											type="text"
											class="form-control"
											:value="order?.phone || ''"
											disabled
										>
									</td>
								</tr>
								<tr>
									<td>Address Street</td>
									<td class="order-street">
										<input
											v-model="form.address"
											type="text"
											class="form-control"
										>
									</td>
								</tr>
								<tr>
									<td>Address City</td>
									<td class="order-city">
										<input
											v-model="form.city"
											type="text"
											class="form-control"
										>
									</td>
								</tr>
								<tr>
									<td>Address State</td>
									<td class="order-state">
										<select
											v-model="form.state"
											class="form-control"
										>
											<option value="" disabled hidden></option>
											<option
												v-for="state in states"
												:key="state.code"
												:value="state.code"
											>
												{{ state.name }}
											</option>
										</select>
									</td>
								</tr>
								<tr>
									<td>Address Zip</td>
									<td class="order-zip">
										<input
											v-model="form.zip"
											type="text"
											class="form-control"
										>
									</td>
								</tr>
								<tr>
									<td>Order Number</td>
									<td class="order-code">
										<input
											type="text"
											class="form-control"
											:value="form.id"
											disabled
										>
									</td>
								</tr>
								<tr>
									<td>Date Created</td>
									<td class="order-create">
										<input
											type="text"
											class="form-control"
											:value="formatDateTime(form.created_at)"
											disabled
										>
									</td>
								</tr>
								<tr>
									<td>Amount</td>
									<td class="order-price">
										<input
											v-model="form.amount"
											type="number"
											step="any"
											min="0"
											class="form-control"
										>
									</td>
								</tr>
								<tr>
									<td>Status</td>
									<td class="order-status">
										<select
											v-model="form.status"
											class="form-control"
										>
											<option
												v-for="status in orderStatuses"
												:key="status.value"
												:value="status.value"
											>
												{{ status.label }}
											</option>
										</select>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="modal-footer">
						<button
							type="button"
							class="btn btn-secondary btn-fw"
							@click="close"
							:disabled="loading"
						>
							Cancel
						</button>
						<button
							type="submit"
							class="btn btn-primary btn-fw"
							:disabled="loading"
						>
							<span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
							{{ loading ? 'Updating...' : 'Update' }}
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div
		v-if="show"
		class="modal-backdrop fade show"
		@click="close"
	></div>
</template>

<script setup>
import { reactive, watch } from 'vue'
import { formatName } from '@/utils/format'
import { US_STATES_CODE_NAME } from '@/constants/states'
import { ORDER_STATUSES } from '@/constants/orderStatuses'

const props = defineProps({
	show: {
		type: Boolean,
		default: false
	},
	order: {
		type: Object,
		default: null
	},
	loading: {
		type: Boolean,
		default: false
	}
})

const emit = defineEmits(['close', 'save'])

const form = reactive({
	id: null,
	address: '',
	city: '',
	state: '',
	zip: '',
	amount: '',
	status: 0,
	created_at: null
})

const states = US_STATES_CODE_NAME
const orderStatuses = ORDER_STATUSES

function parseAmount(value) {
	if (value == null || value === '') return ''
	if (typeof value === 'number' && !Number.isNaN(value)) return value
	const str = String(value).replace(/,/g, '')
	const num = parseFloat(str)
	return Number.isNaN(num) ? '' : num
}

watch(
	() => props.order,
	(order) => {
		if (!order) return
		form.id = order.id ?? null
		form.address = order.address ?? ''
		form.city = order.city ?? ''
		form.state = order.state ?? ''
		form.zip = order.zip ?? ''
		form.amount = order.amount_raw != null ? order.amount_raw : parseAmount(order.amount)
		form.status = order.status_value != null ? order.status_value : (order.status ?? 0)
		form.created_at = order.created_at ?? null
	},
	{ immediate: true }
)

const close = () => {
	emit('close')
}

const handleSubmit = () => {
	const payload = { ...form }
	if (payload.amount === '' || payload.amount == null) {
		payload.amount = null
	} else {
		payload.amount = Number(payload.amount)
	}
	emit('save', payload)
}


const formatDateTime = (dateTime) => {
	if (!dateTime) return ''

	if (typeof dateTime === 'string' && /^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/.test(dateTime)) {
		return dateTime
	}

	const date = new Date(dateTime)
	if (isNaN(date.getTime())) return dateTime

	const year = date.getFullYear()
	const month = String(date.getMonth() + 1).padStart(2, '0')
	const day = String(date.getDate()).padStart(2, '0')
	const hours = String(date.getHours()).padStart(2, '0')
	const minutes = String(date.getMinutes()).padStart(2, '0')
	const seconds = String(date.getSeconds()).padStart(2, '0')

	return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`
}
</script>

<style scoped>
.modal-backdrop {
	position: fixed;
	top: 0;
	left: 0;
	z-index: 1040;
	width: 100vw;
	height: 100vh;
	background-color: #000;
	opacity: 0.5;
}

.modal.show {
	display: block;
}
</style>


