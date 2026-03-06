<template>
	<div>
		<NavbarHeader />
		<div class="dashboard-main-body">
			<PageHeader title="Admin Users" :loading="isLoading" />

			<div class="row">
				<div id="admin-main-tables" class="col-lg-12 grid-margin stretch-card">
					<div class="card">
						<div class="card-body">
							<div class="d-flex justify-content-between align-items-center mb-3">
								<h5 class="mb-0">Admin Users</h5>
								<button
									type="button"
									class="btn btn-primary"
									@click="openCreateModal"
								>
									<iconify-icon icon="solar:add-circle-outline" class="icon me-2" />
									Add User
								</button>
							</div>
							<div class="d-flex justify-content-between mb-3">
								<div class="d-flex gap-3 align-items-center">
									<select v-model="roleFilter" class="form-control" style="width: auto;" @change="loadAdminUsers">
										<option value="all">All Roles</option>
										<option value="admin">Admins</option>
										<option value="manager">Managers</option>
									</select>

									<div class="name-search-wrapper">
										<input
											v-model="searchQuery"
											type="text"
											class="form-control"
											placeholder="Search by name or email..."
											@input="debouncedSearch"
										/>
									</div>
								</div>
							</div>
							<hr />

							<UITable
								:show-title="false"
								:show-index="false"
								:show-length-control="true"
								:show-search-control="false"
								:show-pagination="true"
								:show-info="true"
								table-class="table table-striped table-hover admin-users-table"
								:rows="adminUsers"
								:columns="adminUserColumns"
								:server-pagination="true"
								:current-page="currentPage"
								:total-items="totalItems"
								:per-page="perPage"
								:last-page="lastPage"
								:page-size-options="[10, 15, 25, 50, 100]"
								:loading="isLoading"
								@page-change="handlePageChange"
								@per-page-change="handlePerPageChange"
							>
								<template #cell-role="{ row }">
									<span :class="['badge', getRoleBadgeClass(row.role)]">
										{{ row.role_display || row.role }}
									</span>
								</template>

								<template #cell-branch_name="{ row }">
									{{ row.branch_name || '-' }}
								</template>

								<template #cell-created_at="{ row }">
									{{ formatDateTime(row.created_at) }}
								</template>

								<template #cell-actions="{ row }">
									<div class="d-flex align-items-center action-buttons">
										<button
											type="button"
											class="btn btn-sm btn-soft-warning"
											title="Edit"
											@click="openEditModal(row)"
										>
											<iconify-icon icon="solar:settings-outline" class="icon" />
										</button>
										<button
											type="button"
											class="btn btn-sm btn-soft-danger"
											title="Delete"
											@click="confirmDelete(row)"
										>
											<iconify-icon icon="solar:trash-bin-outline" class="icon" />
										</button>
									</div>
								</template>
							</UITable>
						</div>
					</div>
				</div>
			</div>
		</div>

		<AdminUserModal
			:show="showModal"
			:user="selectedUser"
			:is-edit="isEditMode"
			@close="closeModal"
			@saved="handleSaved"
		/>
	</div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { fetchAdminUsers, deleteAdminUser } from '@/api/adminAdminUsers'
import { formatDateTime } from '@/utils/date'
import { debounce } from '@/utils/debounce'
import { useToast } from '@/composables/useToast'
import UITable from '@/components/ui/UITable.vue'
import NavbarHeader from '@/components/NavbarHeader.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import AdminUserModal from '@/components/modals/AdminUserModal.vue'

const isLoading = ref(false)
const adminUsers = ref([])
const currentPage = ref(1)
const perPage = ref(15)
const totalItems = ref(0)
const lastPage = ref(1)
const searchQuery = ref('')
const roleFilter = ref('all')
const showModal = ref(false)
const selectedUser = ref(null)
const isEditMode = ref(false)

const adminUserColumns = [
	{ key: 'id', label: 'ID', sortable: false },
	{ key: 'name', label: 'Name', sortable: false },
	{ key: 'email', label: 'Email', sortable: false },
	{ key: 'role', label: 'Role', sortable: false },
	{ key: 'branch_name', label: 'Branch', sortable: false },
	{ key: 'created_at', label: 'Created At', sortable: false },
	{ key: 'actions', label: 'Actions', sortable: false }
]

const loadAdminUsers = async () => {
	isLoading.value = true
	try {
		const response = await fetchAdminUsers({
			page: currentPage.value,
			per_page: perPage.value,
			search: searchQuery.value || undefined,
			role: roleFilter.value !== 'all' ? roleFilter.value : undefined
		})

		adminUsers.value = response.data || []
		currentPage.value = response.meta?.current_page || 1
		perPage.value = response.meta?.per_page || 15
		totalItems.value = response.meta?.total || 0
		lastPage.value = response.meta?.last_page || 1
	} catch (error) {
		console.error('Failed to load admin users:', error)
		adminUsers.value = []
	} finally {
		isLoading.value = false
	}
}

const debouncedSearch = debounce(() => {
	currentPage.value = 1
	loadAdminUsers()
}, 500)

const handlePageChange = (page) => {
	currentPage.value = page
	loadAdminUsers()
}

const handlePerPageChange = (newPerPage) => {
	perPage.value = newPerPage
	currentPage.value = 1
	loadAdminUsers()
}

const getRoleBadgeClass = (role) => {
	if (role === 'admin') return 'bg-danger'
	if (role === 'manager') return 'bg-warning'
	return 'bg-secondary'
}

const openCreateModal = () => {
	selectedUser.value = null
	isEditMode.value = false
	showModal.value = true
}

const openEditModal = (user) => {
	selectedUser.value = user
	isEditMode.value = true
	showModal.value = true
}

const closeModal = () => {
	showModal.value = false
	selectedUser.value = null
	isEditMode.value = false
}

const handleSaved = () => {
	closeModal()
	loadAdminUsers()
}

const toast = useToast()

const confirmDelete = async (user) => {
	if (!confirm(`Are you sure you want to delete ${user.email}?`)) {
		return
	}

	try {
		await deleteAdminUser(user.id)
		loadAdminUsers()
		toast.success(`Admin user ${user.email} deleted successfully`)
	} catch (error) {
		console.error('Failed to delete admin user:', error)
		toast.error('Failed to delete admin user: ' + (error.message || 'Unknown error'))
	}
}

onMounted(() => {
	loadAdminUsers()
})
</script>

