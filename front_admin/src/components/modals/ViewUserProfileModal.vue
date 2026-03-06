<template>
	<div
		v-if="show"
		class="modal fade show"
		:class="{ show: show }"
		tabindex="-1"
		role="dialog"
		aria-labelledby="viewUserProfileLabel"
		:aria-modal="show"
		:style="{ display: show ? 'block' : 'none', paddingRight: '15px' }"
		@click.self="close"
	>
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="viewUserProfileLabel">View User Profile</h5>
					<button
						type="button"
						class="close"
						aria-label="Close"
						@click="close"
					>
						<span aria-hidden="true">×</span>
					</button>
				</div>
				<div class="modal-body" v-if="user">
					<table class="table table-striped">
						<tbody>
							<tr>
								<td>User #</td>
								<td class="user-code">{{ user.id }}</td>
							</tr>
							<tr>
								<td>Name</td>
								<td class="user-name">{{ formatName(user) }}</td>
							</tr>
							<tr>
								<td>Email</td>
								<td class="user-login">
									<a :href="`mailto:${user.email}`">{{ user.email }}</a>
								</td>
							</tr>
							<tr>
								<td>Phone</td>
								<td class="user-phone">
									<a :href="`tel:${normalizePhone(user.phone)}`">
										{{ formatPhone(user.phone) }}
									</a>
								</td>
							</tr>
							<tr>
								<td>Orders</td>
								<td class="user-orders">
									{{ user.orders_summary || '0 Active / 0 Closed' }}
								</td>
							</tr>
							<tr>
								<td>Address</td>
								<td class="user-address">
									<template v-if="user.address || user.city || user.state || user.zip">
										{{ user.address }}<br v-if="user.address">
										<template v-if="user.city || user.state || user.zip">
											{{ user.city }}<template v-if="user.city && user.state">, </template>{{ user.state }} {{ user.zip }}
										</template>
									</template>
									<template v-else>
										<br>
									</template>
								</td>
							</tr>
							<tr>
								<td>Payment Method</td>
								<td class="user-bank">
									{{ user.payment_method || '' }}<br>
								</td>
							</tr>
							<tr>
								<td>Government ID</td>
								<td class="user-docs">
									{{ user.government_id || '' }}<br>
								</td>
							</tr>
							<tr>
								<td>Date of Birth</td>
								<td class="user-date">
									{{ formatDate(user.date_of_birth) }}
								</td>
							</tr>
							<tr>
								<td>Date Created</td>
								<td class="user-create">
									{{ formatDateTime(user.created_at) }}
								</td>
							</tr>
							<tr>
								<td>Last Update</td>
								<td class="user-update">
									{{ formatDateTime(user.updated_at) }}
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
import { computed } from 'vue'
import { formatName, formatPhone, normalizePhone } from '@/utils/format'

const props = defineProps({
	show: {
		type: Boolean,
		default: false
	},
	user: {
		type: Object,
		default: null
	}
})

const emit = defineEmits(['close'])

const close = () => {
	emit('close')
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

const formatDate = (date) => {
	if (!date) return ''
	
	const dateObj = new Date(date)
	if (isNaN(dateObj.getTime())) return date
	
	const year = dateObj.getFullYear()
	const month = String(dateObj.getMonth() + 1).padStart(2, '0')
	const day = String(dateObj.getDate()).padStart(2, '0')
	
	return `${year}-${month}-${day}`
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




