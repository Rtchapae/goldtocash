<template>
	<div>
		<NavbarHeader />
		<div class="dashboard-main-body">
			<PageHeader title="SEO Pages" :loading="isLoading" />

			<div class="row">
				<div id="admin-main-tables" class="col-lg-12 grid-margin stretch-card">
					<div class="card">
						<div class="card-body orders-card-body">
							<div class="d-flex justify-content-between mb-3">
								<div class="name-search-wrapper">
									<form @submit.prevent="handleNameSearch" class="d-flex">
										<input
											v-model="searchQuery"
											type="text"
											class="form-control flex-grow-1"
											placeholder="Search SEO pages..."
											@input="debouncedSearch"
										/>
										<button v-if="searchQuery" type="button" class="btn btn-sm btn-outline-secondary ms-2" @click="clearSearch">
											Clear
										</button>
									</form>
								</div>
								<button
									type="button"
									class="btn btn-primary"
									@click="openCreateModal"
								>
									<iconify-icon icon="solar:add-circle-outline" class="me-1" />
									Add SEO Page
								</button>
							</div>
							<hr />

							<UITable
								:show-title="false"
								:show-index="false"
								:show-length-control="true"
								:show-search-control="false"
								:show-pagination="true"
								:show-info="true"
								table-class="table table-striped table-hover seo-pages-table"
								:rows="seoPages"
								:columns="seoPageColumns"
								:server-pagination="true"
								:current-page="currentPage"
								:total-items="totalItems"
								:per-page="perPage"
								:last-page="lastPage"
								:page-size-options="PAGE_SIZE_OPTIONS"
								:sortable="false"
								:loading="isLoading"
								@page-change="handlePageChange"
								@per-page-change="handlePerPageChange"
							>
								<template #cell-page_title="{ row }">
									<div>
										<strong>{{ row.page_title }}</strong>
										<br />
										<small class="text-muted">{{ row.page_url }}</small>
									</div>
								</template>

								<template #cell-meta_title="{ row }">
									<div v-if="row.meta_title" class="text-truncate" :title="row.meta_title" style="max-width: 300px;">
										{{ row.meta_title }}
									</div>
									<span v-else class="text-muted">Not set</span>
								</template>

								<template #cell-meta_description="{ row }">
									<div v-if="row.meta_description" class="text-truncate" :title="row.meta_description" style="max-width: 300px;">
										{{ row.meta_description }}
									</div>
									<span v-else class="text-muted">Not set</span>
								</template>

								<template #cell-is_active="{ row }">
									<label :class="['badge', row.is_active ? 'badge-success' : 'badge-secondary']">
										{{ row.is_active ? 'Active' : 'Inactive' }}
									</label>
								</template>

								<template #cell-actions="{ row }">
									<div class="d-flex align-items-center action-buttons">
										<button
											type="button"
											class="btn btn-sm btn-soft-primary"
											title="Edit"
											@click="openEditModal(row)"
										>
											<iconify-icon icon="solar:settings-outline" class="icon" />
										</button>
										<button
											type="button"
											class="btn btn-sm btn-soft-danger"
											title="Delete"
											@click="openDeleteModal(row)"
										>
											<iconify-icon icon="solar:trash-bin-minimalistic-outline" class="icon" />
										</button>
									</div>
								</template>
							</UITable>
						</div>
					</div>
				</div>
			</div>
		</div>

		<SeoPageModal
			:show="showCreateModal"
			:loading="isSubmitting"
			@close="closeCreateModal"
			@save="handleCreateSeoPage"
		/>
		<SeoPageModal
			:show="showEditModal"
			:seo-page="selectedSeoPage"
			:loading="isSubmitting"
			@close="closeEditModal"
			@save="handleUpdateSeoPage"
		/>
		<DeleteConfirmModal
			:show="showDeleteModal"
			:item="selectedSeoPage"
			:item-type="'SEO Page'"
			:item-name="selectedSeoPage?.page_title"
			:loading="isDeleting"
			@close="closeDeleteModal"
			@confirm="handleDeleteSeoPage"
		/>
	</div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import NavbarHeader from '@/components/NavbarHeader.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import UITable from '@/components/ui/UITable.vue'
import SeoPageModal from '@/components/seo/SeoPageModal.vue'
import DeleteConfirmModal from '@/components/modals/DeleteConfirmModal.vue'
import { fetchSeoPages, createSeoPage, updateSeoPage, deleteSeoPage } from '@/api/seoPages'
import {
	DEFAULT_PER_PAGE,
	DEFAULT_PAGE,
	SEARCH_DEBOUNCE_DELAY,
	PAGE_SIZE_OPTIONS,
} from '@/config/orders'
import { useToast } from '@/composables/useToast'

const seoPageColumns = [
	{ key: 'page_title', label: 'Page', sortable: false },
	{ key: 'route_name', label: 'Route Name', sortable: false },
	{ key: 'meta_title', label: 'Meta Title', sortable: false },
	{ key: 'meta_description', label: 'Meta Description', sortable: false },
	{ key: 'is_active', label: 'Status', sortable: false },
	{ key: 'actions', label: 'Actions', sortable: false },
]

const seoPages = ref([])
const isLoading = ref(false)

const currentPage = ref(DEFAULT_PAGE)
const perPage = ref(DEFAULT_PER_PAGE)
const totalItems = ref(0)
const lastPage = ref(1)

const searchQuery = ref('')

let searchTimer = null

const debouncedSearch = () => {
	if (searchTimer) {
		clearTimeout(searchTimer)
	}
	searchTimer = setTimeout(() => {
		loadSeoPages()
	}, SEARCH_DEBOUNCE_DELAY)
}

const clearSearch = () => {
	searchQuery.value = ''
	loadSeoPages()
}

const handleNameSearch = () => {
	loadSeoPages()
}

const handlePeriodChange = ({ period, from, to }) => {
	currentPeriod.value = period
	fromDate.value = from
	toDate.value = to
	currentPage.value = 1
	loadSeoPages()
}

const applyFilters = () => {
	currentPage.value = 1
	loadSeoPages()
}

const loadSeoPages = async () => {
	isLoading.value = true
	try {
		const params = {
			page: currentPage.value,
			per_page: perPage.value,
		}

		if (searchQuery.value) {
			params.search = searchQuery.value
		}

		const response = await fetchSeoPages(params)

		if (response.data) {
			seoPages.value = response.data
		}

		if (response.meta) {
			currentPage.value = response.meta.current_page
			totalItems.value = response.meta.total
			lastPage.value = response.meta.last_page
			perPage.value = response.meta.per_page
		}
	} catch (error) {
		console.error('Failed to load SEO pages:', error)
	} finally {
		isLoading.value = false
	}
}

const handlePageChange = (page) => {
	currentPage.value = page
	loadSeoPages()
}

const handlePerPageChange = (newPerPage) => {
	perPage.value = newPerPage
	currentPage.value = 1
	loadSeoPages()
}

const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDeleteModal = ref(false)
const selectedSeoPage = ref(null)
const isSubmitting = ref(false)
const isDeleting = ref(false)

const openCreateModal = () => {
	showCreateModal.value = true
}

const closeCreateModal = () => {
	showCreateModal.value = false
}

const openEditModal = (seoPage) => {
	selectedSeoPage.value = seoPage
	showEditModal.value = true
}

const closeEditModal = () => {
	showEditModal.value = false
	selectedSeoPage.value = null
}

const openDeleteModal = (seoPage) => {
	selectedSeoPage.value = seoPage
	showDeleteModal.value = true
}

const closeDeleteModal = () => {
	showDeleteModal.value = false
	selectedSeoPage.value = null
}

const toast = useToast()

const handleCreateSeoPage = async (formData) => {
	isSubmitting.value = true
	try {
		await createSeoPage(formData)
		toast.success('SEO page created successfully')
		closeCreateModal()
		loadSeoPages()
	} catch (error) {
		toast.error(error.data?.message || error.message || 'Failed to create SEO page')
	} finally {
		isSubmitting.value = false
	}
}

const handleUpdateSeoPage = async (formData) => {
	if (!selectedSeoPage.value) return

	isSubmitting.value = true
	try {
		await updateSeoPage(selectedSeoPage.value.id, formData)
		toast.success('SEO page updated successfully')
		closeEditModal()
		loadSeoPages()
	} catch (error) {
		toast.error(error.data?.message || error.message || 'Failed to update SEO page')
	} finally {
		isSubmitting.value = false
	}
}

const handleDeleteSeoPage = async () => {
	if (!selectedSeoPage.value) return

	isDeleting.value = true
	try {
		await deleteSeoPage(selectedSeoPage.value.id)
		toast.success('SEO page deleted successfully')
		closeDeleteModal()
		loadSeoPages()
	} catch (error) {
		toast.error(error.data?.message || error.message || 'Failed to delete SEO page')
	} finally {
		isDeleting.value = false
	}
}

onMounted(() => {
	loadSeoPages()
})
</script>
