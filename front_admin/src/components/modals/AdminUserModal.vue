<template>
	<div
		v-if="show"
		class="modal fade show"
		:class="{ show: show }"
		tabindex="-1"
		role="dialog"
		aria-labelledby="adminUserModalLabel"
		:aria-modal="show"
		:style="{ display: show ? 'block' : 'none', paddingRight: '15px' }"
		@click.self="close"
	>
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="adminUserModalLabel">
						{{ isEdit ? 'Edit Admin User' : 'Create Admin User' }}
					</h5>
					<button
						type="button"
						class="close"
						aria-label="Close"
						@click="close"
					>
						<span aria-hidden="true">×</span>
					</button>
				</div>
				<div class="modal-body">
					<form @submit.prevent="handleSubmit">
						<div class="row">
							<div class="col-md-6 mb-3">
								<label for="email" class="form-label">Email <span class="text-danger">*</span></label>
								<input
									id="email"
									v-model="formData.email"
									type="email"
									class="form-control"
									required
									:disabled="isEdit"
								/>
							</div>

							<div class="col-md-6 mb-3">
								<label for="password" class="form-label">
									Password <span class="text-danger">*</span>
									<span v-if="isEdit" class="text-muted small">(leave empty to keep current)</span>
								</label>
								<input
									id="password"
									v-model="formData.password"
									type="password"
									class="form-control"
									:required="!isEdit"
									minlength="8"
								/>
							</div>

							<div class="col-md-6 mb-3">
								<label for="first_name" class="form-label">First Name</label>
								<input
									id="first_name"
									v-model="formData.first_name"
									type="text"
									class="form-control"
								/>
							</div>

							<div class="col-md-6 mb-3">
								<label for="last_name" class="form-label">Last Name</label>
								<input
									id="last_name"
									v-model="formData.last_name"
									type="text"
									class="form-control"
								/>
							</div>

							<div class="col-md-6 mb-3">
								<label for="name" class="form-label">Full Name</label>
								<input
									id="name"
									v-model="formData.name"
									type="text"
									class="form-control"
								/>
							</div>

							<div class="col-md-6 mb-3">
								<label for="role" class="form-label">Role <span class="text-danger">*</span></label>
								<select
									id="role"
									v-model="formData.role"
									class="form-control"
									required
									@change="handleRoleChange"
								>
									<option value="">Select Role</option>
									<option value="admin">Admin</option>
									<option value="manager">Manager</option>
								</select>
							</div>

							<div v-if="formData.role === 'manager'" class="col-md-6 mb-3">
								<label for="branch_id" class="form-label">Branch <span class="text-danger">*</span></label>
								<select
									id="branch_id"
									v-model="formData.branch_id"
									class="form-control"
									:required="formData.role === 'manager'"
								>
									<option value="">Select Branch</option>
									<option v-for="branch in branches" :key="branch.id" :value="branch.id">
										{{ branch.display_name }}
									</option>
								</select>
							</div>
						</div>

						<div v-if="error" class="alert alert-danger mt-3">
							{{ error }}
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button
						type="button"
						class="btn btn-secondary"
						@click="close"
					>
						Cancel
					</button>
					<button
						type="button"
						class="btn btn-primary"
						:disabled="isSaving"
						@click="handleSubmit"
					>
						{{ isSaving ? 'Saving...' : (isEdit ? 'Update' : 'Create') }}
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
import { ref, watch, onMounted } from 'vue'
import { createAdminUser, updateAdminUser } from '@/api/adminAdminUsers'
import { get } from '@/api/client'

const props = defineProps({
	show: {
		type: Boolean,
		default: false
	},
	user: {
		type: Object,
		default: null
	},
	isEdit: {
		type: Boolean,
		default: false
	}
})

const emit = defineEmits(['close', 'saved'])

const formData = ref({
	email: '',
	password: '',
	first_name: '',
	last_name: '',
	name: '',
	role: '',
	branch_id: null
})

const branches = ref([])
const isSaving = ref(false)
const error = ref('')

const loadBranches = async () => {
	try {
		const response = await get('/admin/branches')
		branches.value = response.data || []
	} catch (err) {
		console.error('Failed to load branches:', err)
		branches.value = []
	}
}

const handleRoleChange = () => {
	if (formData.value.role !== 'manager') {
		formData.value.branch_id = null
	}
}

const resetForm = () => {
	formData.value = {
		email: '',
		password: '',
		first_name: '',
		last_name: '',
		name: '',
		role: '',
		branch_id: null
	}
	error.value = ''
}

const loadUserData = () => {
	if (props.user) {
		formData.value = {
			email: props.user.email || '',
			password: '',
			first_name: props.user.first_name || '',
			last_name: props.user.last_name || '',
			name: props.user.name || '',
			role: props.user.role || '',
			branch_id: props.user.branch_id || null
		}
	} else {
		resetForm()
	}
}

const handleSubmit = async () => {
	error.value = ''

	if (!formData.value.email) {
		error.value = 'Email is required'
		return
	}

	if (!props.isEdit && !formData.value.password) {
		error.value = 'Password is required'
		return
	}

	if (!formData.value.role) {
		error.value = 'Role is required'
		return
	}

	if (formData.value.role === 'manager' && !formData.value.branch_id) {
		error.value = 'Branch is required for manager role'
		return
	}

	isSaving.value = true

	try {
		const data = { ...formData.value }
		
		if (props.isEdit && !data.password) {
			delete data.password
		}

		if (props.isEdit) {
			await updateAdminUser(props.user.id, data)
		} else {
			await createAdminUser(data)
		}

		emit('saved')
	} catch (err) {
		error.value = err.data?.message || err.message || 'Failed to save admin user'
		console.error('Failed to save admin user:', err)
	} finally {
		isSaving.value = false
	}
}

const close = () => {
	resetForm()
	emit('close')
}

watch(() => props.show, (newVal) => {
	if (newVal) {
		loadUserData()
	}
})

watch(() => props.user, () => {
	if (props.show) {
		loadUserData()
	}
})

onMounted(() => {
	loadBranches()
})
</script>

