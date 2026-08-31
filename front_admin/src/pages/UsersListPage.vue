<template>
	<div>
		<NavbarHeader />
		<div class="dashboard-main-body">
			<PageHeader title="User Profiles" :loading="isLoading" />

			<div class="row">
				<div id="admin-main-tables" class="col-lg-12 grid-margin stretch-card">
					<div class="card">
						<div class="card-body">
							<div class="d-flex justify-content-between mb-3">
								<div class="name-search-wrapper flex-grow-1" style="max-width: 480px;">
									<form class="d-flex" @submit.prevent="handleSearchSubmit">
										<input
											v-model="searchQuery"
											type="text"
											class="form-control flex-grow-1"
											placeholder="Search by name, email, or phone..."
											@input="debouncedSearch"
										/>
										<button
											v-if="searchQuery"
											type="button"
											class="btn btn-sm btn-outline-secondary ms-2"
											@click="clearSearch"
										>
											Clear
										</button>
									</form>
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
								table-class="table table-striped table-hover users-table"
								:rows="users"
								:columns="userColumns"
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
								<template #cell-source="{ row }">
									<span
										v-for="badge in (row.source_badges || [])"
										:key="badge.key + String(badge.value)"
										class="badge bg-secondary text-white text-monospace px-2 py-1 fw-normal me-1"
									>
										{{ badge.value }}
									</span>
								</template>

								<template #cell-name="{ row }">
									{{ formatName(row) }}
								</template>

								<template #cell-email="{ row }">
									<a :href="`mailto:${row.email}`">
										{{ row.email }}
									</a>
								</template>

								<template #cell-phone="{ row }">
									<a :href="`tel:${normalizePhone(row.phone)}`">
										{{ formatPhone(row.phone) }}
									</a>
								</template>

								<template #cell-orders_summary="{ row }">
									{{ row.orders_summary || '0 Active / 0 Closed' }}
								</template>

								<template #cell-created_at="{ row }">
									{{ formatDateTime(row.created_at) }}
								</template>

								<template #cell-updated_at="{ row }">
									{{ formatDateTime(row.updated_at) }}
								</template>

								<template #cell-actions="{ row }">
								<div class="d-flex align-items-center action-buttons">
										<button
											type="button"
											class="btn btn-sm btn-soft-primary"
											title="View"
											@click="openViewModal(row)"
										>
											<iconify-icon icon="solar:eye-outline" class="icon" />
										</button>
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
											class="btn btn-sm btn-soft-info"
											title="Files"
											@click="openFilesModal(row)"
										>
											<iconify-icon icon="solar:document-text-outline" class="icon" />
										</button>
										<button
											type="button"
											class="btn btn-sm btn-soft-secondary"
											title="History"
											@click="openHistoryModal(row)"
										>
											<iconify-icon icon="solar:history-outline" class="icon" />
										</button>
									</div>
								</template>

								<template #cell-notes="{ row }">
									<a
										href="#"
										class="text-primary-600 fw-medium"
										@click.prevent="openNotes(row)"
									>
										Notes
									</a>
								</template>
							</UITable>
						</div>
					</div>
				</div>
			</div>
		</div>

		<ViewUserProfileModal
			:show="showViewModal"
			:user="selectedUser"
			@close="closeViewModal"
		/>

		<EditUserProfileModal
			:show="showEditModal"
			:user="selectedUser"
			@close="closeEditModal"
			@save="handleEditSave"
		/>

		<ViewUserFilesModal
			:show="showFilesModal"
			:files="selectedUserFiles"
			@close="closeFilesModal"
		/>

		<ViewUserHistoryModal
			:show="showHistoryModal"
			:history="selectedUserHistory"
			@close="closeHistoryModal"
		/>

		<UserNotesModal
			:show="showNotesModal"
			:user="selectedUser"
			:notes="selectedUserNotes"
			@close="closeNotesModal"
			@save="handleSaveNote"
		/>
	</div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import NavbarHeader from '../components/NavbarHeader.vue'
import PageHeader from '../components/layout/PageHeader.vue'
import UITable from '../components/ui/UITable.vue'
import ViewUserProfileModal from '../components/modals/ViewUserProfileModal.vue'
import EditUserProfileModal from '../components/modals/EditUserProfileModal.vue'
import ViewUserFilesModal from '../components/modals/ViewUserFilesModal.vue'
import ViewUserHistoryModal from '../components/modals/ViewUserHistoryModal.vue'
import UserNotesModal from '../components/modals/UserNotesModal.vue'
import { fetchAdminUsers, getAdminUserById } from '../api/adminUsers'
import { getUserNotes, saveUserNote } from '../api/adminUserNotes'
import { formatName, formatPhone, normalizePhone } from '@/utils/format'
import { SEARCH_DEBOUNCE_DELAY } from '@/config/orders'

const users = ref([])
const searchQuery = ref('')
const isLoading = ref(false)

const currentPage = ref(1)
const perPage = ref(15)
const totalItems = ref(0)
const lastPage = ref(1)
const showViewModal = ref(false)
const showEditModal = ref(false)
const showFilesModal = ref(false)
const showHistoryModal = ref(false)
const showNotesModal = ref(false)
const selectedUser = ref(null)
const selectedUserFiles = ref([])
const selectedUserHistory = ref([])
const selectedUserNotes = ref([])

let searchDebounceTimer = null

const debouncedSearch = () => {
	if (searchDebounceTimer) {
		clearTimeout(searchDebounceTimer)
	}
	searchDebounceTimer = setTimeout(() => {
		currentPage.value = 1
		loadUsers(1)
	}, SEARCH_DEBOUNCE_DELAY)
}

const clearSearch = () => {
	searchQuery.value = ''
	currentPage.value = 1
	loadUsers(1)
}

const handleSearchSubmit = () => {
	if (searchDebounceTimer) {
		clearTimeout(searchDebounceTimer)
	}
	currentPage.value = 1
	loadUsers(1)
}

const userColumns = [
	{ key: 'id', label: '#', sortable: true },
	{ key: 'source', label: 'Source', sortable: false },
	{ key: 'name', label: 'Name', sortable: true },
	{ key: 'email', label: 'Email', sortable: true },
	{ key: 'phone', label: 'Phone', sortable: true },
	{ key: 'orders_summary', label: 'Orders', sortable: false },
	{ key: 'created_at', label: 'Date Created', sortable: true },
    { key: 'updated_at', label: 'Last Update', sortable: true },
	{ key: 'actions', label: 'Action', sortable: false },
	{ key: 'notes', label: '', sortable: false }
]

const loadUsers = async (page = null) => {
	isLoading.value = true
	try {
		const pageToLoad = page !== null ? page : currentPage.value

		const body = await fetchAdminUsers({
			page: pageToLoad,
			per_page: perPage.value,
			nameQuery: searchQuery.value || undefined
		})

		if (body.meta) {
			currentPage.value = body.meta.current_page || 1
			perPage.value = body.meta.per_page || 15
			totalItems.value = body.meta.total || 0
			lastPage.value = body.meta.last_page || 1
		}

		const raw = Array.isArray(body.data) ? body.data : []

		users.value = raw.map((u) => {
			const rawBadges = u.source_badges || u.source || {}
			let normalizedBadges = []

			if (Array.isArray(rawBadges)) {
				normalizedBadges = rawBadges
			} else if (rawBadges && typeof rawBadges === 'object') {
				normalizedBadges = Object.entries(rawBadges).map(([key, value]) => ({
					key,
					value
				}))
			}

			return {
				...u,
				source_badges: normalizedBadges,
				orders_summary: u.orders_summary || null
			}
		})
	} catch (e) {
		// eslint-disable-next-line no-console
		console.error('Error loading users', e)
	} finally {
		isLoading.value = false
	}
}

const handlePageChange = (page) => {
	loadUsers(page)
}

const handlePerPageChange = (newPerPage) => {
	perPage.value = newPerPage
	currentPage.value = 1
	loadUsers(1)
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

const openViewModal = async (row) => {
	showViewModal.value = true
	selectedUser.value = row
	try {
		const res = await getAdminUserById(row.id)
		selectedUser.value = res.data ?? res
	} catch (e) {
		console.error('Failed to load user', e)
	}
}

const closeViewModal = () => {
	showViewModal.value = false
	selectedUser.value = null
}

const openEditModal = async (row) => {
	showEditModal.value = true
	selectedUser.value = row
	try {
		const res = await getAdminUserById(row.id)
		selectedUser.value = res.data ?? res
	} catch (e) {
		console.error('Failed to load user', e)
	}
}

const closeEditModal = () => {
	showEditModal.value = false
}

const handleEditSave = (updatedUser) => {
	// eslint-disable-next-line no-console
	console.log('Save user', updatedUser)
	showEditModal.value = false
}

const openFilesModal = (row) => {
	selectedUser.value = row
	selectedUserFiles.value = []
	showFilesModal.value = true
}

const closeFilesModal = () => {
	showFilesModal.value = false
	selectedUserFiles.value = []
}

const openHistoryModal = (row) => {
	selectedUser.value = row
	selectedUserHistory.value = []
	showHistoryModal.value = true
}

const closeHistoryModal = () => {
	showHistoryModal.value = false
	selectedUserHistory.value = []
}

const openNotes = async (row) => {
	selectedUser.value = row
	showNotesModal.value = true
	
	try {
		const response = await getUserNotes(row.id)
		if (response.data && response.data.notes) {
			selectedUserNotes.value = response.data.notes
		} else {
			selectedUserNotes.value = []
		}
	} catch (error) {
		console.error('Failed to load user notes:', error)
		selectedUserNotes.value = []
	}
}

const closeNotesModal = () => {
	showNotesModal.value = false
	selectedUserNotes.value = []
}

const handleSaveNote = async (data) => {
	if (!data.user?.id || !data.text) {
		console.error('Invalid note data:', data)
		return
	}

	try {
		await saveUserNote({
			userId: data.user.id,
			text: data.text,
		})

		const response = await getUserNotes(data.user.id)
		if (response.data && response.data.notes) {
			selectedUserNotes.value = response.data.notes
		}
	} catch (error) {
		console.error('Failed to save note:', error)
	}
}

onMounted(() => {
	loadUsers()
})
</script>

<style scoped>
.users-table {
	font-size: 0.9rem;
}

.action-buttons button {
	margin-right: 2px;
}

.action-buttons button:last-child {
	margin-right: 0;
}
</style>
