<template>
	<div>
		<NavbarHeader />
		<div class="dashboard-main-body">
			<PageHeader title="Page Builder" :loading="isLoading" />

			<div class="row">
				<div class="col-lg-12 grid-margin stretch-card">
					<div class="card">
						<div class="card-body orders-card-body">
							<div class="pb-3">
								<div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
									<div class="flex-grow-1" style="max-width: 420px;">
										<input
											v-model="searchInput"
											type="search"
											class="form-control"
											placeholder="Search by title or URL..."
											@input="debouncedSearch"
										/>
									</div>
									<router-link to="/landing-pages/create" class="btn btn-primary">
										New page
									</router-link>
								</div>
							</div>

							<UITable
								:show-title="false"
								:show-index="false"
								:show-length-control="true"
								:show-search-control="false"
								:show-pagination="true"
								:show-info="true"
								table-class="table table-striped table-hover orders-table"
								:rows="pages"
								:columns="columns"
								:server-pagination="true"
								:current-page="currentPage"
								:total-items="totalItems"
								:per-page="perPage"
								:last-page="lastPage"
								:page-size-options="PAGE_SIZE_OPTIONS"
								:loading="isLoading"
								@page-change="handlePageChange"
								@per-page-change="handlePerPageChange"
							>
								<template #cell-title="{ row }">
									{{ row.title }}
								</template>
								<template #cell-path="{ row }">
									<code>{{ row.path }}</code>
								</template>
								<template #cell-published="{ row }">
									<span :class="['badge', row.active ? 'bg-success text-white' : 'bg-secondary text-white']">
										{{ row.active ? 'Yes' : 'No' }}
									</span>
								</template>
								<template #cell-date_created="{ row }">
									{{ formatDate(row.created_at, 'MM/DD/YYYY') }}
								</template>
								<template #cell-actions="{ row }">
									<div class="modal-order-data">
										<a v-if="getPageUrl(row)" :href="getPageUrl(row)" target="_blank" rel="noopener noreferrer" title="View" class="me-3">
											<i class="fas fa-eye"></i>
										</a>
										<router-link :to="`/landing-pages/edit/${row.id}`" title="Edit" class="me-3">
											<i class="fas fa-edit"></i>
										</router-link>
										<a href="#" @click.prevent="confirmDelete(row)" title="Delete">
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
			@close="closeDeleteModal"
			@confirm="handleDeleteConfirm"
		/>
	</div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import NavbarHeader from '@/components/NavbarHeader.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import UITable from '@/components/ui/UITable.vue'
import ConfirmDeleteModal from '@/components/modals/ConfirmDeleteModal.vue'
import { fetchLandingPages, deleteLandingPage } from '@/api/landingPages'
import { PAGE_SIZE_OPTIONS, DEFAULT_PER_PAGE, DEFAULT_PAGE, SEARCH_DEBOUNCE_DELAY } from '@/config/orders'
import { formatDate } from '@/utils/date'
import { useToast } from '@/composables/useToast'

const toast = useToast()
const frontBaseUrl = computed(() => import.meta.env.VITE_FRONT_BASE_URL || '')

const pages = ref([])
const isLoading = ref(false)
const showDeleteModal = ref(false)
const isDeleting = ref(false)
const pageToDelete = ref(null)
const deleteMessage = ref('')
const currentPage = ref(DEFAULT_PAGE)
const perPage = ref(DEFAULT_PER_PAGE)
const totalItems = ref(0)
const lastPage = ref(1)
const searchInput = ref('')
const search = ref('')
let searchDebounceId = null

const columns = [
	{ key: 'id', label: '#', sortable: false },
	{ key: 'title', label: 'Title', sortable: false },
	{ key: 'path', label: 'URL', sortable: false },
	{ key: 'date_created', label: 'Created', sortable: false },
	{ key: 'published', label: 'Published', sortable: false },
	{ key: 'actions', label: 'Action', sortable: false },
]

const loadPages = async () => {
	isLoading.value = true
	try {
		const params = { page: currentPage.value, per_page: perPage.value }
		if (search.value) params.search = search.value
		const response = await fetchLandingPages(params)
		pages.value = response.data || []
		if (response.meta) {
			currentPage.value = response.meta.current_page
			totalItems.value = response.meta.total
			lastPage.value = response.meta.last_page
			perPage.value = response.meta.per_page
		}
	} catch (error) {
		pages.value = []
		toast.error('Failed to load landing pages')
	} finally {
		isLoading.value = false
	}
}

const debouncedSearch = () => {
	if (searchDebounceId) clearTimeout(searchDebounceId)
	searchDebounceId = setTimeout(() => {
		search.value = searchInput.value.trim()
		currentPage.value = 1
		loadPages()
	}, SEARCH_DEBOUNCE_DELAY)
}

const handlePageChange = (page) => {
	currentPage.value = page
	loadPages()
}

const handlePerPageChange = (newPerPage) => {
	perPage.value = newPerPage
	currentPage.value = 1
	loadPages()
}

const getPageUrl = (page) => {
	if (!page?.path) return null
	const base = frontBaseUrl.value.replace(/\/$/, '')
	return `${base}${page.path}`
}

const confirmDelete = (page) => {
	pageToDelete.value = page
	deleteMessage.value = `Delete page "${page.title}"?`
	showDeleteModal.value = true
}

const closeDeleteModal = () => {
	showDeleteModal.value = false
	pageToDelete.value = null
}

const handleDeleteConfirm = async () => {
	if (!pageToDelete.value) return
	isDeleting.value = true
	try {
		await deleteLandingPage(pageToDelete.value.id)
		toast.success('Page deleted')
		loadPages()
		closeDeleteModal()
	} catch {
		toast.error('Failed to delete page')
	} finally {
		isDeleting.value = false
	}
}

onMounted(loadPages)
onBeforeUnmount(() => {
	if (searchDebounceId) clearTimeout(searchDebounceId)
})
</script>
