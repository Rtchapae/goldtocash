<template>
	<div>
		<NavbarHeader />
		<div class="dashboard-main-body">
			<PageHeader title="Page Builder" :loading="isLoading" />

			<div class="row">
				<div class="col-lg-12 grid-margin stretch-card">
					<div class="card">
						<div class="card-body">
							<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pb-3">
								<form class="d-flex flex-grow-1" style="max-width: 420px;" @submit.prevent="handleSearch">
									<input
										v-model="searchInput"
										type="search"
										class="form-control"
										placeholder="Search by title or slug..."
										@input="debouncedSearch"
									/>
									<button
										v-if="searchInput"
										type="button"
										class="btn btn-sm btn-outline-secondary ms-2"
										@click="clearSearch"
									>
										Clear
									</button>
								</form>
								<router-link to="/builder-pages/create" class="btn btn-primary">
									New page
								</router-link>
							</div>

							<UITable
								:show-title="false"
								:show-index="false"
								:show-length-control="true"
								:show-search-control="false"
								:show-pagination="true"
								:show-info="true"
								table-class="table table-striped table-hover"
								:rows="pages"
								:columns="columns"
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
								<template #cell-title="{ row }">
									{{ row.title }}
								</template>
								<template #cell-slug="{ row }">
									<code>/{{ row.slug }}</code>
								</template>
								<template #cell-updated="{ row }">
									{{ formatDate(row.updated_at, 'MM/DD/YYYY') }}
								</template>
								<template #cell-published="{ row }">
									<span :class="['badge', row.published ? 'bg-success text-white' : 'bg-secondary text-white']">
										{{ row.published ? 'Published' : 'Draft' }}
									</span>
								</template>
								<template #cell-actions="{ row }">
									<div class="modal-order-data">
										<a
											v-if="row.published"
											:href="publicUrl(row)"
											target="_blank"
											rel="noopener noreferrer"
											title="View"
											class="me-3"
										>
											<i class="fas fa-eye"></i>
										</a>
										<router-link :to="`/builder-pages/edit/${row.id}`" title="Edit" class="me-3">
											<i class="fas fa-edit"></i>
										</router-link>
										<a href="#" title="Delete" class="me-0" @click.prevent="confirmDelete(row)">
											<i class="fas fa-trash"></i>
										</a>
									</div>
								</template>
							</UITable>
						</div>
					</div>
				</div>
			</div>
		</div>

		<ConfirmDeleteModal
			:show="showDeleteModal"
			:message="deleteMessage"
			:is-deleting="isDeleting"
			@confirm="handleDelete"
			@close="showDeleteModal = false"
		/>
	</div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import NavbarHeader from '@/components/NavbarHeader.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import UITable from '@/components/ui/UITable.vue'
import ConfirmDeleteModal from '@/components/modals/ConfirmDeleteModal.vue'
import { fetchBuilderPages, deleteBuilderPage } from '@/api/builderPages'
import { PAGE_SIZE_OPTIONS, DEFAULT_PER_PAGE, DEFAULT_PAGE, SEARCH_DEBOUNCE_DELAY } from '@/config/orders'
import { useToast } from '@/composables/useToast'
import { formatDate } from '@/utils/date'

const toast = useToast()
const isLoading = ref(false)
const pages = ref([])
const currentPage = ref(DEFAULT_PAGE)
const perPage = ref(DEFAULT_PER_PAGE)
const totalItems = ref(0)
const lastPage = ref(1)
const searchInput = ref('')
const searchQuery = ref('')
const showDeleteModal = ref(false)
const deleteMessage = ref('')
const pageToDelete = ref(null)
const isDeleting = ref(false)
let searchTimer = null

const frontBaseUrl = (import.meta.env.VITE_FRONT_BASE_URL || 'https://goldtocash.us').replace(/\/$/, '')

const columns = [
	{ key: 'title', label: 'Title' },
	{ key: 'slug', label: 'Slug' },
	{ key: 'updated', label: 'Updated' },
	{ key: 'published', label: 'Status' },
	{ key: 'actions', label: 'Actions' },
]

const publicUrl = (row) => `${frontBaseUrl}/${row.slug}`

const loadPages = async () => {
	isLoading.value = true
	try {
		const response = await fetchBuilderPages({
			page: currentPage.value,
			per_page: perPage.value,
			search: searchQuery.value,
		})
		pages.value = response.data || []
		const meta = response.meta || {}
		currentPage.value = meta.current_page || 1
		lastPage.value = meta.last_page || 1
		perPage.value = meta.per_page || perPage.value
		totalItems.value = meta.total || 0
	} catch (e) {
		toast.error(e.message || 'Failed to load pages')
		pages.value = []
	} finally {
		isLoading.value = false
	}
}

const handlePageChange = (page) => {
	currentPage.value = page
	loadPages()
}

const handlePerPageChange = (size) => {
	perPage.value = size
	currentPage.value = 1
	loadPages()
}

const handleSearch = () => {
	searchQuery.value = searchInput.value.trim()
	currentPage.value = 1
	loadPages()
}

const debouncedSearch = () => {
	clearTimeout(searchTimer)
	searchTimer = setTimeout(handleSearch, SEARCH_DEBOUNCE_DELAY)
}

const clearSearch = () => {
	searchInput.value = ''
	searchQuery.value = ''
	currentPage.value = 1
	loadPages()
}

const confirmDelete = (row) => {
	pageToDelete.value = row
	deleteMessage.value = `Delete page "${row.title}"?`
	showDeleteModal.value = true
}

const handleDelete = async () => {
	if (!pageToDelete.value) return
	isDeleting.value = true
	try {
		await deleteBuilderPage(pageToDelete.value.id)
		toast.success('Page deleted')
		showDeleteModal.value = false
		pageToDelete.value = null
		await loadPages()
	} catch (e) {
		toast.error(e.message || 'Delete failed')
	} finally {
		isDeleting.value = false
	}
}

onMounted(loadPages)
onBeforeUnmount(() => clearTimeout(searchTimer))
</script>
