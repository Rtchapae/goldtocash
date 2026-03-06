<template>
	<div>
		<NavbarHeader />
		<div class="dashboard-main-body">
			<PageHeader title="Payment History" :loading="isLoading" />

			<div class="row">
				<div id="admin-main-tables" class="col-lg-12 grid-margin stretch-card">
					<div class="card">
					<div class="card-body orders-card-body">
						<div class="pb-3">
							<div class="panel d-flex align-items-start flex-wrap gap-3">
								<PeriodFilter
									v-model="currentPeriod"
									:from="fromDate"
									:to="toDate"
									@update:from="fromDate = $event"
									@update:to="toDate = $event"
									@change="handlePeriodChange"
								/>
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
								:rows="orders"
								:columns="orderColumns"
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
								<template v-for="col in orderColumns" :key="`header-${col.key}`" #[`header-${col.key}`]="{ column }">
									<span v-if="column.sortable" class="d-flex align-items-center sortable-header" @click="handleSort(column.key)">
										{{ column.label }}
										<i
											:class="[
												'fa ms-1',
												orderBy === column.key
													? orderDir === SORT_DIR_ASC
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
								</template>

								<template #cell-email="{ row }">
									<a :href="`mailto:${row.email}`">{{ row.email }}</a>
								</template>

								<template #cell-phone="{ row }">
									<a :href="`tel:${normalizePhone(row.phone)}`">{{ formatPhone(row.phone) }}</a>
								</template>

								<template #cell-order_number="{ row }">
									{{ row.id }}
								</template>

								<template #cell-date_created="{ row }">
									{{ formatDate(row.created_at) }}
								</template>

								<template #cell-amount="{ row }">
									{{ formatAmountDisplay(row.amount) }}
								</template>

								<template #cell-status="{ row }">
									<label :class="['badge', getStatusBadgeClass(row.status)]">
										{{ row.status || 'Unknown' }}
									</label>
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
							</UITable>
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
			@close="closeHistoryModal"
		/>
		<EditOrderDetailsModal
			:show="showEditModal"
			:order="orderDetails || selectedOrder"
			:loading="isUpdatingOrder"
			@close="closeEditModal"
			@save="handleSaveOrder"
		/>
		<ViewOrderFilesModal
			:show="showFilesModal"
			:files="orderFiles"
			@close="closeFilesModal"
		/>
	</div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import NavbarHeader from '@/components/NavbarHeader.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import UITable from '@/components/ui/UITable.vue'
import PeriodFilter from '@/components/filters/PeriodFilter.vue'
import ViewOrderDetailsModal from '@/components/modals/ViewOrderDetailsModal.vue'
import ViewOrderHistoryModal from '@/components/modals/ViewOrderHistoryModal.vue'
import EditOrderDetailsModal from '@/components/modals/EditOrderDetailsModal.vue'
import ViewOrderFilesModal from '@/components/modals/ViewOrderFilesModal.vue'
import { fetchPaidOrders, getOrderDetails, getOrderHistory, getOrderFiles, updateOrder } from '@/api/adminOrders'
import {
	DEFAULT_PER_PAGE,
	DEFAULT_PAGE,
	DEFAULT_PERIOD,
	DEFAULT_SORT_DIR,
	SORT_DIR_ASC,
	SORT_DIR_DESC,
	PAGE_SIZE_OPTIONS,
} from '@/config/orders'
import { getStatusBadgeClass } from '@/utils/orderStatus'
import { formatName, formatPhone, normalizePhone } from '@/utils/format'
import { useToast } from '@/composables/useToast'

const orderColumns = [
	{ key: 'name', label: 'Name', sortable: true },
	{ key: 'email', label: 'Email', sortable: true },
	{ key: 'phone', label: 'Phone', sortable: true },
	{ key: 'order_number', label: 'Order Number', sortable: true },
	{ key: 'date_created', label: 'Date Created', sortable: true },
	{ key: 'amount', label: 'Amount', sortable: true },
	{ key: 'status', label: 'Status', sortable: true },
	{ key: 'actions', label: 'Action', sortable: false },
]

const orders = ref([])
const isLoading = ref(false)

const currentPage = ref(DEFAULT_PAGE)
const perPage = ref(DEFAULT_PER_PAGE)
const totalItems = ref(0)
const lastPage = ref(1)

const currentPeriod = ref(DEFAULT_PERIOD)
const fromDate = ref(null)
const toDate = ref(null)

const orderBy = ref(null)
const orderDir = ref(DEFAULT_SORT_DIR)

const handlePeriodChange = ({ period, from, to }) => {
	currentPeriod.value = period
	fromDate.value = from
	toDate.value = to
	currentPage.value = 1
	loadOrders()
}

const handleSort = (columnKey) => {
	if (orderBy.value === columnKey) {
		orderDir.value = orderDir.value === SORT_DIR_ASC ? SORT_DIR_DESC : SORT_DIR_ASC
	} else {
		orderBy.value = columnKey
		orderDir.value = SORT_DIR_ASC
	}
	currentPage.value = 1
	loadOrders()
}

const formatDate = (dateString) => {
	if (!dateString) return ''
	const date = new Date(dateString)
	const month = String(date.getMonth() + 1).padStart(2, '0')
	const day = String(date.getDate()).padStart(2, '0')
	const year = date.getFullYear()
	return `${month}/${day}/${year}`
}

const formatAmountDisplay = (amount) => {
	if (!amount || amount === 'None' || amount === 'none' || amount === null || amount === undefined) {
		return '0.00'
	}

	if (typeof amount === 'string' && amount.startsWith('$')) {
		amount = amount.replace('$', '').trim()
	}

	const numAmount = parseFloat(amount)
	if (isNaN(numAmount)) {
		return '0.00'
	}

	return numAmount.toFixed(2)
}

const handlePageChange = (page) => {
	currentPage.value = page
	loadOrders()
}

const handlePerPageChange = (newPerPage) => {
	perPage.value = newPerPage
	currentPage.value = 1
	loadOrders()
}

const showViewModal = ref(false)
const showEditModal = ref(false)
const showHistoryModal = ref(false)
const showFilesModal = ref(false)
const selectedOrder = ref(null)
const orderDetails = ref(null)
const orderHistory = ref([])
const orderFiles = ref([])
const isLoadingOrderDetails = ref(false)
const isLoadingOrderHistory = ref(false)
const isLoadingOrderFiles = ref(false)
const isUpdatingOrder = ref(false)

const openViewModal = async (row) => {
	selectedOrder.value = row
	isLoadingOrderDetails.value = true
	showViewModal.value = true

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

const toast = useToast()

const loadOrders = async () => {
	isLoading.value = true
	try {
		const params = {
			page: currentPage.value,
			per_page: perPage.value,
			period: currentPeriod.value,
		}

		if (currentPeriod.value === 'custom' && fromDate.value && toDate.value) {
			params.from = fromDate.value
			params.to = toDate.value
		}

		if (orderBy.value) {
			params['order-by'] = orderBy.value
			params['order-dir'] = orderDir.value
		}

		const response = await fetchPaidOrders(params)

		if (response.data) {
			orders.value = response.data
		}

		if (response.meta) {
			currentPage.value = response.meta.current_page
			totalItems.value = response.meta.total
			lastPage.value = response.meta.last_page
			perPage.value = response.meta.per_page
		}
	} catch (error) {
		console.error('Failed to load paid orders:', error)
		toast.error('Failed to load payment history')
	} finally {
		isLoading.value = false
	}
}

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

onMounted(() => {
	loadOrders()
})
</script>
