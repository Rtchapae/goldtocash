<template>
	<div
		v-if="show"
		class="modal fade show"
		:class="{ show: show }"
		tabindex="-1"
		role="dialog"
		aria-labelledby="viewOrderDetailsLabel"
		:aria-modal="show"
		:style="{ display: show ? 'block' : 'none', paddingRight: '15px' }"
		@click.self="close"
	>
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="viewOrderDetailsLabel">View Order Details</h5>
					<button
						type="button"
						class="close"
						aria-label="Close"
						@click="close"
					>
						<span aria-hidden="true">×</span>
					</button>
				</div>
				<div class="modal-body" v-if="order">
					<table class="table table-striped">
						<tbody>
							<tr>
								<td>Name</td>
								<td class="user-name">{{ formatName(order) }}</td>
							</tr>
							<tr>
								<td>Email</td>
								<td class="user-login">
									<a :href="`mailto:${order.email}`">{{ order.email }}</a>
								</td>
							</tr>
							<tr>
								<td>Phone</td>
								<td class="user-phone">
									<a :href="`tel:${normalizePhone(order.phone)}`">
										{{ formatPhone(order.phone) }}
									</a>
								</td>
							</tr>
							<tr>
								<td>Address</td>
								<td class="order-address">
									<template v-if="order.address || order.city || order.state || order.zip">
										{{ order.address }}<br v-if="order.address">
										<template v-if="order.city || order.state || order.zip">
											{{ order.city }}<template v-if="order.city && order.state">, </template>{{ order.state }} {{ order.zip }}
										</template>
									</template>
									<template v-else>
										<br>
									</template>
								</td>
							</tr>
							<tr>
								<td>Order Number</td>
								<td class="order-code">{{ order.id }}</td>
							</tr>
							<tr>
								<td>Date Created</td>
								<td class="order-create">{{ formatDateTime(order.created_at) }}</td>
							</tr>
							<tr>
								<td>Amount</td>
								<td class="order-price">{{ formatAmount(order.amount) }}</td>
							</tr>
							<tr>
								<td>Status</td>
								<td class="order-status">
									<template v-if="order">
										<span 
											v-if="order.status || order.status_text" 
											:class="['badge', getStatusBadgeClass(order.status || order.status_text)]"
										>
											{{ order.status || order.status_text || 'Unknown' }}
										</span>
										<span v-else class="text-muted">No status</span>
									</template>
									<span v-else>Unknown</span>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button
						type="button"
						class="btn btn-primary btn-fw"
						@click="close"
					>
						Close
					</button>
				</div>
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
import { formatDateTime } from '@/utils/date'
import { formatName, formatPhone, normalizePhone, formatAmount } from '@/utils/format'
import { getStatusBadgeClass } from '@/utils/orderStatus'

const props = defineProps({
	show: {
		type: Boolean,
		default: false
	},
	order: {
		type: Object,
		default: null
	}
})

const emit = defineEmits(['close'])

const close = () => {
	emit('close')
}
</script>


