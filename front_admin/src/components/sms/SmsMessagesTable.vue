<template>
	<div id="SmsNavigateTableManager">
		<UITable
			:show-title="false"
			:show-index="false"
			:show-length-control="true"
			:show-search-control="false"
			:show-pagination="true"
			:show-info="true"
			table-class="table table-striped table-hover"
			:rows="messages"
			:columns="smsColumns"
			:server-pagination="true"
			:current-page="currentPage"
			:total-items="totalItems"
			:per-page="perPage"
			:last-page="lastPage"
			:page-size-options="[10, 20, 50, 100]"
			:sortable="false"
			:loading="loading"
			@page-change="$emit('page-change', $event)"
			@per-page-change="$emit('per-page-change', $event)"
		>
			<template #header-id>
				<span class="text-muted">
					<i class="fa fa-fingerprint"></i>
				</span>
			</template>

			<template #cell-id="{ row }">
				<span class="text-muted">{{ row.id }}</span>
			</template>

			<template #cell-message="{ row }">
				<span class="text-muted">{{ truncateMessage(row.message) }}</span>
			</template>

			<template #cell-status="{ row }">
				{{ row.status?.toUpperCase() || '-' }}
			</template>

			<template #cell-receipts="{ row }">
				<span
					v-for="receipt in row.cache?.receipts || []"
					:key="receipt.id"
					:class="getReceiptBadgeClass(receipt.status)"
					class="badge me-1"
				>
					{{ receipt.status.toUpperCase() }}
				</span>
				<span v-if="!row.cache?.receipts?.length">-</span>
			</template>

			<template #cell-submitted_at="{ row }">
				{{ formatDate(row.submitted_at) }}
			</template>
		</UITable>
	</div>
</template>

<script setup>
import UITable from '@/components/ui/UITable.vue'
import { formatDateTime } from '@/utils/date'
import { getReceiptBadgeClass, truncateMessage } from '@/utils/sms'

const props = defineProps({
	messages: {
		type: Array,
		required: true,
		default: () => []
	},
	loading: {
		type: Boolean,
		default: false
	},
	currentPage: {
		type: Number,
		default: 1
	},
	totalItems: {
		type: Number,
		default: 0
	},
	perPage: {
		type: Number,
		default: 20
	},
	lastPage: {
		type: Number,
		default: 1
	}
})

const emit = defineEmits(['page-change', 'per-page-change'])

const smsColumns = [
	{ key: 'id', label: '', sortable: false },
	{ key: 'to', label: 'To', sortable: false },
	{ key: 'message', label: 'Message', sortable: false },
	{ key: 'mid', label: 'MID', sortable: false },
	{ key: 'status', label: 'Status', sortable: false },
	{ key: 'receipts', label: 'Receipts', sortable: false },
	{ key: 'submitted_at', label: 'Submitted At', sortable: false }
]

const formatDate = (dateString) => {
	if (!dateString) return '-'
	return formatDateTime(dateString)
}
</script>
