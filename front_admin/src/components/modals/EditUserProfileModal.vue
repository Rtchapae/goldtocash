<template>
	<div
		v-if="show"
		class="modal fade show"
		tabindex="-1"
		role="dialog"
		aria-labelledby="editUserProfileLabel"
		:aria-modal="show"
		:style="{ display: show ? 'block' : 'none', paddingRight: '15px' }"
		@click.self="close"
	>
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<form @submit.prevent="handleSubmit">
					<div class="modal-header">
						<h5 class="modal-title" id="editUserProfileLabel">Edit User Profile</h5>
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
									<td>User #</td>
									<td class="user-code">
										<input
											type="text"
											class="form-control"
											:value="form.id"
											disabled
										>
									</td>
								</tr>
								<tr>
									<td>First Name</td>
									<td class="user-name">
										<input
											v-model="form.first_name"
											type="text"
											class="form-control"
										>
									</td>
								</tr>
								<tr>
									<td>Last Name</td>
									<td class="user-sname">
										<input
											v-model="form.last_name"
											type="text"
											class="form-control"
										>
									</td>
								</tr>
								<tr>
									<td>Email</td>
									<td class="user-login">
										<input
											v-model="form.email"
											type="email"
											class="form-control"
										>
									</td>
								</tr>
								<tr>
									<td>Phone</td>
									<td class="user-phone">
										<input
											v-model="form.phone"
											type="tel"
											class="form-control"
										>
									</td>
								</tr>
								<tr>
									<td>Orders</td>
									<td class="user-orders">
										<input
											type="text"
											class="form-control"
											:value="form.orders_summary || '0 Active / 0 Closed'"
											disabled
										>
									</td>
								</tr>
								<tr>
									<td>Street</td>
									<td class="user-street">
										<input
											v-model="form.address"
											type="text"
											class="form-control"
										>
									</td>
								</tr>
								<tr>
									<td>City</td>
									<td class="user-city">
										<input
											v-model="form.city"
											type="text"
											class="form-control"
										>
									</td>
								</tr>
								<tr>
									<td>State</td>
									<td class="user-state">
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
									<td>Zip</td>
									<td class="user-zip">
										<input
											v-model="form.zip"
											type="text"
											class="form-control"
										>
									</td>
								</tr>
								<tr>
									<td>Date Created</td>
									<td class="user-create">
										<input
											type="text"
											class="form-control"
											:value="formatDateTime(form.created_at)"
											disabled
										>
									</td>
								</tr>
								<tr>
									<td>Last Update</td>
									<td class="user-update">
										<input
											type="text"
											class="form-control"
											:value="formatDateTime(form.updated_at)"
											disabled
										>
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
						>
							Cancel
						</button>
						<button
							type="submit"
							class="btn btn-primary btn-fw"
						>
							Update
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
import { US_STATES_CODE_NAME } from '@/constants/states'

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

const emit = defineEmits(['close', 'save'])

const form = reactive({
	id: null,
	first_name: '',
	last_name: '',
	email: '',
	phone: '',
	address: '',
	city: '',
	state: '',
	zip: '',
	orders_summary: '',
	created_at: null,
	updated_at: null
})

const states = US_STATES_CODE_NAME

watch(
	() => props.user,
	(user) => {
		if (!user) return
		form.id = user.id ?? null
		form.first_name = user.first_name ?? ''
		form.last_name = user.last_name ?? ''
		form.email = user.email ?? ''
		form.phone = user.phone ?? ''
		form.address = user.address ?? ''
		form.city = user.city ?? ''
		form.state = user.state ?? ''
		form.zip = user.zip ?? ''
		form.orders_summary = user.orders_summary ?? ''
		form.created_at = user.created_at ?? null
		form.updated_at = user.updated_at ?? null
	},
	{ immediate: true }
)

const close = () => {
	emit('close')
}

const handleSubmit = () => {
	emit('save', { ...form })
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
	const hours = String(date.getHours() + 1).padStart(2, '0')
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





