<template>
	<div>
		<NavbarHeader />
		<div class="dashboard-main-body">
			<PageHeader title="Active Orders" :loading="isLoading" />

			<div class="row">
				<div id="admin-main-tables" class="col-lg-12 grid-margin stretch-card">
					<div class="card">
						<div class="card-body orders-card-body orders-card-body--infinite-table">
							<div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-3">
								<div class="name-search-wrapper flex-grow-1" style="min-width: 200px; max-width: 520px;">
									<form @submit.prevent="handleNameSearch" class="d-flex">
										<input
											v-model="nameQuery"
											type="text"
											class="form-control flex-grow-1"
											placeholder="Search by name, email, or phone..."
											@input="debouncedNameSearch"
										/>
										<button v-if="nameQuery" type="button" class="btn btn-sm btn-outline-secondary ms-2" @click="clearNameSearch">
											Clear
										</button>
									</form>
								</div>
								<PeriodFilter
									toolbar
									v-model="currentPeriod"
									:from="fromDate"
									:to="toDate"
									@update:from="fromDate = $event"
									@update:to="toDate = $event"
									@change="handlePeriodChange"
								/>
							</div>
							<hr />

							<div class="pb-3">
								<div class="panel d-flex align-items-start flex-wrap gap-3">
									<div class="input-group utm-campaign-filter filter-select-wrapper">
										<div class="input-group-prepend">
											<span class="input-group-text">utm_campaign</span>
										</div>
										<select v-model="utmCampaignFilter" class="form-control" @change="applyFilters">
											<option value="any">Any</option>
											<option v-for="campaign in availableUtmCampaigns" :key="campaign" :value="campaign">
												{{ campaign }}
											</option>
										</select>
									</div>

									<div class="input-group utm-medium-filter filter-select-wrapper">
										<div class="input-group-prepend">
											<span class="input-group-text">utm_medium</span>
										</div>
										<select v-model="utmMediumFilter" class="form-control" @change="applyFilters">
											<option value="any">Any</option>
											<option v-for="medium in availableUtmMediums" :key="medium" :value="medium">
												{{ medium }}
											</option>
										</select>
									</div>

									<div class="input-group source-filter filter-select-wrapper">
										<div class="input-group-prepend">
											<span class="input-group-text">Source</span>
										</div>
										<select v-model="sourceFilter" class="form-control" @change="applyFilters">
											<option value="any">Any</option>
											<option v-for="source in availableSources" :key="source" :value="source">
												{{ source }}
											</option>
										</select>
									</div>

									<div class="input-group order-type-filter filter-select-wrapper">
										<div class="input-group-prepend">
											<span class="input-group-text">Type</span>
										</div>
										<select v-model="orderTypeFilter" class="form-control" @change="applyFilters">
											<option value="any">Any</option>
											<option value="online">Online</option>
											<option value="offline">Offline</option>
										</select>
									</div>

									<div class="input-group branch-filter filter-select-wrapper">
										<div class="input-group-prepend">
											<span class="input-group-text">Branch</span>
										</div>
										<select v-model="branchFilter" class="form-control" @change="applyFilters">
											<option value="any">Any</option>
											<option v-for="branch in availableBranches" :key="branch.id" :value="branch.id">
												{{ branch.label }}
											</option>
										</select>
									</div>
								</div>
							</div>

							<UITable
								:show-title="false"
								:show-index="false"
								:show-length-control="false"
								:show-search-control="false"
								:show-pagination="false"
								:show-info="true"
								table-class="table table-striped table-hover orders-table"
								:rows="orders"
								:columns="orderColumns"
								:server-pagination="true"
								:current-page="currentPage"
								:total-items="totalItems"
								:per-page="perPage"
								:last-page="lastPage"
								:sortable="false"
								:loading="isLoading"
								:infinite-scroll="true"
								:has-more="hasMore"
								:loading-more="isLoadingMore"
								:load-more-offset="10"
								scroll-max-height="calc(100vh - 300px)"
								@load-more="loadMoreOrders"
							>
								<template v-for="col in orderColumns" :key="`header-${col.key}`" #[`header-${col.key}`]="{ column }">
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
								<template #cell-name="{ row }">
									{{ formatName(row) }}
									<iconify-icon
										v-if="row.is_returning_customer"
										icon="solar:star-bold"
										class="text-warning ms-1 icon-size-base"
										title="Returning"
									></iconify-icon>
								</template>

								<template #cell-url="{ row }">
									<OrderUrlTruncated :url="row.url" />
								</template>

								<template #cell-source="{ row }">
									<OrderSourceBadgesCollapse :badges="row.source_badges || []" />
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

								<template #cell-order_number="{ row }">
									{{ row.id }}
								</template>

								<template #cell-date_created="{ row }">
									{{ formatDateMMDDYY(orderRowTimestamp(row)) }}
								</template>

								<template #cell-amount="{ row }">
									{{ row.amount }}
								</template>

								<template #cell-status="{ row }">
									<label :class="['badge', getStatusBadgeClass(row.status)]">
										{{ row.status || 'Unknown' }}
									</label>
								</template>

								<template #cell-order_type="{ row }">
									<span class="badge badge-secondary">
										{{ row.order_type === 'online' ? 'Online' : 'Offline' }}
									</span>
								</template>

								<template #cell-branch="{ row }">
									<span v-if="row.order_type === 'offline'">
										{{ row.branch_name || 'N/A' }}
									</span>
									<span v-else>
										—
									</span>
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
											class="btn btn-sm btn-soft-success"
											title="History"
											@click="openHistoryModal(row)"
										>
											<iconify-icon icon="solar:history-outline" class="icon" />
										</button>
									</div>
								</template>

								<template #export-controls>
									<button
										type="button"
										class="btn btn-sm btn-primary ms-3"
										:disabled="isLoading || isExporting"
										@click="exportToCsv"
									>
										<i class="fas fa-download me-1"></i>
										Export to CSV
									</button>
								</template>
							</UITable>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<ViewOrderDetailsModal
		:show="showViewModal"
		:order="orderDetails || selectedOrder"
		@close="closeViewModal"
	/>
	<ViewOrderHistoryModal
		:show="showHistoryModal"
		:history="orderHistory"
		:order-created-at="selectedOrder ? orderRowTimestamp(selectedOrder) : ''"
		@close="closeHistoryModal"
	/>
	<EditOrderDetailsModal
		:show="showEditModal"
		:order="orderDetails || selectedOrder"
		:loading="isUpdatingOrder"
		@close="closeEditModal"
		@save="handleSaveOrder"
	/>
	<EditOrderShippingDetailsModal
		:show="showShippingModal"
		:order="selectedOrder"
		:loading="isUpdatingShipping"
		@close="closeShippingModal"
		@save="handleSaveShipping"
	/>
	<ViewOrderFilesModal
		:show="showFilesModal"
		:files="orderFiles"
		@close="closeFilesModal"
	/>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import NavbarHeader from '@/components/NavbarHeader.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import UITable from '@/components/ui/UITable.vue'
import PeriodFilter from '@/components/filters/PeriodFilter.vue'
import ViewOrderDetailsModal from '@/components/modals/ViewOrderDetailsModal.vue'
import ViewOrderHistoryModal from '@/components/modals/ViewOrderHistoryModal.vue'
import EditOrderDetailsModal from '@/components/modals/EditOrderDetailsModal.vue'
import EditOrderShippingDetailsModal from '@/components/modals/EditOrderShippingDetailsModal.vue'
import ViewOrderFilesModal from '@/components/modals/ViewOrderFilesModal.vue'
import OrderUrlTruncated from '@/components/orders/OrderUrlTruncated.vue'
import OrderSourceBadgesCollapse from '@/components/orders/OrderSourceBadgesCollapse.vue'
import { fetchAdminOrders, getOrderDetails, getOrderHistory, getOrderFiles, updateOrder, updateOrderShipping } from '@/api/adminOrders'
import {
	ORDERS_INFINITE_SCROLL_PER_PAGE,
	DEFAULT_PAGE,
	DEFAULT_PERIOD,
	DEFAULT_FILTER_VALUE,
	DEFAULT_SORT_DIR,
	SEARCH_DEBOUNCE_DELAY,
	ORDER_COLUMNS,
} from '@/config/orders'
import { getStatusBadgeClass } from '@/utils/orderStatus'
import { formatName, formatPhone, normalizePhone } from '@/utils/format'
import { formatDateMMDDYY, formatTimeOnly } from '@/utils/date'
import { useToast } from '@/composables/useToast'

const orderRowTimestamp = (row) => row?.created_at || row?.date_created || ''

const route = useRoute()

const orders = ref([])
const isLoading = ref(false)
const isLoadingMore = ref(false)
const isExporting = ref(false)

// Server-side pagination state (infinite scroll: append pages)
const currentPage = ref(DEFAULT_PAGE)
const perPage = ref(ORDERS_INFINITE_SCROLL_PER_PAGE)
const totalItems = ref(0)
const lastPage = ref(1)
let loadRequestId = 0

const hasMore = computed(() => currentPage.value < lastPage.value)

// Filters
const nameQuery = ref('')
const sourceFilter = ref(DEFAULT_FILTER_VALUE)
const utmCampaignFilter = ref(DEFAULT_FILTER_VALUE)
const utmMediumFilter = ref(DEFAULT_FILTER_VALUE)
const orderTypeFilter = ref(DEFAULT_FILTER_VALUE)
const branchFilter = ref(DEFAULT_FILTER_VALUE)
const currentPeriod = ref(DEFAULT_PERIOD)
const fromDate = ref(null)
const toDate = ref(null)

// Sorting
const orderBy = ref(null)
const orderDir = ref(DEFAULT_SORT_DIR)

// Available filter options
const availableSources = ref([])
const availableUtmCampaigns = ref([])
const availableUtmMediums = ref([])
const availableBranches = ref([])

// Use constants for columns
const orderColumns = ORDER_COLUMNS

// Debounce timer for name search
let nameSearchTimer = null

// Theme-aware badge classes
const getOrderTypeBadgeClass = (orderType) => {
	const isDark = document.documentElement.hasAttribute('data-theme') && document.documentElement.getAttribute('data-theme') === 'dark'
	return orderType === 'online'
		? (isDark ? 'badge-info' : 'badge-dark')
		: (isDark ? 'badge-secondary' : 'badge-danger')
}

const debouncedNameSearch = () => {
	if (nameSearchTimer) {
		clearTimeout(nameSearchTimer)
	}
	nameSearchTimer = setTimeout(() => {
		resetAndLoadOrders()
	}, SEARCH_DEBOUNCE_DELAY)
}

const clearNameSearch = () => {
	nameQuery.value = ''
	resetAndLoadOrders()
}

const handleNameSearch = () => {
	if (nameSearchTimer) {
		clearTimeout(nameSearchTimer)
	}
	resetAndLoadOrders()
}

const handlePeriodChange = ({ period, from, to }) => {
	currentPeriod.value = period
	fromDate.value = from
	toDate.value = to
	resetAndLoadOrders()
}

const applyFilters = () => {
	resetAndLoadOrders()
}

const handleSort = (columnKey) => {
	if (orderBy.value === columnKey) {
		orderDir.value = orderDir.value === 'asc' ? 'desc' : 'asc'
	} else {
		orderBy.value = columnKey
		orderDir.value = DEFAULT_SORT_DIR
	}
	resetAndLoadOrders()
}

const exportToCsv = async () => {
	isExporting.value = true
	try {
		// Get all data with current filters (no pagination)
		const params = {
			page: 1,
			per_page: 10000, // Large limit to get all records
			period: currentPeriod.value,
			export: true, // Flag to indicate export request
		}

		if (currentPeriod.value === 'custom' && fromDate.value && toDate.value) {
			params.from = fromDate.value
			params.to = toDate.value
		}

		if (nameQuery.value) {
			params.nameQuery = nameQuery.value
		}
		if (sourceFilter.value && sourceFilter.value !== DEFAULT_FILTER_VALUE) {
			params.source = sourceFilter.value
		}
		if (utmCampaignFilter.value && utmCampaignFilter.value !== DEFAULT_FILTER_VALUE) {
			params.utm_campaign = utmCampaignFilter.value
		}
		if (utmMediumFilter.value && utmMediumFilter.value !== DEFAULT_FILTER_VALUE) {
			params.utm_medium = utmMediumFilter.value
		}
		if (orderTypeFilter.value && orderTypeFilter.value !== DEFAULT_FILTER_VALUE) {
			params.order_type = orderTypeFilter.value
		}
		if (branchFilter.value && branchFilter.value !== DEFAULT_FILTER_VALUE) {
			params.branch_id = branchFilter.value
		}

		const response = await fetchAdminOrders(params)

		if (response.data && Array.isArray(response.data)) {
			// Convert to CSV
			const csvData = convertToCsv(response.data)
			downloadCsv(csvData, 'orders_export.csv')
		} else {
			console.error('Invalid export data format')
		}
	} catch (error) {
		console.error('Export failed:', error)
		toast.error('Export failed. Please try again.')
	} finally {
		isExporting.value = false
	}
}

const convertToCsv = (data) => {
	if (!data || data.length === 0) return ''

	// Define CSV headers based on orderColumns
	const headers = orderColumns.map(col => col.label)

	// Convert rows to CSV format
	const csvRows = data.map(row => {
		return orderColumns.map(col => {
			let value = row[col.key]

			// Special handling for specific columns
			switch (col.key) {
				case 'order_type':
					value = value === 'online' ? 'Online' : 'Offline'
					break
				case 'status':
					value = row.status_text || value
					break
				case 'amount':
					value = value ? `$${parseFloat(value).toFixed(2)}` : ''
					break
				case 'date_created': {
					const ts = row.created_at || row.date_created || value
					value = ts ? `${formatDateMMDDYY(ts)} ${formatTimeOnly(ts)}`.trim() : ''
					break
				}
				case 'branch':
					value = (value === 'online') ? '' : (row.branch_name || 'N/A')
					break
				default:
					value = value || ''
			}

			// Escape quotes and wrap in quotes if contains comma or quote
			if (typeof value === 'string' && (value.includes(',') || value.includes('"') || value.includes('\n'))) {
				value = '"' + value.replace(/"/g, '""') + '"'
			}

			return value
		})
	})

	// Combine headers and rows
	return [headers, ...csvRows].map(row => row.join(',')).join('\n')
}

const downloadCsv = (csvData, filename) => {
	const blob = new Blob([csvData], { type: 'text/csv;charset=utf-8;' })
	const link = document.createElement('a')

	if (link.download !== undefined) {
		const url = URL.createObjectURL(blob)
		link.setAttribute('href', url)
		link.setAttribute('download', filename)
		link.style.visibility = 'hidden'
		document.body.appendChild(link)
		link.click()
		document.body.removeChild(link)
	}
}

const buildOrdersParams = (page) => {
	const params = {
		page,
		per_page: perPage.value,
		period: currentPeriod.value,
	}

	if (currentPeriod.value === 'custom' && fromDate.value && toDate.value) {
		params.from = fromDate.value
		params.to = toDate.value
	}

	if (nameQuery.value) {
		params.nameQuery = nameQuery.value
	}
	if (sourceFilter.value && sourceFilter.value !== DEFAULT_FILTER_VALUE) {
		params.source = sourceFilter.value
	}
	if (utmCampaignFilter.value && utmCampaignFilter.value !== DEFAULT_FILTER_VALUE) {
		params.utm_campaign = utmCampaignFilter.value
	}
	if (utmMediumFilter.value && utmMediumFilter.value !== DEFAULT_FILTER_VALUE) {
		params.utm_medium = utmMediumFilter.value
	}
	if (orderTypeFilter.value && orderTypeFilter.value !== DEFAULT_FILTER_VALUE) {
		params.order_type = orderTypeFilter.value
	}
	if (branchFilter.value && branchFilter.value !== DEFAULT_FILTER_VALUE) {
		params.branch_id = branchFilter.value
	}
	if (orderBy.value) {
		params['order-by'] = orderBy.value
		params['order-dir'] = orderDir.value
	}

	return params
}

const applyOrdersResponse = (response, { append }) => {
	if (response.data) {
		const rows = Array.isArray(response.data) ? response.data : []
		orders.value = append ? [...orders.value, ...rows] : rows
	}

	if (response.meta) {
		currentPage.value = response.meta.current_page
		totalItems.value = response.meta.total
		lastPage.value = response.meta.last_page
		perPage.value = response.meta.per_page
	}

	if (response.filters) {
		availableSources.value = response.filters.sources || []
		availableUtmCampaigns.value = response.filters.utm_campaigns || []
		availableUtmMediums.value = response.filters.utm_mediums || []
		availableBranches.value = response.filters.branches || []
	}
}

const loadOrders = async ({ append = false } = {}) => {
	const requestId = ++loadRequestId

	if (append) {
		if (isLoading.value || isLoadingMore.value || !hasMore.value) {
			return
		}
		isLoadingMore.value = true
	} else {
		isLoading.value = true
	}

	try {
		const page = append ? currentPage.value + 1 : DEFAULT_PAGE
		const response = await fetchAdminOrders(buildOrdersParams(page))

		if (requestId !== loadRequestId) {
			return
		}

		applyOrdersResponse(response, { append })
	} catch (error) {
		if (requestId === loadRequestId) {
			console.error('Failed to load orders:', error)
		}
	} finally {
		if (requestId === loadRequestId) {
			isLoading.value = false
			isLoadingMore.value = false
		}
	}
}

const loadMoreOrders = () => {
	loadOrders({ append: true })
}

const resetAndLoadOrders = () => {
	currentPage.value = DEFAULT_PAGE
	lastPage.value = 1
	orders.value = []
	loadOrders()
}



const showViewModal = ref(false)
const showEditModal = ref(false)
const showHistoryModal = ref(false)
const showFilesModal = ref(false)
const showShippingModal = ref(false)
const selectedOrder = ref(null)
const orderDetails = ref(null)
const orderHistory = ref([])
const orderFiles = ref([])
const isLoadingOrderDetails = ref(false)
const isLoadingOrderHistory = ref(false)
const isLoadingOrderFiles = ref(false)
const isUpdatingOrder = ref(false)
const isUpdatingShipping = ref(false)

const openViewModal = async (row) => {
	selectedOrder.value = row
	isLoadingOrderDetails.value = true
	showViewModal.value = true
	
	try {
		const data = await getOrderDetails(row.id)
		console.log('Order details loaded:', { id: data.id, status: data.status, hasStatus: !!data.status })
		orderDetails.value = data
	} catch (error) {
		console.error('Failed to load order details:', error)
		orderDetails.value = row
	} finally {
		isLoadingOrderDetails.value = false
	}
}

const openEditModal = async (row) => {
	selectedOrder.value = row
	isLoadingOrderDetails.value = true
	showEditModal.value = true
	
	try {
		const data = await getOrderDetails(row.id)
		orderDetails.value = data
	} catch (error) {
		console.error('Failed to load order details:', error)
		orderDetails.value = row
	} finally {
		isLoadingOrderDetails.value = false
	}
}

const openFilesModal = async (row) => {
	selectedOrder.value = row
	isLoadingOrderFiles.value = true
	showFilesModal.value = true
	
	try {
		const data = await getOrderFiles(row.id)
		orderFiles.value = data.files || data || []
	} catch (error) {
		console.error('Failed to load order files:', error)
		orderFiles.value = []
	} finally {
		isLoadingOrderFiles.value = false
	}
}

const openHistoryModal = async (row) => {
	selectedOrder.value = row
	isLoadingOrderHistory.value = true
	showHistoryModal.value = true
	
	try {
		const data = await getOrderHistory(row.id)
		orderHistory.value = data.history || data || []
	} catch (error) {
		console.error('Failed to load order history:', error)
		orderHistory.value = []
	} finally {
		isLoadingOrderHistory.value = false
	}
}

const openShippingModal = (row) => {
	selectedOrder.value = row
	showShippingModal.value = true
}

const closeViewModal = () => {
	showViewModal.value = false
	selectedOrder.value = null
	orderDetails.value = null
}

const closeEditModal = () => {
	showEditModal.value = false
	selectedOrder.value = null
	orderDetails.value = null
}

const closeHistoryModal = () => {
	showHistoryModal.value = false
	selectedOrder.value = null
	orderHistory.value = []
}

const closeFilesModal = () => {
	showFilesModal.value = false
	selectedOrder.value = null
	orderFiles.value = []
}

const closeShippingModal = () => {
	showShippingModal.value = false
	selectedOrder.value = null
}

const toast = useToast()

const handleSaveOrder = async (formData) => {
	if (!selectedOrder.value) return
	
	isUpdatingOrder.value = true
	try {
		await updateOrder(selectedOrder.value.id, formData)
		await loadOrders()
		closeEditModal()
		toast.success('Order updated successfully')
	} catch (error) {
		console.error('Failed to update order:', error)
		toast.error('Failed to update order. Please try again.')
	} finally {
		isUpdatingOrder.value = false
	}
}

const handleSaveShipping = async (formData) => {
	if (!selectedOrder.value) return
	
	isUpdatingShipping.value = true
	try {
		await updateOrderShipping(selectedOrder.value.id, formData)
		await loadOrders()
		closeShippingModal()
		toast.success('Shipping details updated successfully')
	} catch (error) {
		console.error('Failed to update shipping:', error)
		toast.error('Failed to update shipping. Please try again.')
	} finally {
		isUpdatingShipping.value = false
	}
}

onMounted(() => {
	const searchFromRoute = route.query.search || route.query.nameQuery
	if (typeof searchFromRoute === 'string' && searchFromRoute.trim()) {
		nameQuery.value = searchFromRoute.trim()
	}
	loadOrders()
})
</script>

