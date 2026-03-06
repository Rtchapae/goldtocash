<template>
	<div>
		<NavbarHeader />
		<div class="dashboard-main-body">
			<PageHeader title="Expenses" :loading="isLoading" />

			<div class="row">
				<div id="admin-main-tables" class="col-lg-12 grid-margin stretch-card">
					<div class="card">
						<div class="card-body orders-card-body">
							<div class="d-flex justify-content-between mb-3">
								<div class="col-sm-12 d-flex justify-content-end">
									<button
										type="button"
										class="btn btn-primary"
										@click="handleExportCsv"
										:disabled="isExporting"
									>
										{{ isExporting ? 'Exporting...' : 'Save to CSV' }}
									</button>
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
								table-class="table table-striped table-hover orders-table"
								:rows="expenses"
								:columns="expenseColumns"
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
								<template v-for="col in expenseColumns" :key="`header-${col.key}`" #[`header-${col.key}`]="{ column }">
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

								<template #cell-order_id="{ row }">
									{{ row.order_id || '-' }}
								</template>

								<template #cell-user_id="{ row }">
									{{ row.user_id }}
								</template>

								<template #cell-name="{ row }">
									{{ row.name || '-' }}
								</template>

								<template #cell-status="{ row }">
									<span :class="['badge', row.status_badge_class || 'bg-secondary', 'text-white']">
										{{ row.status || 'No Order' }}
									</span>
								</template>

								<template #cell-amount="{ row }">
									{{ row.amount ? '$' + row.amount : 'None' }}
								</template>

								<template #cell-shipping="{ row }">
									{{ row.shipping ? '$' + row.shipping : 'None' }}
								</template>

								<template #cell-total="{ row }">
									<b>{{ row.total ? '$' + row.total : 'None' }}</b>
								</template>

								<template #cell-order_created_at="{ row }">
									{{ row.order_created_at || '-' }}
								</template>

								<template #cell-order_updated_at="{ row }">
									{{ row.order_updated_at || '-' }}
								</template>

								<template #cell-actions="{ row }">
									<div class="modal-order-data">
										<a
											v-if="row.order_id"
											href="#"
											@click.prevent="openEditShippingModal(row)"
											title="Edit"
											class="me-0"
										>
											<i class="fas fa-cog"></i>
										</a>
									</div>
								</template>
							</UITable>
						</div>
					</div>
				</div>
			</div>
		</div>

		<EditOrderShippingDetailsModal
			v-if="selectedExpense"
			:show="showEditShippingModal"
			:order="selectedExpense"
			:loading="isUpdatingShipping"
			@close="closeEditShippingModal"
			@save="handleSaveShipping"
		/>
	</div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import NavbarHeader from '@/components/NavbarHeader.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import UITable from '@/components/ui/UITable.vue'
import EditOrderShippingDetailsModal from '@/components/modals/EditOrderShippingDetailsModal.vue'
import { fetchExpenses, exportExpensesToCsv } from '@/api/expenses'
import { updateOrderShipping } from '@/api/adminOrders'
import { PAGE_SIZE_OPTIONS, DEFAULT_PER_PAGE, DEFAULT_PAGE, DEFAULT_SORT_DIR } from '@/config/orders'
import { useToast } from '@/composables/useToast'

const toast = useToast()

const expenses = ref([])
const isLoading = ref(false)
const isExporting = ref(false)
const showEditShippingModal = ref(false)
const isUpdatingShipping = ref(false)
const selectedExpense = ref(null)

const currentPage = ref(DEFAULT_PAGE)
const perPage = ref(DEFAULT_PER_PAGE)
const totalItems = ref(0)
const lastPage = ref(1)
	
const orderBy = ref(null)
const orderDir = ref(DEFAULT_SORT_DIR)

const expenseColumns = [
	{ key: 'order_id', label: 'Order #', sortable: true },
	{ key: 'user_id', label: 'User #', sortable: true },
	{ key: 'name', label: 'Name', sortable: true },
	{ key: 'status', label: 'Status', sortable: true },
	{ key: 'amount', label: 'Amount', sortable: true },
	{ key: 'shipping', label: 'Shipping', sortable: true },
	{ key: 'total', label: 'Total', sortable: false },
	{ key: 'order_created_at', label: 'Order Created', sortable: true },
	{ key: 'order_updated_at', label: 'Order Updated', sortable: true },
	{ key: 'actions', label: 'Action', sortable: false }
]

const loadExpenses = async () => {
	isLoading.value = true
	try {
		const params = {
			page: currentPage.value,
			per_page: perPage.value,
		}

		if (orderBy.value) {
			params['order-by'] = orderBy.value
			params['order-dir'] = orderDir.value
		}

		const response = await fetchExpenses(params)

		if (response.data) {
			expenses.value = response.data
		}

		if (response.meta) {
			currentPage.value = response.meta.current_page
			totalItems.value = response.meta.total
			lastPage.value = response.meta.last_page
			perPage.value = response.meta.per_page
		}
	} catch (error) {
		expenses.value = []
		const errorMessage = error.status === 401
			? 'Unauthorized. Please login again.'
			: error.status === 500
			? 'Server error. Please try again later.'
			: 'Failed to load expenses'
		toast.error(errorMessage)
	} finally {
		isLoading.value = false
	}
}

const handleSort = (column) => {
	if (orderBy.value === column) {
		orderDir.value = orderDir.value === 'asc' ? 'desc' : 'asc'
	} else {
		orderBy.value = column
		orderDir.value = 'asc'
	}
	loadExpenses()
}

const handlePageChange = (page) => {
	currentPage.value = page
	loadExpenses()
}

const handlePerPageChange = (newPerPage) => {
	perPage.value = newPerPage
	currentPage.value = 1
	loadExpenses()
}

const handleExportCsv = async () => {
	isExporting.value = true
	try {
		await exportExpensesToCsv()
		toast.success('Expenses exported successfully')
	} catch (error) {
		const errorMessage = error.status === 401
			? 'Unauthorized. Please login again.'
			: error.status === 500
			? 'Server error. Please try again later.'
			: 'Failed to export expenses'
		toast.error(errorMessage)
	} finally {
		isExporting.value = false
	}
}

const openEditShippingModal = (expense) => {
	if (!expense.order_id) {
		toast.error('No order found for this expense')
		return
	}
	
	selectedExpense.value = {
		id: expense.order_id,
		shipping: expense.shipping ? parseFloat(expense.shipping.replace('$', '')) : 0
	}
	showEditShippingModal.value = true
}

const closeEditShippingModal = () => {
	showEditShippingModal.value = false
	selectedExpense.value = null
}

const handleSaveShipping = async (formData) => {
	if (!selectedExpense.value) return
	
	isUpdatingShipping.value = true
	try {
		await updateOrderShipping(selectedExpense.value.id, formData)
		await loadExpenses()
		closeEditShippingModal()
		toast.success('Shipping details updated successfully')
	} catch (error) {
		console.error('Failed to update shipping:', error)
		toast.error('Failed to update shipping. Please try again.')
	} finally {
		isUpdatingShipping.value = false
	}
}

onMounted(() => {
	loadExpenses()
})
</script>
