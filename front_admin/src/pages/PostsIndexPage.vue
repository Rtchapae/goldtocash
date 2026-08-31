<template>
	<div>
		<NavbarHeader />
		<div class="dashboard-main-body">
			<PageHeader title="Blog Posts" :loading="isLoading" />

			<div class="row">
				<div id="admin-main-tables" class="col-lg-12 grid-margin stretch-card">
					<div class="card">
						<div class="card-body orders-card-body">
							<div class="pb-3">
								<div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
									<div class="name-search-wrapper flex-grow-1" style="max-width: 420px;">
										<form class="d-flex" @submit.prevent="handleTitleSearch">
											<input
												v-model="titleSearchInput"
												type="search"
												class="form-control"
												placeholder="Search by title..."
												@input="debouncedTitleSearch"
											/>
											<button
												v-if="titleSearchInput"
												type="button"
												class="btn btn-sm btn-outline-secondary ms-2"
												@click="clearTitleSearch"
											>
												Clear
											</button>
										</form>
									</div>
									<router-link to="/posts/create" class="btn btn-primary">
										New post
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
								:rows="posts"
								:columns="postColumns"
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
								<template v-for="col in postColumns" :key="`header-${col.key}`" #[`header-${col.key}`]="{ column }">
									<span v-if="column.sortable" class="d-flex align-items-center sortable-header" @click="handleSort(column.key)">
										{{ column.label }}
										<i
											:class="[
												'fa ms-1',
												orderBy === column.key
													? orderDir === 'asc'
														? 'fa-sort-up'
														: 'fa-sort-down'
													: 'fa-sort'
											]"
										></i>
									</span>
									<span v-else>{{ column.label }}</span>
								</template>

								<template #cell-id="{ row }">
									{{ row.id }}
								</template>

								<template #cell-title="{ row }">
									{{ row.title }}
								</template>

								<template #cell-seo="{ row }">
									<i v-if="row.seo_description" class="fa fa-check text-success"></i>
									<i v-else class="fa fa-times text-danger"></i>
								</template>

								<template #cell-date_created="{ row }">
									{{ formatPostDate(row.created_at) }}
								</template>

								<template #cell-published="{ row }">
									<span :class="['badge', row.active ? 'bg-success text-white' : 'bg-secondary text-white']">
										{{ row.active ? 'Yes' : 'No' }}
									</span>
								</template>

								<template #cell-actions="{ row }">
									<div class="modal-order-data">
										<a v-if="getPostUrl(row)" :href="getPostUrl(row)" target="_blank" rel="noopener noreferrer" title="View" class="me-3">
											<i class="fas fa-eye"></i>
										</a>
										<router-link :to="`/posts/edit/${row.id}`" title="Edit" class="me-3">
											<i class="fas fa-edit"></i>
										</router-link>
										<a href="#" @click.prevent="confirmDelete(row)" title="Delete" class="me-0">
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
import { ref, onMounted, onBeforeUnmount, computed } from 'vue'
import NavbarHeader from '@/components/NavbarHeader.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import UITable from '@/components/ui/UITable.vue'
import ConfirmDeleteModal from '@/components/modals/ConfirmDeleteModal.vue'
import { fetchPosts, deletePost } from '@/api/posts'
import { PAGE_SIZE_OPTIONS, DEFAULT_PER_PAGE, DEFAULT_PAGE, SEARCH_DEBOUNCE_DELAY } from '@/config/orders'
import { formatDate } from '@/utils/date'
import { useToast } from '@/composables/useToast'

const toast = useToast()

const frontBaseUrl = computed(() => {
	return import.meta.env.VITE_FRONT_BASE_URL || ''
})

const posts = ref([])
const isLoading = ref(false)
const showDeleteModal = ref(false)
const isDeleting = ref(false)
const postToDelete = ref(null)
const deleteMessage = ref('')

const currentPage = ref(DEFAULT_PAGE)
const perPage = ref(DEFAULT_PER_PAGE)
const totalItems = ref(0)
const lastPage = ref(1)

/** UI column key -> API `order-by` (ListPostsRequest allowed values) */
const ORDER_BY_API = {
	id: 'id',
	title: 'title',
	date_created: 'created_at',
	published: 'active',
}

const titleSearchInput = ref('')
const titleSearch = ref('')
let titleSearchDebounceId = null

const orderBy = ref('date_created')
const orderDir = ref('desc')

const postColumns = [
	{ key: 'id', label: '#', sortable: true },
	{ key: 'title', label: 'Title', sortable: true, class: 'w-50' },
	{ key: 'seo', label: 'SEO', sortable: false },
	{ key: 'date_created', label: 'Date Created', sortable: true },
	{ key: 'published', label: 'Published', sortable: true },
	{ key: 'actions', label: 'Action', sortable: false }
]

const loadPosts = async () => {
	isLoading.value = true
	try {
		const params = {
			page: currentPage.value,
			per_page: perPage.value,
		}

		if (titleSearch.value) {
			params.search = titleSearch.value
		}

		if (orderBy.value) {
			params['order-by'] = ORDER_BY_API[orderBy.value] || orderBy.value
			params['order-dir'] = orderDir.value
		}

		const response = await fetchPosts(params)

		if (response.data) {
			posts.value = response.data
		}

		if (response.meta) {
			currentPage.value = response.meta.current_page
			totalItems.value = response.meta.total
			lastPage.value = response.meta.last_page
			perPage.value = response.meta.per_page
		}
	} catch (error) {
		posts.value = []
		const errorMessage = error.status === 401
			? 'Unauthorized. Please login again.'
			: error.status === 500
			? 'Server error. Please try again later.'
			: 'Failed to load posts'
		toast.error(errorMessage)
	} finally {
		isLoading.value = false
	}
}

const handleTitleSearch = () => {
	titleSearch.value = titleSearchInput.value.trim()
	currentPage.value = 1
	loadPosts()
}

const debouncedTitleSearch = () => {
	if (titleSearchDebounceId) {
		clearTimeout(titleSearchDebounceId)
	}
	titleSearchDebounceId = setTimeout(() => {
		titleSearchDebounceId = null
		const next = titleSearchInput.value.trim()
		if (next !== titleSearch.value) {
			titleSearch.value = next
			currentPage.value = 1
			loadPosts()
		}
	}, SEARCH_DEBOUNCE_DELAY)
}

const clearTitleSearch = () => {
	titleSearchInput.value = ''
	titleSearch.value = ''
	currentPage.value = 1
	loadPosts()
}

const handleSort = (column) => {
	if (orderBy.value === column) {
		orderDir.value = orderDir.value === 'asc' ? 'desc' : 'asc'
	} else {
		orderBy.value = column
		orderDir.value = column === 'title' ? 'asc' : 'desc'
	}
	currentPage.value = 1
	loadPosts()
}

const handlePageChange = (page) => {
	currentPage.value = page
	loadPosts()
}

const handlePerPageChange = (newPerPage) => {
	perPage.value = newPerPage
	currentPage.value = 1
	loadPosts()
}

const formatPostDate = (dateString) => {
	if (!dateString) return '-'
	return formatDate(dateString, 'MM/DD/YYYY')
}

const getPostUrl = (post) => {
	if (!post || !post.url) return null
	
	try {
		if (frontBaseUrl.value && post.url) {
			try {
				const urlObj = new URL(post.url)
				const path = urlObj.pathname
				
				const baseUrl = frontBaseUrl.value.replace(/\/$/, '')
				return `${baseUrl}${path}`
			} catch (e) {
				const baseUrl = frontBaseUrl.value.replace(/\/$/, '')
				const path = post.url.startsWith('/') ? post.url : `/${post.url}`
				return `${baseUrl}${path}`
			}
		}
		
		return post.url
	} catch (error) {
		console.warn('Error generating post URL:', error)
		return post.url || null
	}
}

const confirmDelete = (post) => {
	postToDelete.value = post
	deleteMessage.value = `Are you sure you want to delete post "${post.title}"?`
	showDeleteModal.value = true
}

const closeDeleteModal = () => {
	showDeleteModal.value = false
	postToDelete.value = null
	deleteMessage.value = ''
}

const handleDeleteConfirm = async () => {
	if (!postToDelete.value) {
		return
	}

	isDeleting.value = true
	try {
		await deletePost(postToDelete.value.id)
		toast.success('Post deleted successfully')
		loadPosts()
		closeDeleteModal()
	} catch (error) {
		const errorMessage = error.status === 401
			? 'Unauthorized. Please login again.'
			: error.status === 500
			? 'Server error. Please try again later.'
			: 'Failed to delete post'
		toast.error(errorMessage)
	} finally {
		isDeleting.value = false
	}
}

onMounted(() => {
	loadPosts()
})

onBeforeUnmount(() => {
	if (titleSearchDebounceId) {
		clearTimeout(titleSearchDebounceId)
		titleSearchDebounceId = null
	}
})
</script>
