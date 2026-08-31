<template>
	<div
		v-if="show"
		class="modal fade show"
		tabindex="-1"
		role="dialog"
		aria-labelledby="viewOrderHistoryLabel"
		:aria-modal="show"
		:style="{ display: show ? 'block' : 'none', paddingRight: '15px' }"
		@click.self="close"
	>
		<div class="modal-dialog" role="document" style="max-width: 800px !important;">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="viewOrderHistoryLabel">View Order History</h5>
					<button
						type="button"
						class="close"
						aria-label="Close"
						@click="close"
					>
						<span aria-hidden="true">×</span>
					</button>
				</div>
				<div class="modal-body">
					<p v-if="orderCreatedLabel" class="text-muted small mb-3">
						<strong>Order created:</strong>
						<span class="text-monospace ms-1">{{ orderCreatedLabel }}</span>
					</p>
					<table class="table table-striped">
						<tbody>
							<tr v-if="!hasHistory">
								<td>No History Records Available</td>
							</tr>
							<tr
								v-else
								v-for="entry in history"
								:key="entry.id || entry.performed_at + entry.message"
							>
								<td style="width: 200px;">
									{{ formatDateTime(entry.performed_at || entry.date) }}
								</td>
								<td style="white-space: normal;">
									{{ entry.message }}
								</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button
						type="button"
						class="btn btn-primary btn-fw"
						@click="close"
					>
						Close
					</button>
				</div>
			</div>
		</div>
	</div>
	<div
		v-if="show"
		class="modal-backdrop fade show"
		@click="close"
	></div>
</template>

<script setup>
import { computed } from 'vue'
import { formatDateMMDDYY, formatTimeOnly } from '@/utils/date'

const props = defineProps({
	show: {
		type: Boolean,
		default: false
	},
	history: {
		type: Array,
		default: () => []
	},
	/** Raw `created_at` / `date_created` from order row (ISO or `Y-m-d H:i:s`). */
	orderCreatedAt: {
		type: String,
		default: ''
	}
})

const emit = defineEmits(['close'])

const close = () => {
	emit('close')
}

const hasHistory = computed(
	() => Array.isArray(props.history) && props.history.length > 0
)

const orderCreatedLabel = computed(() => {
	const raw = props.orderCreatedAt
	if (raw === null || raw === undefined || raw === '') {
		return ''
	}
	const s = String(raw)
	const d = formatDateMMDDYY(s)
	const t = formatTimeOnly(s)
	if (!d && !t) {
		return ''
	}
	return t ? `${d} · ${t}` : d
})

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
</script>

<style scoped>
.modal-backdrop {
	position: fixed;
	top: 0;
	left: 0;
	z-index: 1040;
	width: 100vw;
	height: 100vh;
	background-color: #000;
	opacity: 0.5;
}

.modal.show {
	display: block;
}
</style>


